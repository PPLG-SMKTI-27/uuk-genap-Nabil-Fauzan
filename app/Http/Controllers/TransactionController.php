<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionDetail;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $Transactions = Transaction::with('transactionDetails.product.category')
            ->when($search, function ($query) use ($search) {
                $query->whereHas('transactionDetails.product', function ($q) use ($search) {
                    $q->where('product_name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

        $transactionCount = Transaction::count();

        return view('transaction.index', compact('Transactions', 'transactionCount', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();

        return view('transaction.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'customer_name' => 'nullable',
            'quantity' => 'nullable|integer|min:1',
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk tidak valid.',
            'date.required' => 'Tanggal wajib diisi.',
            'status.required' => 'Status wajib diisi.',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);
        $total_amount = $product->price * $quantity;

        if ($product->stock < $quantity) {
            return redirect()->back()
                ->withErrors(['quantity' => 'Stok produk tidak mencukupi.'])
                ->withInput();
        }

        // Deduct product stock
        $product->decrement('stock', $quantity);

        $transaction = Transaction::create([
            'transaction_no' => mt_rand(100000, 999999),
            'date' => $request->date,
            'customer_name' => $request->input('customer_name') ?: 'Umum',
            'total_amount' => $total_amount,
            'status' => $request->status,
        ]);

        TransactionDetail::create([
            'transaction_id' => $transaction->id,
            'product_id' => $request->product_id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
            'subtotal' => $total_amount,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $transaction = Transaction::with('transactionDetails')->findOrFail($id);
        $products = Product::all();

        return view('transaction.edit', compact('transaction', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'date' => 'required|date',
            'status' => 'required|in:pending,completed,cancelled',
            'customer_name' => 'nullable',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $transaction = Transaction::findOrFail($id);
        $detail = $transaction->transactionDetails()->first();

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->input('quantity', 1);
        $total_amount = $product->price * $quantity;

        // Restore stock first
        if ($detail) {
            $oldProduct = Product::find($detail->product_id);
            if ($oldProduct) {
                $oldProduct->increment('stock', $detail->quantity);
            }
        }

        // Check if new stock is sufficient
        if ($product->stock < $quantity) {
            // rollback restoration if failed
            if ($detail && isset($oldProduct) && $oldProduct) {
                $oldProduct->decrement('stock', $detail->quantity);
            }
            return redirect()->back()
                ->withErrors(['quantity' => 'Stok produk tidak mencukupi.'])
                ->withInput();
        }

        // Deduct new stock
        $product->decrement('stock', $quantity);

        $transaction->update([
            'date' => $request->date,
            'customer_name' => $request->input('customer_name') ?: 'Umum',
            'total_amount' => $total_amount,
            'status' => $request->status,
        ]);

        if ($detail) {
            $detail->update([
                'product_id' => $request->product_id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $total_amount,
            ]);
        } else {
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $request->product_id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'subtotal' => $total_amount,
            ]);
        }

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::with('transactionDetails')->findOrFail($id);

        // Restore stock
        foreach ($transaction->transactionDetails as $detail) {
            $product = Product::find($detail->product_id);
            if ($product) {
                $product->increment('stock', $detail->quantity);
            }
        }

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
