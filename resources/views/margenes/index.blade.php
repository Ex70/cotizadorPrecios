	@extends("layouts.app")

	@section("style")
	<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
	@endsection

		@section("wrapper")
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Márgenes de utilidad</div>
				</div>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Categoria</th>
										<th>Subcategoria</th>
										<th>Marca</th>
										<th>Existencias</th>
										<th>Margen de utilidad</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($data['combinaciones'] as $key => $combinacion)
										{{-- @if(number_format($data['margenes'][$key]*100,2)>10) --}}
											<tr>
												<td>{{$combinacion->categoria}}</td>
												<td>{{$combinacion->Subcategoria}}</td>
												<td>{{$combinacion->Marca}}</td>
												<td>{{$combinacion->existencias}}</td>
												<td>{{number_format($data['margenes'][$key]*100,2)}}%</td>
											</tr>
										{{-- @endif --}}
									@endforeach
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end page wrapper -->
		@endsection
	
	@section("script")
	<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
	<script>
		$(document).ready(function() {
			$('#example').DataTable();
		  } );
	</script>
	<script>
		$(document).ready(function() {
			var table = $('#example2').DataTable( {
				lengthChange: false,
				buttons: [ 'excel', 'pdf', 'print']
			} );
		 
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		} );
	</script>
	@endsection