@extends('admin.base')

@section('title', $nameModule)

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <h1 class="text-info mt-20">{{ $nameModule }}</h1>
            <a class="btn btn-info" href="{{ URL::to($routeName."/add") }}"><i class="fas fa-plus-square"></i> Crear</a>
        </div>
    </div>
@stop

@section('content')
    <div class="table-responsive">
        <table id="dataTable" class="display table table-hover">
            <thead class="thead-ligth">
                <tr>
                    <th>ID</th>
                    <th>CÓDIGO</th>
                    <th>NOMBRE</th>
                    <th>STOCK</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>	
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="//cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/themes/default.css') }}">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.js">
<script>
    $(document).ready(function(){
        $('#dataTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "{{ route('postRowsArticles') }}",
                "dataType": "json",
                "type": "POST",
                "data": { _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": "id" },
                { "data": "code" },
                { "data": "name" },
                { "data": "stock" },
                { "data": "status", className: "text-right" },
                { "data": "options", className: "text-right" }
            ],	 
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "thousands":",",
                "lengthMenu": "Mostrar _MENU_ registros por pagina",
                "zeroRecords": "No Fue posible obtener registros",
                "info": "Mostrando pagina _PAGE_ de _PAGES_",
                "infoEmpty": "No records available",
                "infoFiltered": "(Filtrados de _MAX_ total de registros)",
                "loadingRecords": "Cargando...",
                "search":"Buscar:",
                "paginate": {
                    "first":      "Primera",
                    "last":       "Ultima",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                },
                "aria": {
                    "sortAscending":  ": activate to sort column ascending",
                    "sortDescending": ": activate to sort column descending"
                }
            },
        });
    })
    
    function alertDelete(href) {
        Swal.fire({
            title: '¿Quieres deshabilitar el registro?',
            text: 'Si quieres volver a utilizarlo recuerda que puedes activarlo editando el registro',
            type: "warning",
            showCancelButton: true,
            confirmButtonText: 'SI',
            cancelButtonText: 'NO',
            customClass: {
                actions: 'my-actions',
                cancelButton: 'order-1 ',
                confirmButton: 'order-2 right-gap'
            }
        }).then((result) => {
            if (result.value) {
                postAjaxDelete(href);

            } else {
                Swal.fire('Proceso cancelado!', '', 'info')

            }
        })
    };

    function postAjaxDelete(href) {
        $.ajax({
            url: href,
            method: 'GET',
            async: true,
            cache: false,
        }).done(function (data) { 
            if(data.type == 'success') {
                Swal.fire({
                    title: 'Registro desactivado exitosamente!',
                    type: 'success',
                    timer: 1000,
                    showCancelButton: false,
                    showConfirmButton: false
                })

                setTimeout(function() {
                    $('#dataTable').DataTable().ajax.reload();
                }, 1250);

            } else if(data.type == 'error') {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'cerrar'
                })

            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'cerrar'
                })
            }
        }).fail(function (data) {
            Swal.fire({
                title: 'Error!',
                text: 'No fue posible desactivar el registro intente de nuevo!',
                icon: 'error',
                confirmButtonText: 'cerrar'
            })
        });
    }
</script>
@stop