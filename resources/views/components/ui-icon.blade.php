@props(['name' => 'grid'])
@php
    $paths = [
        'grid' => 'M3 3h7v7H3z M14 3h7v7h-7z M3 14h7v7H3z M14 14h7v7h-7z',
        'users' => 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2 M16 3a4 4 0 0 1 0 8 M22 21v-2a4 4 0 0 0-3-3.87 M13 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0',
        'book' => 'M4 3h6a3 3 0 0 1 3 3v15a4 4 0 0 0-4-2H3V3z M13 6a3 3 0 0 1 3-3h5v16h-5a3 3 0 0 0-3 2',
        'calendar' => 'M8 2v4 M16 2v4 M3 10h18 M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2 M8 15l3 3 5-5',
        'clipboard' => 'M9 4H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2h-3 M9 2h6v4H9z M8 14l3 3 5-5',
        'menu' => 'M4 6h16 M4 12h16 M4 18h16',
        'chevron' => 'M9 5l7 7-7 7',
        'logout' => 'M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9',
        'shield' => 'M12 3l9 4v5c0 5-9 9-9 9s-9-4-9-9V7z M8 12l3 3 5-6',
        'briefcase' => 'M8 6V3h8v3 M3 6h18v14H3z M3 12a24 24 0 0 0 18 0 M10 12h4v3h-4z',
        'award' => 'M16 8a4 4 0 1 1-8 0 4 4 0 0 1 8 0 M8 12l-2 9 6-3 6 3-2-9',
        'chart' => 'M3 3v18h18 M7 16v-5 M12 16V7 M17 16v-9',
    ];
@endphp
<svg {{ $attributes->merge(['class' => 'ui-icon']) }} width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="{{ $paths[$name] ?? $paths['grid'] }}"/></svg>
