@extends("layouts.app")
@section("style")
    <link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
    <link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@section("wrapper")
    <div class="page-wrapper">
        <div class="page-content">
    <div class="row justify-content-centre" style="margin-top: 4%">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bgsize-primary-4 white card-header">
                    <h4 class="card-title">Productos por Categorias y Subcategorias</h4>
                </div>
                <div class="card-body">
                    @if ($message = Session::get('success'))
                        <div class="alert alert-success alert-block">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <strong>{{ $message }}</strong>
                        </div>
                        <br>
                    @endif
                    <form action="{{url("wp_productos_filtros")}}" method="post" enctype="multipart/form-data">
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
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="example2" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>SKU</th>
                            <th>Nombre</th>
                            <th>Publicado</th>
                            <th>¿Está destacado?</th>
                            <th>Visibilidad en el catálogo</th>
                            <th>Descripción corta</th>
                            <th>Descripción</th>
                            <th>Día en que empieza el precio rebajado</th>
                            <th>Día en que termina el precio rebajado</th>
                            <th>Estado del impuesto</th>
                            <th>¿Existencias?</th>
                            <th>Inventario</th>
                            <th>¿Permitir reservas de productos agotados?</th>
                            <th>¿Vendido individualmente?</th>
                            <th>¿Permitir valoraciones de clientes?</th>
                            <th>Precio rebajado</th>
                            <th>Precio normal</th>
                            <th>Categorías</th>
                            <th>Etiquetas</th>
                            <th>Imágenes</th>
                            <th>Posición</th>
                            <th>URL externa</th>
                            <th>Texto del botón</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data['productos']))
                            @foreach ($data['productos'] as $productos)
                            <tr>
                                {{-- @if ($data['met'] == 1) --}}
                                @if (($productos->almacen) == 50)
                                    @if(($productos->existencias) >= 1)
                                        <td>simple</td>
                                    @else
                                        <td>external</td>
                                    @endif
                                @else
                                    <td>external</td>
                                @endif
                                <td>{{$productos->clave_ct}}</td>
                                <td>{{$productos->nombre}}</td>
                                <td>1</td>
                                <td>0</td>
                                <td>visible</td>
                                <td>{{$productos->descripcion_corta}}</td>
                                <td>{{$productos->descripcion_corta}}</td>
                                @php
                                    if(isset($productos->inicio)){
                                        $fecha_inico = Carbon\Carbon::createFromFormat('Y-m-d',($productos->inicio))
                                        ->format('d/m/Y 00:00:00');
                                        $fecha_fin = Carbon\Carbon::createFromFormat('Y-m-d',($productos->fin))
                                        ->format('d/m/Y 11:59:59');
                                        $fecha_inico = $fecha_inico.' a. m.';
                                        $fecha_fin = $fecha_fin.' p. m.';
                                    }else{
                                        $fecha_inico = '';
                                        $fecha_fin = '';
                                    }
                                    $mes= date('m');
                                    $año= date('Y');
                                @endphp
                                <td>{{$fecha_inico}}</td>
                                <td>{{$fecha_fin}}</td>
                                <td>taxable</td>
                                <td>1</td>
                                {{-- @if ($data['met'] == 1) --}}
                                @if (($productos->almacen) == 50)
                                    @if(($productos->existencias) >= 1)
                                        <td>{{$productos->existencias}}</td>
                                    @else
                                        <td></td>
                                    @endif
                                @else
                                    <td></td>
                                @endif
                                <td>0</td>
                                <td>0</td>
                                <td>1</td>
                                @php
                                    if(isset($productos->margen)){
                                        if(isset($productos->descuento)){
                                            $precio_rebajado = round(((($productos->precio_unitario)*(($productos->margen)+1))*((100-($productos->descuento))/100)),2);
                                            $precio_normal= round((($productos->precio_unitario)*(($productos->margen)+1)),2);
                                        }else{
                                            $precio_rebajado = '';
                                            $precio_normal= round((($productos->precio_unitario)*(($productos->margen)+1)),2);
                                        }
                                    }else{
                                        if(isset($productos->descuento)){
                                            $precio_rebajado = round((($productos->precio_unitario)*(1.10)*((100-($productos->descuento))/100)),2);
                                            $precio_normal= round((($productos->precio_unitario)*(1.10)),2);
                                        }else{
                                            $precio_rebajado = '';
                                            $precio_normal= round((($productos->precio_unitario)*(1.10)),2);
                                        }
                                    }
                                    // $precio_final = round((($productos->precio_unitario)*(1.10)),2)
                                    // $precio_final = round((($productos->precio_unitario)*(($productos->margen)+1)),2)
                                @endphp
                                <td>{{$precio_rebajado}}</td> 
                                <td>{{$precio_normal}}</td> 
                                @if (isset($productos->descuento))
                                    <td>{{$productos->categoria}}, {{$productos->categoria}} > {{$productos->subcategoria}}, {{$productos->marca}}, Promociones</td>
                                @else
                                    <td>{{$productos->categoria}}, {{$productos->categoria}} > {{$productos->subcategoria}}, {{$productos->marca}}</td>
                                @endif
                                <td>{{$productos->categoria}},  {{$productos->subcategoria}}, {{$productos->marca}}</td>
                                <td>https://ehstecnologias.com.mx/wp-content/uploads/{{$año}}/{{$mes}}/{{$productos->clave_ct}}_0.jpg</td>
                                {{-- @if ($data['met'] == 1) --}}
                                @if (($productos->almacen) == 50)
                                    @if(($productos->existencias) >= 1)
                                        @if (isset($productos->descuento))
                                            <td>1</td>
                                        @else
                                            <td>2</td>
                                        @endif
                                    @else
                                        <td>3</td>
                                    @endif
                                @else
                                    <td>3</td>
                                @endif
                                {{-- @if ($data['met'] == 1) --}}
                                @if (($productos->almacen) == 50)
                                    @if(($productos->existencias) >= 1)
                                        <td></td>
                                        <td></td>
                                    @else
                                        <td>https://api.whatsapp.com/send?phone=2283669400&text=Hola,%20quiero%20solicitar%20la%20cotización%20del%20producto:%20%2A{{$productos->nombre}}%2A%20con%20CLAVE:%20%2A{{$productos->clave_ct}}%2A</td>
                                        <td>Consultar Disponibilidad</td>
                                    @endif
                                @else
                                    <td>https://api.whatsapp.com/send?phone=2283669400&text=Hola,%20quiero%20solicitar%20la%20cotización%20del%20producto:%20%2A{{$productos->nombre}}%2A%20con%20CLAVE:%20%2A{{$productos->clave_ct}}%2A</td>
                                    <td>Consultar Disponibilidad</td>
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
@endsection

@section("script")
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
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
            var table = $('#example2').DataTable( {
				lengthChange: false,
				buttons: [ 'csv','excel', 'pdf', 'print'],
				order: [3,'asc'],
				pageLength: 50
			} );
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
        });
    </script>
@endsection