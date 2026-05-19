<!DOCTYPE html>
<html>
<head>
   <title>Data Absensi</title>

    <style>
         .hadir {
              background-color: #d4edda;
         }
    
         .absen {
              background-color: #f8d7da;
         }
    
         .izin {
              background-color: #fff3cd;
         }
    </style>
</head>
<body>

<h2>Data Transaksi</h2>

<p>Jumlah Transaksi: {{ $transactionCount }}</p>

<a href="/transactions/create">Tambah Transaksi</a>

<br><br>

<form method="GET" action="/transactions">
   <input
       type="text"
       name="search"
       placeholder="Cari nama produk"
       value="{{ $search ?? '' }}"
   >

   <button type="submit">Search</button>
</form>

<br>

@if(session('success'))
   <p style="color: green">
       {{ session('success') }}
   </p>
@endif

<table border="1" cellpadding="10" cellspacing="0">

   <tr>
       <th>No</th>
       <th>Nama Produk</th>
       <th>Jumlah</th>
       <th>Tanggal</th>
       <th>Status</th>
   </tr>

   @forelse($Transactions as $transaction)

       @php
           $rowClass = '';

           if($transaction->status == 'pending') {
               $rowClass = 'pending';
           } elseif($transaction->status == 'cancelled') {
               $rowClass = 'cancelled';
           } elseif($transaction->status == 'completed') {
               $rowClass = 'completed';
           }
       @endphp

       <tr class="{{ $rowClass }}">

           <td>{{ $transaction->customer_name }}</td>

           <td>{{ $transaction->quantity }}</td>

           <td>{{ $transaction->date }}</td>

           <td>
               <strong>
                   {{ ucfirst($transaction->status) }}
               </strong>
           </td>

           <td>

               <a href="/transactions/{{ $transaction->id }}/edit">
                   Edit
               </a>

               <form
                   action="/transactions/{{ $transaction->id }}"
                   method="POST"
                   style="display:inline"
               >
                   @csrf
                   @method('DELETE')

                   <button
                       type="submit"
                       onclick="return confirm('Yakin hapus data?')"
                   >
                       Hapus
                   </button>

               </form>

           </td>

       </tr>

   @empty

       <tr>
           <td colspan="5">
               Data transaksi belum ada.
           </td>
       </tr>

   @endforelse

</table>

<br>

{{ $Transactions->links() }}

</body>
</html>