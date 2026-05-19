<!DOCTYPE html>
<html>
<head>
   <title>Edit Transaksi</title>
</head>
<body>

<h2>Edit Transaksi</h2>

<a href="/transactions">Kembali</a>

<br><br>

@if($errors->any())
   <ul style="color: red">
       @foreach($errors->all() as $error)
           <li>{{ $error }}</li>
       @endforeach
   </ul>
@endif

<form action="/transactions/{{ $transaction->id }}" method="POST">
   @csrf
   @method('PUT')

   <label>Nama Produk</label><br>
   <select name="product_id">
       @foreach($products as $product)
           <option value="{{ $product->id }}"
               {{ $transaction->product_id == $product->id ? 'selected' : '' }}>
               {{ $product->name }} - {{ $product->category->name }}
           </option>
       @endforeach
   </select>

   <br><br>

   <label>Tanggal</label><br>
   <input type="date" name="date" value="{{ $transaction->date }}">

   <br><br>

   <label>Status</label><br>
   <select name="status">
       <option value="accepted" {{ $transaction->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
       <option value="pending" {{ $transaction->status == 'pending' ? 'selected' : '' }}>Pending</option>
       <option value="cancelled" {{ $transaction->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
   </select>

   <br><br>

   <button type="submit">Update</button>
</form>

</body>
</html>
