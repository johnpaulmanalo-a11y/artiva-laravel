@props(['compact' => false])

@php
    $flashMessages = collect([
        'success' => ['variant' => 'success', 'icon' => 'bi-check-circle'],
        'error' => ['variant' => 'danger', 'icon' => 'bi-exclamation-octagon'],
        'info' => ['variant' => 'info', 'icon' => 'bi-info-circle'],
        'status' => ['variant' => 'info', 'icon' => 'bi-envelope-check'],
    ])->filter(fn ($meta, $key) => session()->has($key));
@endphp

@if ($flashMessages->isNotEmpty() || $errors->any())
    <div class="flash-stack {{ $compact ? 'flash-stack--compact' : '' }}">
        @foreach ($flashMessages as $key => $meta)
            <div class="alert alert-{{ $meta['variant'] }} flash-alert d-flex align-items-start gap-2" role="alert" data-flash>
                <i class="bi {{ $meta['icon'] }} mt-1"></i>
                <div class="flex-grow-1">{{ session($key) }}</div>
                <button type="button" class="flash-close" data-dismiss-alert aria-label="Dismiss message">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        @endforeach

        @if ($errors->any())
            <div class="alert alert-danger flash-alert" role="alert" data-flash>
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-triangle mt-1"></i>
                    <div class="flex-grow-1">
                        <strong class="d-block mb-1">Please fix the following:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="flash-close" data-dismiss-alert aria-label="Dismiss message">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>
@endif
