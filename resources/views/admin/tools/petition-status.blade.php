@switch($status)
    @case(7)
        <a class="btn btn-sm btn-info text-white" style="cursor: default;"><i class="fas fa-cart-plus"></i> {{ $name }}</a>
        @break
    @case(2)
        <a class="btn btn-sm btn-secondary text-white" style="cursor: default;"><i class="fas fa-pause-circle"></i> {{ $name }}</a>
        @break
    @case(3)
        <a class="btn btn-sm btn-success text-white" style="cursor: default;"><i class="fas fa-check-double"></i> {{ $name }}</a>
        @break
    @case(4)
        <a class="btn btn-sm btn-warning text-white" style="cursor: default;"><i class="fas fa-exclamation"></i> {{ $name }}</a>
        @break
    @case(5)
        <a class="btn btn-sm btn-danger text-white" style="cursor: default;"><i class="fas fa-times-circle"></i> {{ $name }}</a>
        @break
    @default
@endswitch