@props(['icon', 'title', 'value', 'subtitle'])

<div class="col-md-3">
    <div class="card border-0 shadow-sm rounded-3 h-100">
        <div class="card-body">
            <div class="d-flex align-items-center mb-3">
                <i class="bi {{ $icon }} text-secondary me-2"></i>
                <span class="text-secondary fw-medium" style="font-size: 0.9rem;">{{ $title }}</span>
            </div>
            <h2 class="fw-bold text-pet-green mb-1">{{ $value }}</h2>
            <small class="text-muted">{!! $subtitle !!}</small>
        </div>
    </div>
</div>
