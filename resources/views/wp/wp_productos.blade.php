@extends("layouts.app")

@section("style")
	<link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@section("wrapper")
	<div class="page-wrapper">
		<div class="page-content">
			<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
				<div class="breadcrumb-title pe-3">Productos</div>
				<div class="ps-3">
					<nav aria-label="breadcrumb">
						<ol class="breadcrumb mb-0 p-0">
							<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
							</li>
						</ol>
					</nav>
				</div>
			</div>
			<hr/>
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
							@foreach ($data['productos'] as $productos)
								<tr>
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
											// $fecha_inico = Carbon\Carbon::createFromFormat('Y-m-d',($productos->inicio))
											// ->format('d/m/Y 00:00:00');
											// $fecha_fin = Carbon\Carbon::createFromFormat('Y-m-d',($productos->fin))
											// ->format('d/m/Y 11:59:59');
											// $fecha_inico = $fecha_inico.' a. m.';
											// $fecha_fin = $fecha_fin.' p. m.';
											$fecha_inico = ($productos->inicio);
											$fecha_fin = ($productos->fin);
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
										if (($productos->almacen) == 50) {
											
										}else{
											$precio_rebajado = $precio_rebajado+100;
											$precio_normal = $precio_normal+100;
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
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@section("script")
	<script src="/assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="/assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
	<script>
		
		$(document).ready(function() {
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