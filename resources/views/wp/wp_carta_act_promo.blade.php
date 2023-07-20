@extends("layouts.app")


@section("wrapper")
<div class="page-wrapper" align="center">
<div class="page-content">
    <div class="container box w-50" >
    <h1>Producto</h1>
    <div class="form-group">
        <form action="/wp_promo_individual" method="post">
        @csrf
        <input type="text" name="clavect"  id="clave_ct" class="form-control" placeholder="Clave CT"><br/>
        <button type="submit">Buscar</button>
        </form></div><br>
        @if(isset($data['productos']))
        @foreach ($data['productos'] as $producto)
            <div class="card">
            {{-- <img src="http://grupoehs.com/img/producto/{{$producto->clave_ct}}.jpg" class="card-img-top" alt="..."> --}}
            <div class="card-header">
                <h4 class="card-title">Clave CT</h4>{{$data['productos'][0]['clave_ct']}}
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Nombre: </strong>{{$data['productos'][0]['nombre']}}</li>
                <li class="list-group-item"><strong>Descuento: </strong>{{$data['productos'][0]['descuento']}}</li>
                <li class="list-group-item"><strong>Fecha Inicio WP: </strong>{{$data['productos'][0]['fecha_iwp']}} | <strong> Fecha Inicio CT: </strong>{{$data['productos'][0]['fecha_inicio']}}</li>
                <li class="list-group-item"><strong>Fecha Fin WP: </strong>{{$data['productos'][0]['fecha_fwp']}} | <strong> Fecha Fin CT: </strong>{{$data['productos'][0]['fecha_fin']}}</li>
                <li class="list-group-item"><strong>Precio Rebajado CT: </strong>{{$data['productos'][0]['precio_venta']}} | <strong>Precio CT: </strong>{{$data['productos'][0]['precio_unitario']}}</li>
                <li class="list-group-item"><strong>Precio Rebajado WP: </strong>{{$data['productos'][0]['precio_desc_wp']}} | <strong>Precio WP: </strong>{{$data['productos'][0]['precio_wp']}}</li>
                <li class="list-group-item"><strong>Precio Rebajado Sugerido: </strong>{{$data['productos'][0]['precio_rebajado']}} | <strong>Precio Sugerido: </strong>{{$data['productos'][0]['precio_normal']}}</li>
            </ul>
            </div>
        <form action="/wp_act_promo" method="post">
            @csrf
            <input type="hidden" name="fecha_inicio" value="{{$data['productos'][0]['fecha_inicio']}}">
            <input type="hidden" name="fecha_fin" value="{{$data['productos'][0]['fecha_fin']}}">
            <input type="hidden" name="precio_oferta" value="{{$data['productos'][0]['precio_rebajado']}}">
            <input type="hidden" name="clave" value="{{$data['productos'][0]['clave_ct']}}">
            <input type="hidden" name="idWP" value="{{$data['productos'][0]['idWP']}}">
            <button class="btn btn-primary square" type="submit">Actualizar</button>
        </form></div><br>
        @endforeach
        @endif
    </div>
</div>
</div>
@endsection