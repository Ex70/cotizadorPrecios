@extends("layouts.app")

@section("style")
	<link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@section("wrapper")
	<div class="page-wrapper">
		<div class="page-content">
			<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
				<div class="breadcrumb-title pe-3">Inventario</div>
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
									<th>SKU</th>
									<th>Inventario</th>
									<th>Precio normal</th>
                                    {{-- <th></th>
                                    <th></th> --}}
								</tr>
							</thead>
							<tbody>
							@foreach ($data['productos'] as $productos)
								<tr>
									<td>{{$productos->clave_ct}}</td>
									@if (($productos->estatus == 'Activo'))
										<td>{{$productos->existencias}}</td>
									@else
										<td>0</td>
									@endif
                                    {{-- <td></td>
                                    <td></td> --}}
									@php
										if(isset($productos->margen)){
											if(isset($productos->descuento)){
												// $precio_rebajado = round(((($productos->precio_unitario)*(($productos->margen)+1))*((100-($productos->descuento))/100)),2);
												$precio_normal= round((($productos->precio_unitario)*(($productos->margen)+1)),2);
											}else{
												// $precio_rebajado = '';
												$precio_normal= round((($productos->precio_unitario)*(($productos->margen)+1)),2);
											}
										}else{
											if(isset($productos->descuento)){
												// $precio_rebajado = round((($productos->precio_unitario)*(1.10)*((100-($productos->descuento))/100)),2);
												$precio_normal= round((($productos->precio_unitario)*(1.10)),2);
											}else{
												// $precio_rebajado = '';
												$precio_normal= round((($productos->precio_unitario)*(1.10)),2);
											}
										}
										// if (($productos->almacen) == 50) {
											
										// }else{
										// 	$precio_rebajado = $precio_rebajado+100;
										// 	$precio_normal = $precio_normal+100;
										// }
										// $precio_final = round((($productos->precio_unitario)*(1.10)),2)
										// $precio_final = round((($productos->precio_unitario)*(($productos->margen)+1)),2)
									@endphp
									{{-- <td>{{$precio_rebajado}}</td>  --}}
									<td>{{$precio_normal}}</td> 
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
				order: [1,'asc'],
				pageLength: 50
			} );
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		});
	</script>
@endsection