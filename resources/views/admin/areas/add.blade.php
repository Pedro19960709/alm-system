@extends('admin.base')

@section('title', $nameModule)

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item info" ><a href="{{ route('getAreaIndex') }}">Áreas</a></li>
                <li class="breadcrumb-item active">Crear Área</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ URL::to($routeName.'/add') }}" method="post" id="form-control">
            @csrf
            <div class="row">
                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="name">Nombre <span class="text-red">*</span></label>
                        <input class="form-control form-control-border.border-width-2" type="text" name="name" required>
                    </div>
                </div>

                <div class="col-sm-12 col-md-4">
                    <div class="form-group">
                        <label for="department_id">Departamento <span class="text-red">*</span></label>
                        <select class="form-control form-control-border.border-width-2" name="department_id" required>
                            <option value="" selected>-- Selecciona una Opción --</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js" integrity="sha512-3gJwYpMe3QewGELv8k/BX9vcqhryRdzRMxVfq6ngyWXwo03GFEzjsUm8Q7RZcHPHksttq7/GFoxjCVUjkjvPdw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/themes/default.css') }}">
<script>
    const form = document.querySelector('#form-control');
    
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('add', {
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
                    document.location="{{ URL::to("areas/index") }}";
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