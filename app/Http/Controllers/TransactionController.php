<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Transaction;

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
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->latest()
            ->paginate(5)
            ->withQueryString();

            $transactionCount = Transaction::where('status', 'pending')->count();
            $transactionCount = Transaction::where('status', 'cancelled')->count();
            $transactionCount = Transaction::where('status', 'completed')->count();

            return view('transaction.index', compact('Transactions','transactionCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $transactions = Transaction::orderBy('id')->get();

        return view('transaction.create', compact('transactions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'transaction_no' => 'required',
            'date' => 'required',
            'customer_name' => 'required',
            'total_amount' => 'required',
            'status' => 'required',
        ]);

        Transaction::create($request->all());

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       $transaction = Transaction::findOrFail($id);
       $transaction = Transaction::all();

       return view('transaction.edit', compact('transaction', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'transaction_id' => [
                'required',
                Rule::unique('transactions')->where(function ($query) use ($request, $id) {
                    return $query->where('transaction_id', $request->transaction_id)
                                 ->where('id', '!=', $id);
                })->ignore($id),
            ],
            'date' => 'required',
            'customer_name' => 'required',
            'total_amount' => 'required',
            'status' => 'required',
        ], [
            'transaction_id.unique' => 'The transaction ID has already been taken.',
        ]);

        $transaction->update($request->only('transaction_id', 'date', 'customer_name', 'total_amount', 'status'));

        return redirect('/transactions')->with('success', 'Transaction berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return redirect('/transactions')->with('success', 'Transaction berhasil dihapus.');
    }
}
