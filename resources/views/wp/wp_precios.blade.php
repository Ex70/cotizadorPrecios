@extends("layouts.app")

@section("style")
	<link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@section("wrapper")
	<div class="page-wrapper">
		<div class="page-content">
			<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
				<div class="breadcrumb-title pe-3">Precios</div>
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
									{{-- <th>Día en que empieza el precio rebajado</th>
                                    <th>Día en que termina el precio rebajado</th>
                                    <th>Precio rebajado</th> --}}
									<th>Inventario</th>
                                    <th>Precio normal</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
							@foreach ($data['productos'] as $productos)
								<tr>
									<td>{{$productos->clave_ct}}</td>
									@if (($productos->estatus) == 'Activo')
										<td>{{$productos->existencias}}</td>
									@else
										<td>0</td>
									@endif
                                    @php
										if(isset($productos->margen)){
												$precio_normal= round((($productos->precio_unitario)*(($productos->margen)+1)),2);
										}else{
												$precio_normal= round((($productos->precio_unitario)*(1.10)),2);
										}
										// if (($productos->almacen) == 50) {
										// }else{
										// 	$precio_normal = $precio_normal+100;
										// }
									@endphp
									<td>{{$precio_normal}}</td> 
									<td></td>
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