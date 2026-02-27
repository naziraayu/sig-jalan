@php
    $colors = [
        'Baik'         => ['bg' => '#2ecc71', 'text' => '#ffffff'],
        'Sedang'       => ['bg' => '#f1c40f', 'text' => '#000000'],
        'Rusak Ringan' => ['bg' => '#e67e22', 'text' => '#ffffff'],
        'Rusak Berat'  => ['bg' => '#e74c3c', 'text' => '#ffffff'],
    ];

    $bgColor   = $colors[$category]['bg']   ?? '#6c757d';
    $textColor = $colors[$category]['text'] ?? '#ffffff';
    $sdiValue  = $sdiValue ?? null;
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