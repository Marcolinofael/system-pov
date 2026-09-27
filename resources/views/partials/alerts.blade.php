@foreach (['success' => 'check', 'error' => 'ban', 'warning' => 'exclamation-triangle'] as $tipo => $icone)
    @if (session($tipo))
        <div class="alert alert-{{ $tipo === 'error' ? 'danger' : $tipo }} alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="icon fas fa-{{ $icone }}"></i> {{ session($tipo) }}
        </div>
    @endif
@endforeach
