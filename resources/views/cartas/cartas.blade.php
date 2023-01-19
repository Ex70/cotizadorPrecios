@extends("layouts.app")

@section("wrapper")
<div class="page-wrapper" align="center">
    <div class="page-content">
        <div class="container">
            @if (isset($data))
                <h1>Margenes</h1>
            @else
                <h1>Promociones</h1>
                <?php
                    $data = $prom
                ?>
            @endif
            <div class="row">
                <div class="row">
                @if(isset($data))
                    @foreach ($data as $key=>$producto) 
                    <div class="col-xs-12 col-lg-3">
                            <div class="card">
                                <div class="card-header">
                                    <img src="{{route('getfile',$producto['clave_ct'].".jpg")}}" class="card-img-top" alt="...">
                                    <h4 class="card-title">Clave CT</h4>{{$producto['clave_ct']}}
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>Nombre: </strong>{{$producto['nombre']}}</li>
                                        <li class="list-group-item"><strong>Categoria: </strong>{{$producto['categoria']}}</li>
                                        <li class="list-group-item"><strong>Subcategoria: </strong>{{$producto['subcategoria']}}</li>
                                        <li class="list-group-item"><strong>Marca: </strong>{{$producto['marca']}}</li>
                                        <li class="list-group-item"><strong>Enlace: </strong><a href="{{$producto['enlace']}}">{{$producto['enlace']}}</a></li>
                                        <li class="list-group-item"><strong>Existencias: </strong>{{$producto['existencias']}}</li>
                                        @if (isset($prom))
                                            <li class="list-group-item"><strong>Descuento: </strong>{{$producto['descuento']}}%</li>
                                            <li class="list-group-item"><strong>Fecha Fin: </strong>{{$producto['fecha_fin']}}</li>
                                        @else
                                            <li class="list-group-item"><strong>Margen de Utilidad: </strong>{{$producto['margen']*100}}%</li>                                            
                                        @endif                                            
                                    </ul>
                                </div>     
                            </div>
                        </div>
                            @if (($key+1)%4==0 && $key<sizeof($data))
                                </div>
                                <div class="row">
                            @endif
                            
                            @if ($key+1==sizeof($data))
                                </div>
                                hola
                            @endif
                    @endforeach
                @endif
            </div>
        </div>
    </div>
    {!!$data->links() !!}
</div>
@endsection

