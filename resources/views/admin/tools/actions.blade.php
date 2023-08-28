@if(!is_null($edit))
    <a href="{{ $edit }}" class="btn btn-sm btn-warning text-white mb-1"><i class="fas fa-edit"></i> Editar</a>
@endif

@if(!is_null($delete))
    <button href="{{ $delete }}" class="btn btn-sm btn-danger" onclick="alertDelete('{{ $delete }}')"><i class="fa fa-trash"></i> Borrar</button>
@else
    <button href="{{ $delete }}" class="btn btn-sm btn-secondary disabled" ><i class="fa fa-trash"></i> Borrar</button>
@endif