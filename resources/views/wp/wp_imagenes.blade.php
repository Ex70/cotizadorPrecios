
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
									<th>Clave CT</th>
									<th>Enlace "CT"</th>
									<th>Enlace "Full"</th>
                                    <th>Enlace "0"</th>
									<th>Enlace "1"</th>
									<th>Enlace "2"</th>
									<th>Enlace "3"</th>
									<th>Enlace "4"</th>
									<th>Enlace "5"</th>
									<th>Enlace "6"</th>
									<th>Enlace "7"</th>
									<th>Enlace "8"</th>
									<th>Enlace "9"</th>
									<th>Enlace "10"</th>
								</tr>
							</thead>
							<tbody>
							@foreach ($data['productos'] as $productos)
								<tr>
									<td>{{$productos->clave_ct}}</td>
                                    <td>http://ctonline.mx/img/productos/{{$productos->clave_ct}}.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_0_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_1_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_2_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_3_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_4_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_5_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_6_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_7_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_8_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_9_full.jpg</td>
									<td>https://static.ctonline.mx/imagenes/{{$productos->clave_ct}}/{{$productos->clave_ct}}_10_full.jpg</td>
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