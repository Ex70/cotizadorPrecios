@extends("layouts.app")


@section("wrapper")
<div class="page-wrapper" align="center">
  <div class="page-content">
    <div class="container box w-50" >
      <h1>Productos</h1>
      <div class="form-group">
        <form action="/productos/cartas" method="post">
          @csrf
          <input type="text" name="clavect"  id="country_name" class="form-control" placeholder="Clave CT"><br/>
          <button type="submit">Buscar</button>
        </form></div><br>
        @if($data['productos'])
          @foreach ($data['productos'] as $producto)
            <div class="card">
              <img src="http://grupoehs.com/img/productos/{{$producto->clave_ct}}.jpg" class="card-img-top" alt="...">
              <div class="card-header">
                <h4 class="card-title">Clave CT</h4>{{$producto->clave_ct}}
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nombre: </strong>{{$producto->nombre}}</li>
                <li class="list-group-item"><strong>Categoria: </strong>{{$producto->categoria}}</li>
                <li class="list-group-item"><strong>Subcategoria: </strong>{{$producto->subcategoria}}</li>
                <li class="list-group-item"><strong>Marca: </strong>{{$producto->marca}}</li>
                <li class="list-group-item"><strong>Enlace: </strong><a href="{{$producto->enlace}}">{{$producto->enlace}}</a></li>
                <li class="list-group-item"><strong>Existencias:</strong>{{$producto->existencias}}</li>
              </ul>
            </div>
          @endforeach
        @endif
    </div>
  </div>
</div>
@endsection
