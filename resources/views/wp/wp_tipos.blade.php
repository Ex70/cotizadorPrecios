@extends("layouts.app")

@section("style")
	<link href="/assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
@endsection

@section("wrapper")
	<div class="page-wrapper">
		<div class="page-content">
			<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
				<div class="breadcrumb-title pe-3">Imagenes</div>
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
                                    <th>URL externa</th>
									<th>Texto del botón</th>
								</tr>
							</thead>
							<tbody>
                                @foreach ($data['xalapa'] as $productos)
                                    <tr>
                                        <td>{{$productos->clave_ct}}</td>
                                        <td>simple</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
								@endforeach
                                @foreach ($data['resto'] as $productos)
                                    <tr>
                                        <td>{{$productos->clave_ct}}</td>
                                        <td>external</td>
                                        <td>https://api.whatsapp.com/send?phone=2283669400&text=Hola,%20quiero%20solicitar%20la%20cotización%20del%20producto:%20%2A{{$productos->nombre}}%2A%20con%20CLAVE:%20%2A{{$productos->clave_ct}}%2A</td>
										<td>Consultar Disponibilidad</td>
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