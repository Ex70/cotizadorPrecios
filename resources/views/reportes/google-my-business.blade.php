	@extends("layouts.app")

	@section("style")
	<link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
	@endsection

		@section("wrapper")
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
				<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
					<div class="breadcrumb-title pe-3">Google My Business</div>
					<div class="ps-3">
						<nav aria-label="breadcrumb">
							<ol class="breadcrumb mb-0 p-0">
								<li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
								</li>
								<li class="breadcrumb-item">Reportes</li>
								<li class="breadcrumb-item active" aria-current="page">Google My Business</li>
							</ol>
						</nav>
					</div>
				</div>
				{{-- <h6 class="mb-0 text-uppercase">Productos más vendidos del mes de {{$productos['mes']}}</h6> --}}
				<hr/>

				{{-- <div class="row">
					<div class="col-xl-12 mx-auto">
						<hr/>
						<div class="card border-top border-0 border-4 border-primary">
							<div class="card-body p-5">
								<div class="card-title d-flex align-items-center">
									<div><i class="bx bxs-user me-1 font-22 text-primary"></i>
									</div>
									<h5 class="mb-0 text-primary">Consultar ventas por mes - año</h5>
								</div>
								<hr>
								<form class="row g-3" method="POST" action="{{ route('consultarTops') }}">
									@csrf
									<div class="col-md-6">
										<label for="inputState" class="form-label">Mes</label>
										<select name="month" id="inputState" class="form-select" required>
											<option selected disabled>Escoge un mes</option>
											<option value="1">Enero</option>
											<option value="2">Febrero</option>
											<option value="3">Marzo</option>
											<option value="4">Abril</option>
											<option value="5">Mayo</option>
											<option value="6">Junio</option>
											<option value="7">Julio</option>
											<option value="8">Agosto</option>
											<option value="9">Septiembre</option>
											<option value="10">Octubre</option>
											<option value="11">Noviembre</option>
											<option value="12">Diciembre</option>
										</select>
									</div>
									<div class="col-md-6">
										<label for="inputState" class="form-label">Año</label>
										<select name="year" id="inputState" class="form-select">
											<option selected disabled>Escoge un año</option>
											<option value="2022">2022</option>
											<option value="2023">2023</option>
										</select>
									</div>
									<div class="col-12">
										<button type="submit" class="btn btn-primary px-5">Consultar</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div> --}}
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
								<thead>
									<tr>
										<th>Clave CT</th>
										<th>Subcategoria</th>
										<th>Nombre</th>
										<th>Descripción Corta</th>
										<th>Enlace</th>
									</tr>
								</thead>
								<tbody>
									@if($data['productos'])
									@foreach ($data['productos'] as $producto)
										<tr>
											<td>{{$producto->clave_ct}}</td>
											<td>{{$producto->subcategoria}}</td>
											<td>{{$producto->nombre}}</td>
											<td>{{$producto->descripcion_corta}}</td>
											<td>{{$producto->enlace}}</td>
										</tr>
									@endforeach
									@endif
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!--end page wrapper -->
		@endsection
	
	@section("script")
	<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
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