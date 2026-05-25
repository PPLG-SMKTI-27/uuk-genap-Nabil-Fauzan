<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Statistics Cards (Task 6) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card: Jumlah Barang -->
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-xl overflow-hidden text-white p-6 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-indigo-100 font-semibold">Jumlah Barang</p>
                            <h3 class="text-3xl font-extrabold mt-2">{{ $productCount }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-indigo-100 flex items-center">
                        <a href="{{ route('products.index') }}" class="underline hover:text-white">Lihat Daftar Barang &rarr;</a>
                    </div>
                </div>

                <!-- Card: Jumlah Kategori -->
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-xl overflow-hidden text-white p-6 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-emerald-100 font-semibold">Jumlah Kategori</p>
                            <h3 class="text-3xl font-extrabold mt-2">{{ $categoryCount }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-emerald-100 flex items-center">
                        <a href="{{ route('categories.index') }}" class="underline hover:text-white">Kelola Kategori &rarr;</a>
                    </div>
                </div>

                <!-- Card: Total Stok -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl shadow-xl overflow-hidden text-white p-6 transform hover:scale-[1.02] transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-wider text-amber-100 font-semibold">Total Stok Barang</p>
                            <h3 class="text-3xl font-extrabold mt-2">{{ $totalStock }}</h3>
                        </div>
                        <div class="p-3 bg-white/20 rounded-full">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-sm text-amber-100">
                        Akumulasi seluruh stok barang di gudang.
                    </div>
                </div>
            </div>

            <!-- Transaction Status Summary & Quick Sale Form -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- POS / Transaction Summary -->
                <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-150">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Ringkasan Transaksi</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-xl">
                            <span class="text-yellow-800 font-medium">Pending</span>
                            <span class="px-3 py-1 bg-yellow-200 text-yellow-800 text-xs font-bold rounded-full">{{ $pendingCount }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-green-50 rounded-xl">
                            <span class="text-green-800 font-medium">Selesai (Completed)</span>
                            <span class="px-3 py-1 bg-green-200 text-green-800 text-xs font-bold rounded-full">{{ $completedCount }}</span>
                        </div>
                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-xl">
                            <span class="text-red-800 font-medium">Dibatalkan</span>
                            <span class="px-3 py-1 bg-red-200 text-red-800 text-xs font-bold rounded-full">{{ $cancelledCount }}</span>
                        </div>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('transactions.index') }}" class="block text-center w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition duration-150">
                            Lihat Semua Transaksi
                        </a>
                    </div>
                </div>

                <!-- Quick Transaction Form -->
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-150">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Form Transaksi Baru</h3>

                    <form action="{{ route('transactions.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Customer Name -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Pelanggan</label>
                                <input type="text" name="customer_name" placeholder="Contoh: Budi (Kosongkan untuk 'Umum')" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <!-- Product Selection -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Produk</label>
                                <select name="product_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">
                                            {{ $product->product_name }} (Stok: {{ $product->stock }}) - Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Jumlah</label>
                                <input type="number" name="quantity" min="1" value="1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Transaction Date -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal</label>
                                <input type="date" name="date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>
                        </div>

                        <!-- Status Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Status Transaksi</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end pt-4">
                            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 transition duration-150">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
