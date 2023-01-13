@extends("layouts.app")

@section("wrapper")
<div class="page-wrapper" align="center">
    <div class="page-content">
        <div class="container">
            <h1>Margenes</h1>
            <div class="row" height="100">
                @if($data['productos'])
                    @foreach ($data['productos'] as $producto)
                        <div class="col-xs-12 col-sm-4 col-md-3 col-lg-3">
                            <div class="card">
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
                        </div>
                    @endforeach
                    <div>

                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
