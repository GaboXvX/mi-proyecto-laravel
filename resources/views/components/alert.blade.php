@props(['type'])

@php
    switch ($type) {
        case 'success':
            $color = 'green';
            $message = $title;
            break;
        case 'error':
            $color = 'red';
            $message = $title;
            break;
        default:
            $color = 'red';
            $message = session('error');
            break;
    }
@endphp

@if ($message)
    <div style="color: {{ $color }}; font-weight: bold;">
        {{ $message }}
    </div>
@endif
