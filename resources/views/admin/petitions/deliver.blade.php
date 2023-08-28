@extends('admin.base')

@section('title', $nameModule)

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item info" ><a href="{{ route('getPetitionIndex') }}">Peticiones</a></li>
                <li class="breadcrumb-item active">Historial del Articulo</li>
            </ol>

        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="callout callout-info col-sm mr-2">
            <div>
                <h3 class="text-info">Información</h3>
                <p><span class="font-weight-bold">Departamento: </span> <span>{{ $petition->Department->name }}</span></p>
                <p></p>
            </div>

            <div>
                <p><span class="font-weight-bold">Área: </span> <span>{{ $petition->Area->name }}</span></p>
                <p></p>
            </div>

            <div>
                <p><span class="font-weight-bold">Artículo: </span> <span>{{ $petition->Article->code.' - '.$petition->Article->name.' ('.$petition->Article->quantity.' '.$petition->Article->MeasurementUnit->symbol.')' }}</span></p>
                <p></p>
            </div>

            <div>
                <p><span class="font-weight-bold">Fecha: </span> <span>{{ $created_at }}</span></p>
            </div>
        </div>

        <div class="callout callout-info col-sm">
            <h3 class="text-info">Seguimiento</h3>
            <div>
                <p><span class="font-weight-bold">Total de Productos: </span><span>{{ $petition->ordered_articles }}</span></p>
                <p></p>
            </div>

            <div>
                <p><span class="font-weight-bold">Productos Restantes: </span> <span>{{ $petition->remaining_articles }}</span></p>
                <p></p>
            </div>

            <div>
                <p><span class="font-weight-bold">Productos Entregados: </span> <span>{{ $petition->delivered_articles }}</span></p>
                <p></p>
            </div>

            <div>
                <p><span class="font-weight-bold">Estado del pedido: </span> <span>{{ $petition->Status->name }}</span></p>
            </div>
        </div>
    </div>

       <hr>
       <form action="{{ URL::to($routeName.'/item-deliver/'.$petition->id) }}" method="post" id="form-control">
        @csrf
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="ordered_articles">Productos</label>
                        <input class="form-control form-control-border.border-width-2" type="number" value="{{ $petition->ordered_articles }}" disabled>
                        <input type="number" name="ordered_articles" value="{{ $petition->ordered_articles }}" hidden>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="remaining_articles">Productos Restantes</label>
                        <input type="number" class="form-control form-control-border.border-width-2" value="{{ $petition->remaining_articles }}" disabled required>
                        <input type="number" name="remaining_articles" value="{{ $petition->remaining_articles }}" hidden>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="delivered_articles">Productos a Entregar</label>
                        <input type="number" name="delivered_articles" id="delivered_articles" class="form-control form-control-border.border-width-2" min="0" max="{{ $petition->remaining_articles }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 col-md">
                    <div class="form-group">
                        <label for="comment">Comentario</label>
                        <textarea name="comment" cols="30" rows="10" class="form-control form-control-border.border-width-2" required></textarea>
                    </div>
                </div>
            </div>

            <hr>
            <div class="row d-flex justify-content-end">
                <div class="mr-3 offset-6 text-rigth">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
                    <a type="button" class="btn btn-warning text-white" href="{{ URL::to($routeName."/index") }}"><i class="fas fa-undo"></i> Cancelar</a>
                </div>
            </div>
        </form>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        const form = document.querySelector('#form-control');

        form.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            fetch("{{ URL::to("petitions/item-deliver/{$petition->id}") }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                sessionStorage.setItem('respuestaCache', JSON.stringify(data));
                
                if(data.type == 'success') {
                    Swal.fire({
                        title: 'Creación Exitosa!',
                        type: 'success',
                        timer: 1000,
                        showCancelButton: false,
                        showConfirmButton: false
                    })
                    setTimeout(function(){
                        document.location="{{ URL::to("petitions/index") }}";
                    }, 1250);

                }else if(data.type == 'error') {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'cerrar'
                    })

                }else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'cerrar'
                    })
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
@stop