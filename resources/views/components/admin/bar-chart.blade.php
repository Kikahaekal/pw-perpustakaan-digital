@props(['labels' => [], 'values' => []])

@php
    $labels = array_values($labels);
    $values = array_map(fn ($value): int => (int) $value, array_values($values));
    $maxValue = max($values ?: [1]);
    $maxValue = $maxValue > 0 ? $maxValue : 1;
    $maxValue = (int) ceil($maxValue * 1.2);

    $width = 340;
    $height = 150;
    $left = 26;
    $right = 6;
    $top = 12;
    $bottom = 20;
    $plotWidth = $width - $left - $right;
    $plotHeight = $height - $top - $bottom;

    $count = count($values);
    $slotWidth = $count > 0 ? $plotWidth / $count : $plotWidth;
    $barWidth = max(3, $slotWidth * 0.55);

    $peakIndex = $values === [] ? null : array_search(max($values), $values, true);
    $peakIndex = is_int($peakIndex) ? $peakIndex : null;
    $gradientId = 'barGradient'.uniqid();
    $gridSteps = [0, 0.25, 0.5, 0.75, 1];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-auto w-full" role="img" aria-label="Grafik buku dipinjam per bulan">
        <defs>
            <linearGradient id="{{ $gradientId }}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#67e8f9"/>
                <stop offset="100%" stop-color="#2563eb"/>
            </linearGradient>
        </defs>

        @foreach ($gridSteps as $step)
            @php
                $gridY = round($top + $plotHeight - ($step * $plotHeight), 1);
            @endphp
            <line x1="{{ $left }}" y1="{{ $gridY }}" x2="{{ $width - $right }}" y2="{{ $gridY }}" stroke="#1f2a4d" stroke-width="0.6"/>
            <text x="{{ $left - 3 }}" y="{{ $gridY + 2 }}" text-anchor="end" font-size="5.5" fill="#7c8bb4">{{ round($maxValue * $step) }}</text>
        @endforeach

        @foreach ($values as $index => $value)
            @php
                $barHeight = round(($value / $maxValue) * $plotHeight, 1);
                $x = round($left + ($index * $slotWidth) + (($slotWidth - $barWidth) / 2), 1);
                $y = round($top + $plotHeight - $barHeight, 1);
            @endphp
            <rect x="{{ $x }}" y="{{ $y }}" width="{{ round($barWidth, 1) }}" height="{{ max($barHeight, 0.6) }}" rx="0.8"
                fill="url(#{{ $gradientId }})" opacity="{{ $index === $peakIndex ? 1 : 0.75 }}"/>
        @endforeach

        @if ($peakIndex !== null && isset($values[$peakIndex]))
            @php
                $peakHeight = round(($values[$peakIndex] / $maxValue) * $plotHeight, 1);
                $peakX = round($left + ($peakIndex * $slotWidth) + ($slotWidth / 2), 1);
            @endphp
            <text x="{{ min($peakX + 2, $width - 20) }}" y="{{ max($top + $plotHeight - $peakHeight - 4, 8) }}" font-size="6" font-weight="bold" fill="#a7f3d0">{{ $values[$peakIndex] }}</text>
        @endif

        @foreach ($labels as $index => $label)
            <text x="{{ round($left + ($index * $slotWidth) + ($slotWidth / 2), 1) }}" y="{{ $height - 6 }}" text-anchor="middle" font-size="5.5" fill="#7c8bb4">{{ $label }}</text>
        @endforeach
    </svg>
</div>
