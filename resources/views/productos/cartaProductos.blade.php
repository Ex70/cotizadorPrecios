@extends("layouts.app")

@section("style")
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@endsection

@section("wrapper")
<div class="page-wrapper">
  <div class="page-content">
    <div class="container box" style="width: 18rem;">
    <h1>Prueba de Cartas</h1>
      <div lass="form-group">
        <form action="/productos/cartas" method="post">
          @csrf
        <input type="text" name="clavect"  id="country_name" class="form-control" placeholder="Clave CT"><br/>
        <button type="submit">Buscar</button>
        </form>
        <div class="card" style="width: 18rem;">
        <img src="/IMG/1.jpeg" class="card-img-top" alt="...">
      <div class="card-header">
        CLAVE CT
      </div>
      <ul class="list-group list-group-flush">
      </ul>
    </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
@endsection
