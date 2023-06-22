@extends("layouts.app")


@section("wrapper")
<div class="page-wrapper" align="center">
  <div class="page-content">
    <div class="container box w-50" >
      <h1>Producto</h1>
      <div class="form-group">
        <form action="/wp_met_precios" method="post">
          @csrf
          <input type="text" name="clavect"  id="country_name" class="form-control" placeholder="Clave CT"><br/>
          <button type="submit">Buscar</button>
        </form></div><br>
        @if(isset($data['productos']))
          @foreach ($data['productos'] as $producto)
            <div class="card">
              {{-- <img src="http://grupoehs.com/img/producto/{{$producto->clave_ct}}.jpg" class="card-img-top" alt="..."> --}}
              <div class="card-header">
                <h4 class="card-title">Clave CT</h4>{{$producto->clave_ct}}
              </div>
              <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nombre: </strong>{{$producto->nombre}}</li>
                <li class="list-group-item"><strong>Precio CT: </strong>{{$producto->precio_unitario}}</li>
                <li class="list-group-item"><strong>Precio WP: </strong>{{$producto->precio_venta}}</li>
                @php
                    if(isset($producto->margen)){
						if(isset($producto->descuento)){
							$precio_rebajado = round(((($producto->precio_unitario)*(($producto->margen)+1))*((100-($producto->descuento))/100)),2);
							$precio_normal= round((($producto->precio_unitario)*(($producto->margen)+1)),2);
						}else{
							$precio_rebajado = '';
							$precio_normal= round((($producto->precio_unitario)*(($producto->margen)+1)),2);
						}
					}else{
						if(isset($producto->descuento)){
							$precio_rebajado = round((($producto->precio_unitario)*(1.11)*((100-($producto->descuento))/100)),2);
							$precio_normal= round((($producto->precio_unitario)*(1.11)),2);
						}else{
							$precio_rebajado = '';
							$precio_normal= round((($producto->precio_unitario)*(1.11)),2);
						}
					}
                @endphp
                <li class="list-group-item"><strong>Precio Sugerido: </strong>{{$precio_normal}}</li>
                {{-- <li class="list-group-item"><strong>Enlace: </strong><a href="{{$producto->enlace}}">{{$producto->enlace}}</a></li>
                <li class="list-group-item"><strong>Existencias:</strong>{{$producto->existencias}}</li> --}}
              </ul>
            </div>
          @endforeach
          <form action="/wp_act_precios" method="post">
            @csrf
            <button type="submit">Actualizar</button>
          </form></div><br>
        @endif
    </div>
  </div>
</div>
@endsection