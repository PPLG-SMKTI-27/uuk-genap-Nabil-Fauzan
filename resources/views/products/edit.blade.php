<!DOCTYPE html>
<html>
<head>
   <title>Edit Produk</title>
</head>
<body>

<h2>Edit Produk</h2>

<a href="/products">Kembali</a>

<br><br>

@if($errors->any())
   <ul style="color: red">
       @foreach($errors->all() as $error)
           <li>{{ $error }}</li>
       @endforeach
   </ul>
@endif

<form action="/products/{{ $product->id }}" method="POST">
   @csrf
   @method('PUT')

   <label>Nama Produk</label><br>
   <input type="text" name="name" value="{{ $product->name }}">

   <br><br>

   <label>Kategori</label><br>
   <select name="category_id">
       @foreach($categories as $category)
           <option value="{{ $category->id }}"
               {{ $product->category_id == $category->id ? 'selected' : '' }}>
               {{ $category->name }}
           </option>
       @endforeach
   </select>

   <br><br>

   <label>Tanggal</label><br>
   <input type="date" name="date" value="{{ $product->date }}">

   <br><br>

   <label>Kategori</label><br>
   <select name="category_id">
       @foreach($categories as $category)
           <option value="{{ $category->id }}"
               {{ $product->category_id == $category->id ? 'selected' : '' }}>
               {{ $category->name }}
           </option>
       @endforeach
   </select>

   <br><br>

   <button type="submit">Update</button>
</form>

</body>
</html>
