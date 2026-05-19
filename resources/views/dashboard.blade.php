<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div>
        <h2>Form Transaksi</h2>

        <a href="/transactions">Kembali</a>

        <br><br>

        @if($errors->any())
        <ul style="color: red">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        @endif

        <form action="/transactions/store" method="POST">
        @csrf

        <label>Nama Produk</label><br>
        <select name="product_id">
            <option value="">-- Pilih Produk --</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}">
                    {{ $product->name }} - {{ $product->category->name }}
                </option>
            @endforeach
        </select>

        <br><br>

        <label>Tanggal</label><br>
        <input type="date" name="date">

        <br><br>

        <label>Status</label><br>
        <select name="status">
            <option value="">-- Pilih Status --</option>
            <option value="pending">Pending</option>
            <option value="cancelled">Cancelled</option>
            <option value="completed">Completed</option>
        </select>

        <br><br>

        <button type="submit">Simpan</button>
        </form>
    </div>
</x-app-layout>
