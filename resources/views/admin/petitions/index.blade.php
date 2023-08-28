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
                    <th>ARTÍCULOS</th>
                    <th>ARTÍCULOS RESTANTES</th>
                    <th>ARTÍCULOS ENTREGADOS</th>
                    <th>DEPARTAMENTO</th>
                    <th>ÁREA</th>
                    <th>FECHA DE PETICIÓN</th>
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
                "url": "{{ route('postRowsPetition') }}",
                "dataType": "json",
                "type": "POST",
                "data": { _token: "{{csrf_token()}}"}
            },
            "columns": [
                { "data": "id" },
                { "data": "articles_id" },
                { "data": "remaining_articles" },
                { "data": "delivered_articles" },
                { "data": "department_id" },
                { "data": "area_id" },
                { "data": "created_at" },
                { "data": "petition_status_id" },
                { "data": "actions", className: "text-right" },
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

    function alertCancel(href) {
        Swal.fire({
            title: '¿Quieres cancelar la petición?',
            text: 'Si cancelas la petición ya no podras entregar productos!',
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
                Swal.fire({
                    title: 'Agrega un comentario!',
                    html: `
                            <textarea id="comment" name="comment" cols="30" rows="10" class="form-control form-control-border.border-width-2" required></textarea>
                        `,
                    confirmButtonText: 'Guardar',
                    focusConfirm: false,
                    preConfirm: () => {
                        const comment = Swal.getPopup().querySelector('#comment').value
                        if (!comment) {
                            Swal.showValidationMessage(`Para poder completar la acción es necesario agregar un comentario!`)
                        }
                        
                        postAjaxCancel(href, comment);
                    }
                })

            } else {
                Swal.fire('Proceso cancelado!', '', 'info')
            }
        })
    };

    function postAjaxCancel(href, comment) {
        $.ajax({
            url: href,
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            data: {
                'comment': comment
            },
            async: true,
            cache: false,
        }).done(function (data) { 
            if(data.type == 'success') {
                Swal.fire({
                    title: 'Petición cancelada exitosamente!',
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
                text: 'No fue posible cancelar la petición intente de nuevo!',
                icon: 'error',
                confirmButtonText: 'cerrar'
            })
        });
    }
</script>
@stop