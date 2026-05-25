<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $categories = Categories::when($search, function ($query) use ($search) {
                $query->where('category_name', 'like', '%' . $search . '%');
            })
            ->paginate(5);

        return view('categories.index', compact('categories', 'search'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|unique:categories,category_name',
            'description' => 'nullable',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique' => 'Kategori sudah ada.',
        ]);

        Categories::create([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil disimpan.');
    }

    public function edit(string $id)
    {
        $category = Categories::findOrFail($id);

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'category_name' => [
                'required',
                Rule::unique('categories', 'category_name')->ignore($id),
            ],
            'description' => 'nullable',
        ], [
            'category_name.required' => 'Nama kategori wajib diisi.',
            'category_name.unique' => 'Kategori sudah ada.',
        ]);

        $category = Categories::findOrFail($id);
        $category->update([
            'category_name' => $request->category_name,
            'description' => $request->description,
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diupdate.');
    }

    public function destroy(string $id)
    {
        $category = Categories::findOrFail($id);

        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih memiliki barang.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
