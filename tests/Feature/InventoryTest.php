<?php

use App\Models\User;
use App\Models\Categories;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;

/*
|--------------------------------------------------------------------------
| Guest Redirects
|--------------------------------------------------------------------------
*/

test('guests are redirected from categories routes', function () {
    $this->get(route('categories.index'))->assertRedirect(route('login'));
    $this->get(route('categories.create'))->assertRedirect(route('login'));
    $this->post(route('categories.store'), [])->assertRedirect(route('login'));
});

test('guests are redirected from products routes', function () {
    $this->get(route('products.index'))->assertRedirect(route('login'));
    $this->get(route('products.create'))->assertRedirect(route('login'));
    $this->post(route('products.store'), [])->assertRedirect(route('login'));
});

test('guests are redirected from transactions routes', function () {
    $this->get(route('transactions.index'))->assertRedirect(route('login'));
    $this->get(route('transactions.create'))->assertRedirect(route('login'));
    $this->post(route('transactions.store'), [])->assertRedirect(route('login'));
});

/*
|--------------------------------------------------------------------------
| Category CRUD
|--------------------------------------------------------------------------
*/

test('authenticated users can render categories list', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get(route('categories.index'));
    $response->assertStatus(200);
});

test('authenticated users can create a category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('categories.store'), [
        'category_name' => 'Snack Makanan',
        'description' => 'Kategori untuk makanan ringan',
    ]);

    $response->assertRedirect(route('categories.index'));
    $this->assertDatabaseHas('categories', [
        'category_name' => 'Snack Makanan',
        'description' => 'Kategori untuk makanan ringan',
    ]);
});

test('category creation requires a name and name must be unique', function () {
    $user = User::factory()->create();
    Categories::create([
        'category_name' => 'Existing Category',
        'description' => 'Test',
    ]);

    // Name is empty
    $response = $this->actingAs($user)->post(route('categories.store'), [
        'category_name' => '',
        'description' => 'Test',
    ]);
    $response->assertSessionHasErrors('category_name');

    // Name is duplicate
    $response2 = $this->actingAs($user)->post(route('categories.store'), [
        'category_name' => 'Existing Category',
        'description' => 'Test 2',
    ]);
    $response2->assertSessionHasErrors('category_name');
});

