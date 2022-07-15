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
                   <h4 class="card-title">Cotizador de precios Cyberpuerta</h4>
               </div>
               <div class="card-body">
                   @if ($message = Session::get('success'))
                       <div class="alert alert-success alert-block">
                           <button type="button" class="close" data-dismiss="alert">×</button>
                           <strong>{{ $message }}</strong>
                       </div>
                       <br>
                   @endif
                   <form action="{{url("importCyberpuerta")}}" method="post" enctype="multipart/form-data">
                       @csrf
                       <fieldset>
                           <label>Seleccione una categoría y subcategoría para consultar</label>
                           <div class="input-group">
                            <select name="filtro1" id="filtro1" class="form-control">
                                <option value="x">Todas las categorias</option>
                                @foreach($data['categorias'] as $row)
                                    <option value="{{$row->categoria}}">{{$row->categoria}}</option>
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
                   <!-- <div class="pull-right">
                       <a href="{{url("export")}}" class="btn btn-primary" style="margin-left:85%">Export Excel Data</a>
                   </div> -->
                   <div class=" card-content table-responsive">
                       <table id="example" class="table table-striped table-bordered" style="width:100%">
                           <thead>
                           <th>Precios</th>
                           <!-- <th>Gender</th>
                           <th>Address</th>
                           <th>City</th>
                           <th>Postal Code</th>
                           <th>Country</th> -->
                           </thead>
                           <tbody>
                           @if(!empty($data['productos']))
                               @foreach($data['precios'] as $key=>$row)
                                   <tr>
                                       <td>{!! $data['precios'][$key] !!}</td>
                                       
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
        //    $('#example').DataTable();

            $("#filtro1").change(function () {    
                var id = $(this).val();
                var url = '{{ route("getCategorias", ":id") }}';
                url = url.replace(':id', id);
                $.ajax({
                // var url = '{{ route("getCategorias", ":categoria") }}';
                // var id = $(this).val();
                // url = url.replace(':categoria', id),
                // url: "{{ route('getCategorias') }}",
                url: url,
                type: 'get',
                dataType: 'json',
                success: function(response){
                // type: 'POST',
                // data: {
                //     _token: "{{ csrf_token() }}"
                // },
                // success: function (data) {
                    var newOption = '';
                        $.each(response, function (k, category) {
                            newOption += '<option value="' + category.subcategoria + '">' + category.subcategoria + '</option>';
                        });
                        $('#llenar').append(newOption);
                },
                error: function (error) {
                    console.log(error);
                }
            });
        });
       } );

   </script>
</body>
</html>