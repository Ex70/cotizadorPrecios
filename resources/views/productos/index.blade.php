{{-- <!DOCTYPE html>
<html>
<head>
    <title>Cotizador de pagos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
</head> --}}
@extends("layouts.app")
@section("style")
    <link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
@endsection

@section("wrapper")
{{-- <body> --}}
    <div class="page-wrapper">
        <div class="page-content">
    <div class="row justify-content-centre" style="margin-top: 4%">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bgsize-primary-4 white card-header">
                    <h4 class="card-title">Consultar productos</h4>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        <br>
                    @endif
                    <form action="{{url("productos")}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <fieldset>
                            <label>Seleccione una categoría y subcategoría para consultar, la marca es opcional</label>
                            <div class="input-group">
                                <select name="filtro1" id="filtro1" class="form-control" required>
                                    <option selected disabled>Todas las categorias</option>
                                    {{-- <option value="x">Todas las categorias</option> --}}
                                    @foreach($data['categorias'] as $row)
                                        <option value="{{$row->id}}">{{$row->nombre}}</option>
                                    @endforeach
                                </select>
                                <select name="filtro2" id="filtro2" class="form-control">
                                    <option selected disabled>Todas las subcategorias</option>
                                    {{-- <option value="y" id="llenar2">Todas las subcategorias</option> --}}
                                </select>
                                <select name="filtro3" id="filtro3" class="form-control">
                                    <option value="z" id="llenar4">Todas las marcas</option>
                                </select>
                                <div class="input-group-append" id="button-addon2">
                                    <button class="btn btn-primary square" type="submit"><i class="ft-upload mr-1"></i>Consultar</button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-left">
        <div class="col-md-12">
            <br />
            <div class="card">
                <div class="card-header bgsize-primary-4 white card-header">
                    <h4 class="card-title">Precios</h4>
                </div>
                <div class="card-body">
                    <div class=" card-content table-responsive">
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                {{-- <th>Clave CT</th> --}}
                                <th>SKU</th>
                                {{-- <th>Categoría</th>
                                <th>Subcategoria</th>
                                <th>Marca</th> --}}
                                <th>Precio CT</th>
                                <th>Abasteo</th>
                                <th>CyberPuerta</th>
                                <th>MiPC</th>
                                <th>Zegucom</th>
                                <th>Precio Promedio</th>
                                <th>Margen de utilidad promedio</th>
                            </thead>
                            <tbody>
                                <?php $suma = 0.0; ?>
                                {{-- @if(sizeof($data['productos'])>0 || !empty($data['productos'])) --}}
                                @if(!empty($data['productos']))
                                    @foreach($data['productos'] as $key=>$row)
                                        <?php $divisor = 4; ?>
                                        <tr>
                                            {{-- <td>{{$row->clave_ct}}</td> --}}
                                            <td>{{$row->sku}}</td>
                                            {{-- <td>{{$row->categoria}}</td>
                                            <td>{{$row->subcategoria}}</td>
                                            <td>{{$row->marca}}</td> --}}
                                            <td>{{$row->precioct}}</td>
                                            <td>{{$row->abasteo}}</td>
                                            @if($row->abasteo == 0)
                                                <?php $divisor = $divisor-1;?>
                                            @endif
                                            <td>{{$row->cyberpuerta}}</td>
                                            @if($row->cyberpuerta == 0)
                                                <?php $divisor = $divisor-1;?>
                                            @endif
                                            <td>{{$row->mipc}}</td>
                                            @if($row->mipc == 0)
                                                <?php $divisor = $divisor-1;?>
                                            @endif
                                            <td>{{$row->zegucom}}</td>
                                            @if($row->zegucom == 0)
                                                <?php $divisor = $divisor-1;?>
                                            @endif
                                            <?php $suma = $row->abasteo + $row->cyberpuerta + $row->mipc + $row->zegucom;?>
                                            <td><?php if($divisor>0){ $promedio = $suma/$divisor; echo number_format($promedio,2); }else{ echo $row->precioct;}?></td>
                                            <td><?php if($divisor>0) echo number_format(($promedio/$row->precioct)*100-100,2)."%"; else echo $row->precioct;?></td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10">No existen productos activos con esos criterios de busqueda.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script>
        $(document).ready(function() {
            $("#filtro1").change(function () {
                var id = $(this).val();
                $("#llenar option").remove();
                $('#llenar ').append('<option select disabled>Todas las subcategorias</option>');
                var url = '{{ route("getCategorias", ":id") }}';
                url = url.replace(':id', id);
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function(response){
                        var newOption = '';
                        $.each(response, function (k, category) {
                            newOption += '<option value="' + category.id + '">' + category.nombre + '</option>';
                        });
                        $('#filtro2').append(newOption);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
            $("#filtro2").change(function () {
                var id = $('#filtro1').val();
                var id2 = $(this).val();
                $("#llenar3 option").remove();
                $('#llenar3 ').append('<option value="z" id="llenar4">Todas las marcas</option>');
                var url = '{{ route("getMarcas", [":id",":id2"]) }}';
                url = url.replace(':id', id);
                url = url.replace(':id2', id2);
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    success: function(response){
                        var newOption = '';
                        $.each(response, function (k, marca) {
                            newOption += '<option value="' + marca.id + '">' + marca.nombre + '</option>';
                        });
                        $('#filtro3').append(newOption);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>

@endsection

@section("script")
    <script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/plugins/chartjs/js/Chart.min.js"></script>
    <script src="assets/plugins/chartjs/js/Chart.extension.js"></script>
    <script src="assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="assets/js/index.js"></script>
@endsection
