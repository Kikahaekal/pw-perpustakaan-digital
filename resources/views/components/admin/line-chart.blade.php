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
    $stepX = $count > 1 ? $plotWidth / ($count - 1) : 0;

    $points = [];
    foreach ($values as $index => $value) {
        $points[] = [
            round($left + ($index * $stepX), 1),
            round($top + $plotHeight - (($value / $maxValue) * $plotHeight), 1),
        ];
    }

    $linePath = implode(' ', array_map(fn (array $point): string => $point[0].','.$point[1], $points));
    $areaPath = $count > 0
        ? $left.','.($top + $plotHeight).' '.$linePath.' '.end($points)[0].','.($top + $plotHeight)
        : '';

    $peakIndex = $values === [] ? null : array_search(max($values), $values, true);
    $peakIndex = is_int($peakIndex) ? $peakIndex : null;
    $gradientId = 'lineGradient'.uniqid();
    $gridSteps = [0, 0.25, 0.5, 0.75, 1];
@endphp

<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <svg viewBox="0 0 {{ $width }} {{ $height }}" class="h-auto w-full" role="img" aria-label="Grafik pengguna aktif bulanan">
        <defs>
            <linearGradient id="{{ $gradientId }}" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="#38bdf8" stop-opacity="0.55"/>
                <stop offset="100%" stop-color="#0b1530" stop-opacity="0"/>
            </linearGradient>
        </defs>

        @foreach ($gridSteps as $step)
            @php
                $gridY = round($top + $plotHeight - ($step * $plotHeight), 1);
            @endphp
            <line x1="{{ $left }}" y1="{{ $gridY }}" x2="{{ $width - $right }}" y2="{{ $gridY }}" stroke="#1f2a4d" stroke-width="0.6"/>
            <text x="{{ $left - 3 }}" y="{{ $gridY + 2 }}" text-anchor="end" font-size="5.5" fill="#7c8bb4">{{ round($maxValue * $step) }}</text>
        @endforeach

        @if ($areaPath !== '')
            <polygon points="{{ $areaPath }}" fill="url(#{{ $gradientId }})"/>
            <polyline points="{{ $linePath }}" fill="none" stroke="#38bdf8" stroke-width="1.6" stroke-linejoin="round" stroke-linecap="round"/>
        @endif

        @if ($peakIndex !== null && isset($points[$peakIndex]))
            @php
                $peak = $points[$peakIndex];
            @endphp
            <circle cx="{{ $peak[0] }}" cy="{{ $peak[1] }}" r="2.2" fill="#38bdf8" stroke="#e0f2fe" stroke-width="0.8"/>
            <text x="{{ min($peak[0] + 3, $width - 34) }}" y="{{ max($peak[1] - 4, 8) }}" font-size="6" font-weight="bold" fill="#a7f3d0">{{ $values[$peakIndex] }}</text>
        @endif

        @foreach ($labels as $index => $label)
            <text x="{{ round($left + ($index * $stepX), 1) }}" y="{{ $height - 6 }}" text-anchor="middle" font-size="5.5" fill="#7c8bb4">{{ $label }}</text>
        @endforeach
    </svg>
</div>
