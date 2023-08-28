@if(!is_null($pdf))
    <a href="{{ $pdf }}" class="btn btn-sm btn-info text-white m-1"><i class="fas fa-file-pdf"></i> PDF</a>
@endif

@if(!is_null($history))
    <a href="{{ $history }}" class="btn btn-sm btn-info text-white m-1"><i class="fas fa-comments"></i> Comentarios</a>
@endif

@if(!is_null($deliver))
    <a href="{{ $deliver }}" class="btn btn-sm btn-success m-1"><i class="fas fa-dolly"></i> Entregar</a>
@else
    <button href="{{ $deliver }}" class="btn btn-sm btn-success m-1 disabled"><i class="fas fa-dolly"></i> Entregar</button>
@endif 

@if(!is_null($cancel))
    <button href="{{ $cancel }}" class="btn btn-sm btn-danger m-1" onclick="alertCancel('{{ $cancel }}')"><i class="fas fa-window-close"></i> Cancelar</button>
@else
    <button href="{{ $cancel }}" class="btn btn-sm btn-danger m-1 disabled"><i class="fas fa-window-close"></i> Cancelar</button>
@endif 