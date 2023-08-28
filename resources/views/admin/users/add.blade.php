@extends('admin.base')

@section('title', $nameModule)

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item info" ><a href="{{ route('getUserIndex') }}">Usuarios</a></li>
                <li class="breadcrumb-item active">Crear Usuario</li>
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
                            <label for="user_type_id">Tipo de usuario <span class="text-red">*</span></label>
                            <select class="form-control form-control-border.border-width-2" name="user_type_id" required>
                                <option value="" selected>-- Selecciona una Opción --</option>
                                @foreach ($usersTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
    
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="email">Correo <span class="text-red">*</span></label>
                            <input class="form-control form-control-border.border-width-2" type="text" name="email" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="password">Contraseña <span class="text-red">*</span></label>
                            <input class="form-control form-control-border.border-width-2" type="password" name="password" required>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="department_id">Departamento <span class="text-red">*</span></label>
                            <select class="form-control form-control-border.border-width-2" name="department_id" id="department_id" required>
                                <option value="" selected disabled>-- Selecciona una Opción --</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-4">
                        <div class="form-group">
                            <label for="area_id">Área <span class="text-red">*</span></label>
                            <select class="form-control form-control-border.border-width-2" name="area_id" id="area_id" required>
                                <option value="" selected disabled>-- Primeramente selecciona un departamento --</option>
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
    document.getElementById("department_id").addEventListener("change", updateSelectArea);
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
                    document.location="{{ URL::to("users/index") }}";
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

    function updateSelectArea() {
        const departmentSelect = document.getElementById('department_id');
        const areaSelect = document.getElementById('area_id');
        const selectedDepartment = departmentSelect.value;

        areaSelect.innerHTML = '<option value="" selected disabled>-- Selecciona una Opción --</option>'
    
        var objSelect = @json($areas);

        objSelect.forEach(area => {
            const option = document.createElement("option");
            if(area.department_id == selectedDepartment) {
                option.text = area.name;
                option.value = area.id;
                areaSelect.appendChild(option);
            }
        });
    }
</script>
@stop