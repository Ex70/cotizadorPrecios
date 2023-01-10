@extends("layouts.app")


@section("wrapper")
<div class="page-wrapper" align="center">
    <div class="page-content">
        <div class="container box">
            <h1>Margenes</h1>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        @if($data['productos'])
                            @foreach ($data['productos'] as $producto)
                                <div class="card col-12" style="width: 15rem;">
                                    <img src="http://grupoehs.com/img/productos/{{$producto->clave_ct}}.jpg" class="card-img-top" alt="...">
                                    <div class="card-header">
                                        <h4 class="card-title">Clave CT</h4>{{$producto->clave_ct}}
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Nombre: </strong>{{$producto->nombre}}</li>
                                            <li class="list-group-item"><strong>Categoria: </strong>{{$producto->categoria}}</li>
                                            <li class="list-group-item"><strong>Subcategoria: </strong>{{$producto->subcategoria}}</li>
                                            <li class="list-group-item"><strong>Marca: </strong>{{$producto->marca}}</li>
                                            <li class="list-group-item"><strong>Enlace: </strong><a href="{{$producto->enlace}}">{{$producto->enlace}}</a></li>
                                            <li class="list-group-item"><strong>Existencias: </strong>{{$producto->existencias}}</li>
                                            <li class="list-group-item"><strong>Margen de Utilidad: </strong>{{$producto->margen*100}}%</li>
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
