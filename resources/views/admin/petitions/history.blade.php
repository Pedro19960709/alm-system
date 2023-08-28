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

        <div class="timeline">
            @foreach ($timeline as $history)
                @if($history->NextStatus->flag == 1 || $history->NextStatus->flag == 1)
                    <div>
                        @switch($history->next_status_id)
                            @case(3)
                                <i class="fas fa-check-double bg-success"></i>
                                @break
                            @case(5)
                                <i class="fas fa-times-circle bg-danger"></i>
                                @break
                            @default
                        @endswitch
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at }}</span>
                                <h3 class="timeline-header">{{ $history->User->name }}</h3>
                                <div class="timeline-body">
                                    @switch($history->next_status_id)
                                        @case(3)
                                            <p><span class="text-success">COMENTARIO: </span> {{ $history->comment }}</p>
                                            <p><span class="text-success">ARTÍCULOS ENTREGADOS: </span> {{ $history->delivered_articles }}</p>
                                            <p><span class="text-success">Estado: </span> {{ $history->NextStatus->name }}</p>
                                            @break
                                        @case(5)
                                            <p><span class="text-danger">COMENTARIO: </span> {{ $history->comment }}</p>
                                            <p><span class="text-danger">Estado: </span> {{ $history->NextStatus->name }}</p>
                                            @break
                                        @default
                                    @endswitch
                                </div>
                                <div class="timeline-footer">
                                </div>
                            </div>
                    </div>
                @else
                    <div>
                        @switch($history->previous_status_id)
                            @case(7)
                                <i class="fas fa-cart-plus bg-info"></i>
                                @break
                            @case(2)
                                <i class="fas fa-pause-circle bg-secondary"></i>
                                @break
                            @case(3)
                                <i class="fas fa-check-double bg-success"></i>
                                @break
                            @case(4)
                            <i class="fas fa-exclamation bg-warning text-white"></i>
                                @break
                            @case(5)
                                <i class="fas fa-times-circle bg-danger"></i>
                                @break
                            @default
                        @endswitch
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at }}</span>
                            <h3 class="timeline-header">{{ $history->User->name }}</h3>
                            <div class="timeline-body">
                                @switch($history->previous_status_id)
                                    @case(7)
                                        <p><span class="text-info">COMENTARIO: </span> {{ $history->comment }}</p>
                                        <p><span class="text-info">Estado: </span> {{ $history->PrevStatus->name }}</p>
                                        @break
                                    @case(2)
                                        <p><span class="text-secondary">COMENTARIO: </span> {{ $history->comment }}</p>
                                        <p><span class="text-secondary">ARTÍCULOS ENTREGADOS: </span> {{ $history->delivered_articles }}</p>
                                        <p><span class="text-secondary">Estado: </span> {{ $history->PrevStatus->name }}</p>
                                        @break
                                    @case(3)
                                        <p><span class="text-success">COMENTARIO: </span> {{ $history->comment }}</p>
                                        <p><span class="text-success">ARTÍCULOS ENTREGADOS: </span> {{ $history->delivered_articles }}</p>
                                        <p><span class="text-success">Estado: </span> {{ $history->PrevStatus->name }}</p>
                                        @break
                                    @case(4)
                                        <p><span class="text-warning">COMENTARIO: </span> {{ $history->comment }}</p>
                                        <p><span class="text-warning">ARTÍCULOS ENTREGADOS: </span> {{ $history->delivered_articles }}</p>
                                        <p><span class="text-warning">Estado: </span> {{ $history->PrevStatus->name }}</p>
                                        @break
                                    @case(5)
                                        <p><span class="text-danger">COMENTARIO: </span> {{ $history->comment }}</p>
                                        <p><span class="text-danger">Estado: </span> {{ $history->PrevStatus->name }}</p>
                                        @break
                                    @default
                                @endswitch
                            </div>
                            <div class="timeline-footer">
                            </div>
                        </div>
                    </div>
                @endif
           
            @endforeach
        </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')

@stop