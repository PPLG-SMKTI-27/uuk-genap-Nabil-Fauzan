<!DOCTYPE html>
<html>
<head>
   <title>Form Produk</title>
</head>
<body>

<h2>Form Produk</h2>

<a href="/products">Kembali</a>

<br><br>

@if($errors->any())
   <ul style="color: red">
       @foreach($errors->all() as $error)
           <li>{{ $error }}</li>
       @endforeach
   </ul>
@endif

<form action="/products/store" method="POST">
   @csrf

   <label>Nama Produk</label><br>
    <input type="text" name="product_name">

   <br><br>

   <label>Deskripsi Produk</label><br>
   <textarea name="description"></textarea>

    <br><br>

    <label>Harga</label><br>
    <input type="number" name="price" step="0.1">

    <br><br>

    <label>Stok</label><br>
    <input type="number" name="stock">

    <br><br>

    <label>Unit</label><br>
    <input type="number" name="unit">

    <br><br>

   <label>Tanggal</label><br>
   <input type="date" name="date">

   <br><br>

   <label>Kategori</label><br>
   <select name="category_id">
       <option value="">-- Pilih Kategori --</option>
       <option value="1">Supercar</option>
       <option value="2">Hypercar</option>
       <option value="3">SUV</option>
   </select>

   <br><br>

   <button type="submit">Simpan</button>
</form>

</body>
</html>