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
	<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
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
                    @if(!empty($data['productos']))
                    <div class="card">
                        <div class="card-header bgsize-primary-4 white card-header">
                            <h4 class="card-title">Precios</h4>
                        </div>
                        <div class="card-body">
                            <div class=" card-content table-responsive">
                                <table id="tabla-productos" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <th>SKU</th>
                                        <th>Existencias</th>
                                        <th>Peso</th>
                                        <th>Precio CT</th>
                                        <th>Abasteo</th>
                                        <th>MiPC</th>
                                        <th>Zegucom</th>
                                        <th>Precio Promedio</th>
                                        <th>Margen promedio</th>
                                        <th>Precio*Margen</th>
                                        <th>Porcentaje agregado</th>
                                        <th>Comisión Mercadopago</th>
                                        {{-- <th>Menor precio</th>
                                        <th>Utilidad a menor precio</th> --}}
                                        {{-- <th>Comisión Mercadopago</th> --}}
                                    </thead>
                                    <tbody>
                                        <?php  $suma = 0.0; $sumPromedio = 0.0; $sumaCT = 0.0; $a =1; $stockTotal = 0; $margenesTotales=0;?>
                                        @if(!empty($data['productos']))
                                            @foreach($data['productos'] as $key=>$row)
                                                <?php $divisor = 3; $min = 1000000;?>
                                                @if($row->existencias < ($data['totalSubCat']/2))
                                                    <tr>
                                                @else
                                                    <tr style="background-color: aquamarine;">
                                                @endif
                                                    <td>{{$row->sku}}</td>
                                                    <td>{{$row->existencias}}</td>
                                                    <td>{{$row->existencias/$data['totalFiltro']}}</td>
                                                    <?php $stockTotal = $stockTotal + $row->existencias;?>
                                                    <td>${{$row->precioct}}</td>
                                                        <?php $sumaCT = $sumaCT + $row->precioct;?>
                                                    @if($row->abasteo < ($row->precioct*.65))
                                                        <?php $row->abasteo = 0; ?>
                                                    @endif
                                                    <td>${{$row->abasteo}}</td>
                                                        @if($row->abasteo != 0 && $row->abasteo<$min)
                                                            <?php $min = $row->abasteo;?>
                                                        @endif
                                                        @if($row->abasteo == 0)
                                                            <?php $divisor = $divisor-1;?>
                                                        @endif
                                                    <td>${{$row->mipc}}
                                                    </td>
                                                        @if($row->mipc != 0 && $row->mipc<$min)
                                                            <?php $min = $row->mipc;?>
                                                        @endif
                                                        @if($row->mipc == 0)
                                                            <?php $divisor = $divisor-1;?>
                                                        @endif
                                                    <td>${{$row->zegucom}}</td>
                                                        @if($row->zegucom != 0 && $row->zegucom<$min)
                                                            <?php $min = $row->zegucom;?>
                                                        @endif
                                                        @if($row->zegucom == 0)
                                                            <?php $divisor = $divisor-1;?>
                                                        @endif
                                                    <?php $suma = $row->abasteo + $row->cyberpuerta + $row->mipc + $row->zegucom;?>
                                                    {{-- PRECIO PROMEDIO --}}
                                                    <td>$<?php if($divisor>0){ $promedio = $suma/$divisor; $sumPromedio = $sumPromedio + $promedio; echo number_format($promedio,2); }else{ echo $row->precioct; $sumPromedio = $sumPromedio + $row->precioct;}?></td>
                                                    {{-- MARGEN PROMEDIO --}}
                                                    <?php $precioMargenTotal = $row->existencias*number_format((1-($row->precioct/$promedio)),4); ?>
                                                    <?php $margenesTotales = $margenesTotales + $precioMargenTotal; ?>
                                                    <td><?php if($divisor>0) echo number_format((1-($row->precioct/$promedio))*100,2)."%"; else echo $row->precioct;?></td>
                                                    <td>{{$precioMargenTotal}}</td>
                                                    {{-- Porcentaje Agregado --}}
                                                    <td><?php if($divisor>0) echo number_format(($promedio/$row->precioct)*100-100,2)."%"; else echo $row->precioct;?></td>
                                                    {{-- Comisión Mercadopago --}}
                                                    <td>$<?php if($divisor>0) echo number_format(((($row->precioct*number_format(((($promedio/$row->precioct)*100-100)/100)+1,4)/100)*3.49)+1)*1.16,2); else echo $row->precioct." - ".$divisor;?></td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="11">No existen productos activos con esos criterios de busqueda.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @if(sizeof($data['productos'])>0)
                <div class="row justify-content-centre" style="margin-top: 1%">
                    <div class="card">
                        <div class="card-body">
                            <div class="list-group">
                                <a href="javascript:;" class="list-group-item list-group-item-action active" aria-current="true">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1 text-white">Resultados</h5>
                                        <small>Márgenes de utilidad</small>
                                    </div>
                                </a>
                                <a href="javascript:;" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        {{"Sumatoria de producto de márgenes: "}}<?php echo $margenesTotales;?><br>
                                        {{"Existencias totales de filtro: "}}<?php echo $stockTotal;?><br><br>
                                        {{"Precio general promedio: "}}<?php echo "$".number_format($sumPromedio/sizeof($data['productos']),2);?><br>
                                        {{"Precio EHS promedio: "}}<?php echo "$".number_format($sumaCT/sizeof($data['productos']),2);?><br>
                                        {{"Stock de filtro: "}}<?php echo $stockTotal;?><br><br>
                                        {{"Margen de utilidad para marca: "}}<?php echo number_format((($sumPromedio/sizeof($data['productos']))/($sumaCT/sizeof($data['productos'])))*100-100,2)."%";?><br>
                                        {{"Existencias de categoria: "}}<?php echo $data['totalCat'];?><br>
                                        {{"Existencias de subcategoria: "}}<?php echo $data['totalSubCat'];?><br>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script>
            $(document).ready(function() {
                $('#tabla-productos').DataTable({
                    order: [[1,'desc']]
                });
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
<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="assets/plugins/chartjs/js/Chart.min.js"></script>
    <script src="assets/plugins/chartjs/js/Chart.extension.js"></script>
    <script src="assets/plugins/jquery.easy-pie-chart/jquery.easypiechart.min.js"></script>
    <script src="assets/js/index.js"></script>
@endsection
