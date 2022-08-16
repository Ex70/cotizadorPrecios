@extends("layouts.app")

@section("style")
<link href="assets/plugins/fancy-file-uploader/fancy_fileupload.css" rel="stylesheet" />
<link href="assets/plugins/Drag-And-Drop/dist/imageuploadify.min.css" rel="stylesheet" />
@endsection
    @section("wrapper")
        <div class="page-wrapper">
            <div class="page-content">
                @if (session()->has('message'))
                <?php echo "HOLA"; ?>
                    <div class="alert alert-success">
                        <p>{{session('message')}}</p>
                    </div>
                @endif
                <!--breadcrumb-->
                <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                    <div class="breadcrumb-title pe-3">Top100</div>
                    <div class="ps-3">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0 p-0">
                                <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Subir archivo</li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!--end breadcrumb-->
                <div class="row">
                    <div class="col-xl-9 mx-auto">
                        <h6 class="mb-0 text-uppercase">Subir documento</h6>
                        <hr/>
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('importTops') }}" method="POST" enctype="multipart/form-data" class="d-flex">
                                    <div class="col-12">
                                        @csrf
                                        <input id="image-uploadify" type="file" name="file" accept=".xlsx,.xls" multiple>
                                        <br>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary px-5">Subir</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end row-->
            </div>
        </div>
    @endsection

@section("script")
<script src="assets/plugins/fancy-file-uploader/jquery.ui.widget.js"></script>
<script src="assets/plugins/fancy-file-uploader/jquery.fileupload.js"></script>
<script src="assets/plugins/fancy-file-uploader/jquery.iframe-transport.js"></script>
<script src="assets/plugins/fancy-file-uploader/jquery.fancy-fileupload.js"></script>
<script src="assets/plugins/Drag-And-Drop/dist/imageuploadify.min.js"></script>
<script>
    $('#fancy-file-upload').FancyFileUpload({
        params: {
            action: 'fileuploader'
        },
        maxfilesize: 1000000
    });
</script>
<script>
    $(document).ready(function () {
        $('#image-uploadify').imageuploadify();
    })
</script>
@endsection
