<?php

namespace App\Transformers;

use App\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Product $product)
    {
        return [
            'identifier' => (int) $product->id, 
            'title' => (string) $product->name,
            'details' => (string) $product->description,
            'stock' => (int) $product->quantity,
            'situation' => (string) $product->status,
            'picture' => url("images/{$product->image}"),
            'seller' => (int) $product->seller_id,
            'creationDate' => (string) $product->created_at,
            'lastChange' => (string) $product->updated_at,
            'deletionDate' => isset($product->deleted_at) ? (string) $product->deleted_at : null,

            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('products.show', $product)
                ],
                [
                    'rel' => 'product.buyers',
                    'href' => route('products.buyers.index', $product)
                ],
                [
                    'rel' => 'product.categories',
                    'href' => route('products.categories.index', $product)
                ],
                [
                    'rel' => 'product.transactions',
                    'href' => route('products.transactions.index', $product)
                ],
                [
                    'rel' => 'seller',
                    'href' => route('sellers.show', $product->seller_id)
                ],
            ]
        ];
    }

    public static function originalAttribute($index)
    {
        $attributes = [
            'identifier' => 'id',
            'title' => 'name',
            'details' => 'description',
            'stock' => 'quantity',
            'situation' => 'status',
            'picture' => 'image',
            'seller' => 'seller_id',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletionDate' => 'deleted_at'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }

    public static function transformedAttribute($index)
    {
        $attributes = [
            'id' => 'identifier',
            'name' => 'title',
            'description' => 'details',
            'quantity' => 'stock',
            'status' => 'situation',
            'image' => 'picture',
            'seller_id' => 'seller',
            'created_at' => 'creationDate',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletionDate'
        ];

        return isset($attributes[$index]) ? $attributes[$index] : null;
    }
}
