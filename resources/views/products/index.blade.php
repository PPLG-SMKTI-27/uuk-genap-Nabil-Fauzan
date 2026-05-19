<!DOCTYPE html>
<html>
<head>
   <title>Data Product</title>
    <style>
        .Supercar {
            background-color: #fff3cd;
        }

        .Hypercar {
            background-color: #f8d7da;
        }

        .SUV {
            background-color: #d4edda;
        }    
</style>
</head>
<body>

<h2>Data Product</h2>

<p>Jumlah Product: {{ $products->count()}}</p>

<a href="/products/create">Tambah Product</a>

<br><br>

<form method="GET" action="/products">
   <input
       type="text"
       name="search"
       placeholder="Cari nama product"
       value="{{ $search }}"
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
    <th>Nama Product</th>
    <th>Category</th>
    <th>Date</th>
    <th>Status</th>
    <th>Aksi</th>
   </tr>

   @forelse($products as $product)

       <tr>
              <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                <td>{{ $product->date }}</td>
                <td>{{ $product->status }}</td>

           <td>

               <a href="/products/{{ $product->id }}/edit">
                   Edit
               </a>

               <form
                   action="/products/{{ $product->id }}"
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
               Data product belum ada.
           </td>
       </tr>

   @endforelse

</table>

<br>

{{ $products->links() }}

</body>
</html>