<!DOCTYPE html>
<html>
<head>
    <title>Cotizador de pagos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" />
</head>
<body>
    <div class="container">
    <div class="card-header bg-secondary dark bgsize-darken-4 white card-header">
        <h4 class="text-white">EHS Tecnologías</h4>
    </div>
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
                            <label>Seleccione una categoría y subcategoría para consultar</label>
                            <div class="input-group">
                                <select name="filtro1" id="filtro1" class="form-control">
                                    <option value="x">Todas las categorias</option>
                                    @foreach($data['categorias'] as $row)
                                        <option value="{{$row->id}}">{{$row->nombre}}</option>
                                    @endforeach
                                </select>
                                <select name="filtro2" id="llenar" class="form-control">
                                    <option value="y" id="llenar2">Todas las subcategorias</option>
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
                                <th>Clave CT</th>
                                <th>SKU</th>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Abasteo</th>
                            </thead>
                            <tbody>
                                @if(!empty($data['productos']))
                                    @foreach($data['productos'] as $key=>$row)
                                        <tr>
                                            <td>{{$row->clave_ct}}</td>
                                            <td>{{$row->sku}}</td>
                                            <td>{{$row->nombre}}</td>
                                            <td>{{$row->precio_unitario}}</td>
                                            @if(!empty($data['abasteo'][$key]))
                                                <td>{{$data['abasteo'][$key]}}</td>
                                            @else
                                                <td></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="10">There are no data.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
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
                $('#llenar ').append('<option value="y" id="llenar2">Todas las subcategorias</option>');
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
                        $('#llenar').append(newOption);
                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
</body>
</html>