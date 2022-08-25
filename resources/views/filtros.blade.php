@extends("layouts.app")
@section("style")
    <link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
@endsection

@section("wrapper")
    <div class="page-wrapper">
        <div class="page-content">
    <div class="row justify-content-centre" style="margin-top: 4%">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bgsize-primary-4 white card-header">
                    <h4 class="card-title">Cotizador de precios</h4>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        <br>
                    @endif
                    <form action="{{url("import")}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <fieldset>
                            <label>Seleccione una categoría y subcategoría para consultar, la marca es opcional</label>
                            <div class="input-group">
                                <select name="filtro1" id="filtro1" class="form-control" required>
                                    <option selected disabled>Todas las categorias</option>
                                    @foreach($data['categorias'] as $row)
                                        <option value="{{$row->id}}">{{$row->nombre}}</option>
                                    @endforeach
                                </select>
                                <select name="filtro2" id="filtro2" class="form-control">
                                    <option selected disabled>Todas las subcategorias</option>
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
                                <th>ID</th>
                                <th>Clave CT</th>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Precio CT</th>
                                <th>Abasteo</th>
                                <th>Cyberpuerta</th>
                                <th>MiPC</th>
                                <th>Zegucom</th>
                            </thead>
                            <tbody>
                                @if(!empty($data['productos']))
                                    @foreach($data['productos'] as $key=>$row)
                                        <tr>
                                            <td>{{$key}}</td>
                                            <td>{{$row->clave_ct}}</td>
                                            <td>{{$row->sku}}</td>
                                            <td>{{$row->nombre}}</td>
                                            <td>{{$row->precio_unitario}}</td>
                                            @if(!empty($data['abasteo'][$key]))
                                                <td>{{$data['abasteo'][$key]}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
                                            @if(!empty($data['cyberpuerta'][$key]))
                                                <td>{{$data['cyberpuerta'][$key]}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
                                            @if(!empty($data['mipc'][$key]))
                                                <td>{{$data['mipc'][$key]}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
                                            @if(!empty($data['zegucom'][$key]))
                                                <td>{{$data['zegucom'][$key]}}</td>
                                            @else
                                                <td>0</td>
                                            @endif
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
                $("#filtro2 option").remove();
                $('#filtro2 ').append('<option select disabled>Todas las subcategorias</option>');
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
                $("#filtro3 option").remove();
                $('#filtro3 ').append('<option value="z" id="llenar4">Todas las marcas</option>');
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