test('kategori tidak bisa dihapus jika masih memiliki barang', function () {
    $user = User::factory()->create();
    $category = Categories::create([
        'category_name' => 'Bahan Baku',
        'description' => 'Bahan baku mentah',
    ]);

    $product = Product::create([
        'category_id' => $category->id,
        'product_name' => 'Tepung Terigu',
        'description' => 'Tepung terigu protein sedang',
        'price' => 12000,
        'stock' => 10,
        'unit' => 'Kg',
    ]);

    $response = $this->actingAs($user)->delete(route('categories.destroy', $category->id));
    $response->assertRedirect(route('categories.index'));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('categories', ['id' => $category->id]);

    // Now delete the product, then delete the category should succeed
    $product->delete();
    $response2 = $this->actingAs($user)->delete(route('categories.destroy', $category->id));
    $response2->assertRedirect(route('categories.index'));
    $response2->assertSessionHas('success');
    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

/*
|--------------------------------------------------------------------------
| Product CRUD
|--------------------------------------------------------------------------
*/

test('authenticated users can create a product', function () {
    $user = User::factory()->create();
    $category = Categories::create([
        'category_name' => 'Minuman',
        'description' => 'Minuman segar',
    ]);

    $response = $this->actingAs($user)->post(route('products.store'), [
        'product_name' => 'Es Teh Manis',
        'description' => 'Es teh dengan gula asli',
        'price' => 5000,
        'stock' => 50,
        'unit' => 'Gelas',
        'category_id' => $category->id,
    ]);

    $response->assertRedirect(route('products.index'));
    $this->assertDatabaseHas('products', [
        'product_name' => 'Es Teh Manis',
        'price' => 5000,
        'stock' => 50,
        'unit' => 'Gelas',
        'category_id' => $category->id,
    ]);
});

test('product validations enforce non-negative price and stock', function () {
    $user = User::factory()->create();
    $category = Categories::create([
        'category_name' => 'Minuman',
        'description' => 'Minuman segar',
    ]);

    // Negative price
    $response1 = $this->actingAs($user)->post(route('products.store'), [
        'product_name' => 'Es Jeruk',
        'description' => 'Segar',
        'price' => -100,
        'stock' => 20,
        'unit' => 'Gelas',
        'category_id' => $category->id,
    ]);
    $response1->assertSessionHasErrors('price');

    // Negative stock
    $response2 = $this->actingAs($user)->post(route('products.store'), [
        'product_name' => 'Es Kelapa',
        'description' => 'Segar',
        'price' => 10000,
        'stock' => -5,
        'unit' => 'Gelas',
        'category_id' => $category->id,
    ]);
    $response2->assertSessionHasErrors('stock');
});

test('products can be searched by name', function () {
    $user = User::factory()->create();
    $category = Categories::create(['category_name' => 'Kopi']);

    Product::create([
        'product_name' => 'Espresso Hot',
        'description' => 'Strong',
        'price' => 15000,
        'stock' => 20,
        'unit' => 'Cup',
        'category_id' => $category->id,
    ]);

    Product::create([
        'product_name' => 'Americano Ice',
        'description' => 'Mild',
        'price' => 18000,
        'stock' => 15,
        'unit' => 'Cup',
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->get(route('products.index', ['search' => 'Espresso']));
    $response->assertStatus(200);
    $response->assertSee('Espresso Hot');
    $response->assertDontSee('Americano Ice');
});

/*
|--------------------------------------------------------------------------
| Transaction CRUD
|--------------------------------------------------------------------------
*/

test('creating transaction decrements product stock', function () {
    $user = User::factory()->create();
    $category = Categories::create(['category_name' => 'Makanan']);
    $product = Product::create([
        'product_name' => 'Nasi Goreng',
        'description' => 'Lezat',
        'price' => 15000,
        'stock' => 10,
        'unit' => 'Porsi',
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'product_id' => $product->id,
        'date' => '2026-05-25',
        'customer_name' => 'Andi',
        'quantity' => 3,
        'status' => 'completed',
    ]);

    $response->assertRedirect(route('transactions.index'));
    $product->refresh();
    $this->assertEquals(7, $product->stock); // 10 - 3 = 7

    $this->assertDatabaseHas('transactions', [
        'customer_name' => 'Andi',
        'total_amount' => 45000, // 3 * 15000 = 45000
        'status' => 'completed',
    ]);
});

test('cannot create transaction if stock is insufficient', function () {
    $user = User::factory()->create();
    $category = Categories::create(['category_name' => 'Makanan']);
    $product = Product::create([
        'product_name' => 'Mie Goreng',
        'description' => 'Lezat',
        'price' => 12000,
        'stock' => 2,
        'unit' => 'Porsi',
        'category_id' => $category->id,
    ]);

    $response = $this->actingAs($user)->post(route('transactions.store'), [
        'product_id' => $product->id,
        'date' => '2026-05-25',
        'customer_name' => 'Andi',
        'quantity' => 3, // Exceeds stock (2)
        'status' => 'completed',
    ]);

    $response->assertSessionHasErrors('quantity');
    $product->refresh();
    $this->assertEquals(2, $product->stock); // Stock should not change
});

test('updating transaction adjusts product stock correctly', function () {
    $user = User::factory()->create();
    $category = Categories::create(['category_name' => 'Makanan']);
    $product = Product::create([
        'product_name' => 'Ayam Bakar',
        'description' => 'Lezat',
        'price' => 20000,
        'stock' => 10, // Stock starts at 10
        'unit' => 'Porsi',
        'category_id' => $category->id,
    ]);

    // First transaction takes 2 items (leaving 8 stock)
    $response1 = $this->actingAs($user)->post(route('transactions.store'), [
        'product_id' => $product->id,
        'date' => '2026-05-25',
        'customer_name' => 'Budi',
        'quantity' => 2,
        'status' => 'pending',
    ]);
    $product->refresh();
    $this->assertEquals(8, $product->stock);

    $transaction = Transaction::latest()->first();

    // Now update quantity to 5. Budi takes 3 more items.
    // Stock should restore 2 (back to 10), then subtract 5 (stock becomes 5).
    $response2 = $this->actingAs($user)->put(route('transactions.update', $transaction->id), [
        'product_id' => $product->id,
        'date' => '2026-05-25',
        'customer_name' => 'Budi',
        'quantity' => 5,
        'status' => 'completed',
    ]);

    $product->refresh();
    $this->assertEquals(5, $product->stock); // 10 - 5 = 5

    // Total amount should be updated
    $transaction->refresh();
    $this->assertEquals(100000, $transaction->total_amount); // 5 * 20000 = 100000
});

test('deleting transaction restores product stock', function () {
    $user = User::factory()->create();
    $category = Categories::create(['category_name' => 'Makanan']);
    $product = Product::create([
        'product_name' => 'Es Campur',
        'description' => 'Segar',
        'price' => 8000,
        'stock' => 10,
        'unit' => 'Mangkok',
        'category_id' => $category->id,
    ]);

    // Transaction takes 4 items (leaves 6 stock)
    $response1 = $this->actingAs($user)->post(route('transactions.store'), [
        'product_id' => $product->id,
        'date' => '2026-05-25',
        'customer_name' => 'Caca',
        'quantity' => 4,
        'status' => 'completed',
    ]);
    $product->refresh();
    $this->assertEquals(6, $product->stock);

    $transaction = Transaction::latest()->first();

    // Now delete the transaction, stock should go back to 10
    $response2 = $this->actingAs($user)->delete(route('transactions.destroy', $transaction->id));
    $response2->assertRedirect(route('transactions.index'));

    $product->refresh();
    $this->assertEquals(10, $product->stock); // 6 + 4 = 10
    $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
});
