@extends('admin.base')

@section('title', $nameModule)

@section('content_header')
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ol class="breadcrumb float-sm-left">
                <li class="breadcrumb-item info" ><a href="{{ route('getPetitionIndex') }}">Peticiones</a></li>
                <li class="breadcrumb-item active">Crear Petición</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ URL::to($routeName.'/add') }}" method="post" id="form-control">
            @csrf

            <div class="row">
                <table class="table concepts-table">
                    <thead>
                        <tr>
                            <th style="width: 40%">Nombre</th>
                            <th style="width: 20%">Artículos Disponibles</th>
                            <th style="width: 20%">Cantidad Seleccionada</th>
                            <th style="width:5%">
                                <button type="button" class="btn btn-sm btn-primary" onclick="addItem()"><i class="fas fa-cart-plus"></i> Agregar</button>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="wrapper-item-lines">

                    </tbody>
                </table>
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

    function deleteItem(element){
        element.parentElement.parentElement.remove();
    }

    function addItem(){
        const oDiv = document.createElement('tr');
        oDiv.classList.add('item-line');
        
        const sNode = `
        <td>
            <div class="col-sm-12 col-md">
                <div class="form-group">
                    <select class="form-control form-control-border.border-width-2" name="article_id[]" id="article_id" onchange="updateSelectArea(this.parentElement.parentElement.parentElement.parentElement)"  required>
                        <option value="" selected disabled>-- Selecciona una Opción --</option>
                        @foreach ($articles as $article)
                            <option value="{{ $article->id }}">{{ $article->code.' - '.$article->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </td>
        <td>
            <input class="form-control form-control-border.border-width-2" type="number" id="stock" name="stock[]" value="0" disabled>
        </td>
        <td>
            <input class="form-control form-control-border.border-width-2" type="number" name="ordered_articles[]" min="1" required>
        </td>
        <td>
            <button style="" class="btn btn-danger delete-item" onclick="deleteItem(this)"><i class="fas fa-trash-alt"></i></button>
        </td>
        `;
        oDiv.innerHTML = sNode;
        document.querySelector('tbody.wrapper-item-lines').appendChild(oDiv);
    }

    function updateSelectArea(element) {
        const articleSelect = element.childNodes[1].childNodes[1].childNodes[1].childNodes[1];
        const stockInput = element.childNodes[3].childNodes[1];
        const remainingInput = element.childNodes[5].childNodes[1];
        const selectedArticle = articleSelect.value;

        var articles = @json($articles);

        const objFound = articles.find(objeto => objeto.id == selectedArticle);
        // remainingInput.setAttribute('max', objFound.stock);
        stockInput.value = objFound.stock;        
    }
</script>
@stop