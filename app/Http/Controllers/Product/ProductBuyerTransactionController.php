<?php

namespace App\Http\Controllers\Product;

use App\User;
use App\Buyer;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ApiController;
use App\Transformers\TransactionTransformer;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . TransactionTransformer::class)->only(['store']);
        $this->middleware('scope:purchase-product')->only(['store']);
        $this->middleware('can:view,buyer')->only('store');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product, User $buyer)
    {
        if ($buyer->id == $product->seller_id) {
            return $this->errorResponse("The buyer must be different from the seller", 409);
        }

        if (! $product->seller->isVerified()) {
            return $this->errorResponse("The seller must be verified", 409);
        }

        if (! $buyer->isVerified()) {
            return $this->errorResponse("The buyer must be verified", 409);
        }

        if (! $product->isAvailable()) {
            return $this->errorResponse("The product is not available", 409);
        }

        $rules = [
            'quantity' => 'required|integer|min:1|max:' . $product->quantity 
        ];

        $request->validate($rules);

        return DB::transaction(function() use ($request, $product, $buyer) {
            $product->quantity -= $request->quantity;
            $product->save();

            $transaction = Transaction::create([
                'quantity' => $request->quantity,
                'product_id' => $product->id,
                'buyer_id' => $buyer->id
            ]);

            return $this->showOne($transaction);
        });
    }
}
