@php
    $bgColor = $bgColor ?? '#6c757d';
    $textColor = $textColor ?? '#ffffff';
    $sdiValue = $sdiValue ?? null;
@endphp
<span 
    {{ $attributes->merge([
        'class' => 'badge sdi-badge',
        'style' => "background-color: {$bgColor}; color: {$textColor}; border: none; padding: 0.375rem 0.75rem; font-size: 0.875rem; font-weight: 600; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
    ]) }}
>
    {{ $category }}
    @if($sdiValue !== null)
        <small style="opacity: 0.95; margin-left: 0.35rem; font-weight: 500;">
            ({{ is_numeric($sdiValue) ? number_format($sdiValue, 2) : $sdiValue }})
        </small>
    @endif
</span>