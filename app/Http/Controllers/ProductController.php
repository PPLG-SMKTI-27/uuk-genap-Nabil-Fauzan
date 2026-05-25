<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categories;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
    {
        $search = $request->search;

        $products = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });

            $pendingCount = Transaction::where('status', 'pending')->count();
            $cancelledCount = Transaction::where('status', 'cancelled')->count();
            $completedCount = Transaction::where('status', 'completed')->count();
            $productCount = Product::count();

            return view('products.index', compact('products','pendingCount','cancelledCount','completedCount','productCount', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Categories::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|unique:products,product_name',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ], [
            'product_name.required' => 'Nama produk wajib diisi.',
            'product_name.unique' => 'Produk sudah ada.',

            'description.required' => 'Deskripsi wajib diisi.',

            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh minus.',

            'stock.required' => 'Stock wajib diisi.',
            'stock.integer' => 'Stock harus berupa angka bulat.',
            'stock.min' => 'Stock tidak boleh minus.',

            'unit.required' => 'Unit wajib diisi.',
            'unit.integer' => 'Unit harus berupa angka bulat.',
            'unit.min' => 'Unit tidak boleh minus.',

            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
        ]);

        Product::create([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category')->findOrFail($id);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $categories = Categories::all();

        $product = Product::findOrFail($id);

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_name' => [
                'required',
                Rule::unique('products', 'product_name')->ignore($id),
            ],
            'description' => 'required',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
        ], [
            'product_name.required' => 'Nama produk wajib diisi.',
            'product_name.unique' => 'Produk sudah ada.',
            'description.required' => 'Deskripsi wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'price.min' => 'Harga tidak boleh minus.',
            'stock.required' => 'Stock wajib diisi.',
            'stock.integer' => 'Stock harus berupa angka bulat.',
            'stock.min' => 'Stock tidak boleh minus.',
            'unit.required' => 'Unit wajib diisi.',
            'unit.integer' => 'Unit harus berupa angka bulat.',
            'unit.min' => 'Unit tidak boleh minus.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori tidak valid.',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'product_name' => $request->product_name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'unit' => $request->unit,
            'category_id' => $request->category_id,
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       $product = Product::findOrFail($id);
       $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }
}
