<?php

use App\User;
use App\Product;
use App\Category;
use App\Transaction;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	* Seed the application's database.
	*
	* @return void
	*/
	public function run()
	{
		$usersQuantity = 50;
		$categoriesQuantity = 50;
		$productsQuantity = 100;
		$transactionsQuantity = 200;

		User::flushEventListeners();
		Category::flushEventListeners();
		Product::flushEventListeners();
		Transaction::flushEventListeners();

		factory(User::class, $usersQuantity)->create();
		$categories = factory(Category::class, $categoriesQuantity)->create();	
		factory(Product::class, $productsQuantity)->create()->each(
			function($product) use ($categories) {
				$randomCategories = $categories->random(rand(1, 5))->pluck('id');
				$product->categories()->attach($randomCategories);
			}
		);	
		factory(Transaction::class, $transactionsQuantity)->create();
		factory(User::class, $usersQuantity)->create();		// not sellers or buyers
	}
}
