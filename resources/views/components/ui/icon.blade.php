@props([
    'name',
    'class' => 'h-5 w-5',
    'stroke' => 1.8,
])

<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('building')
            <path d="M3 21h18" />
            <path d="M5 21V7l7-4 7 4v14" />
            <path d="M9 9h.01M9 12h.01M9 15h.01M15 9h.01M15 12h.01M15 15h.01" />
            <path d="M10 21v-4h4v4" />
            @break
        @case('layout-dashboard')
            <rect x="3" y="3" width="7" height="7" rx="1" />
            <rect x="14" y="3" width="7" height="11" rx="1" />
            <rect x="14" y="17" width="7" height="4" rx="1" />
            <rect x="3" y="14" width="7" height="7" rx="1" />
            @break
        @case('package')
            <path d="M16.5 9.4 7.5 4.21" />
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-6-3.43a2 2 0 0 0-2 0l-6 3.43A2 2 0 0 0 5 8v8a2 2 0 0 0 1 1.73l6 3.43a2 2 0 0 0 2 0l6-3.43A2 2 0 0 0 21 16Z" />
            <path d="M3.3 7 12 12l8.7-5" />
            <path d="M12 22V12" />
            @break
        @case('boxes')
            <path d="M2.97 7.27 12 12l9.03-4.73" />
            <path d="M12 22V12" />
            <path d="m7.5 4.27 9 4.73" />
            <path d="m7.5 19.73-4.53-2.38A2 2 0 0 1 2 15.58V8.42a2 2 0 0 1 .97-1.77L7.5 4.27" />
            <path d="m16.5 19.73 4.53-2.38A2 2 0 0 0 22 15.58V8.42a2 2 0 0 0-.97-1.77L16.5 4.27" />
            <path d="M7.5 14.5 12 17l4.5-2.5" />
            @break
        @case('shopping-cart')
            <circle cx="9" cy="20" r="1" />
            <circle cx="18" cy="20" r="1" />
            <path d="M3 4h2l2.4 10.2a1 1 0 0 0 1 .8h9.9a1 1 0 0 0 1-.76L21 7H7" />
            @break
        @case('receipt')
            <path d="M4 3h16v18l-3-2-2 2-2-2-2 2-2-2-2 2-3-2V3Z" />
            <path d="M8 7h8M8 11h8M8 15h5" />
            @break
        @case('wallet')
            <path d="M19 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" />
            <path d="M3 7h15a3 3 0 1 1 0 6H13" />
            <path d="M16 10h.01" />
            @break
        @case('rotate-ccw')
            <path d="M3 2v6h6" />
            <path d="M3 8a9 9 0 1 0 2.64-3.36L3 8" />
            @break
        @case('users')
            <path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" />
            <circle cx="9.5" cy="7" r="4" />
            <path d="M20 8v6M23 11h-6" />
            @break
        @case('user-circle')
            <circle cx="12" cy="12" r="9" />
            <circle cx="12" cy="10" r="3" />
            <path d="M6.5 18a7 7 0 0 1 11 0" />
            @break
        @case('clipboard-list')
            <rect x="5" y="4" width="14" height="16" rx="2" />
            <path d="M9 2h6v4H9z" />
            <path d="M9 10h6M9 14h6M9 18h4" />
            @break
        @case('bar-chart-3')
            <path d="M3 20h18" />
            <path d="M7 16V8M12 16V4M17 16v-6" />
            @break
        @case('bell')
            <path d="M6 8a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9" />
            <path d="M10 21a2 2 0 0 0 4 0" />
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09a1.65 1.65 0 0 0-1-1.51 1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09a1.65 1.65 0 0 0 1.51-1 1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33h.01a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51h.01a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82v.01a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z" />
            @break
        @case('circle-help')
            <circle cx="12" cy="12" r="9" />
            <path d="M9.09 9a3 3 0 1 1 5.82 1c0 2-3 2-3 4" />
            <path d="M12 17h.01" />
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16" />
            @break
        @case('x')
            <path d="M18 6 6 18M6 6l12 12" />
            @break
        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break
        @case('search')
            <circle cx="11" cy="11" r="7" />
            <path d="m20 20-3.5-3.5" />
            @break
        @case('filter')
            <path d="M4 6h16" />
            <path d="M7 12h10" />
            <path d="M10 18h4" />
            @break
        @case('calendar')
            <rect x="3" y="5" width="18" height="16" rx="2" />
            <path d="M16 3v4M8 3v4M3 11h18" />
            @break
        @case('pencil')
            <path d="M12 20h9" />
            <path d="m16.5 3.5 4 4L7 21l-4 1 1-4L16.5 3.5Z" />
            @break
        @case('trash')
            <path d="M3 6h18" />
            <path d="M8 6V4h8v2" />
            <path d="m19 6-1 14H6L5 6" />
            <path d="M10 11v6M14 11v6" />
            @break
        @case('log-out')
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <path d="M16 17l5-5-5-5" />
            <path d="M21 12H9" />
            @break
        @case('check-circle')
            <circle cx="12" cy="12" r="9" />
            <path d="m9 12 2 2 4-4" />
            @break
        @case('alert-triangle')
            <path d="m10.3 3.9-7 12.1A2 2 0 0 0 5 19h14a2 2 0 0 0 1.73-3l-7-12.1a2 2 0 0 0-3.46 0Z" />
            <path d="M12 9v4M12 17h.01" />
            @break
        @case('file-text')
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
            <path d="M14 2v6h6" />
            <path d="M8 13h8M8 17h8M8 9h2" />
            @break
        @case('truck')
            <path d="M10 17h4V5H2v12h3" />
            <path d="M14 8h4l3 3v6h-3" />
            <circle cx="7.5" cy="17.5" r="2.5" />
            <circle cx="17.5" cy="17.5" r="2.5" />
            @break
        @case('chevron-right')
            <path d="m9 18 6-6-6-6" />
            @break
        @case('chevron-left')
            <path d="m15 18-6-6 6-6" />
            @break
        @case('chevron-down')
            <path d="m6 9 6 6 6-6" />
            @break
        @case('eye')
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
            <circle cx="12" cy="12" r="3" />
            @break
        @case('user')
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
            <circle cx="12" cy="7" r="4" />
            @break
        @case('user-check')
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
            <circle cx="9" cy="7" r="4" />
            <polyline points="16 11 18 13 22 9" />
            @break
        @case('tag')
            <path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z" />
            <circle cx="7.5" cy="7.5" r=".5" fill="currentColor" />
            @break
        @case('bookmark')
            <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z" />
            @break
        @case('clock')
            <circle cx="12" cy="12" r="10" />
            <polyline points="12 6 12 12 16 14" />
            @break
        @case('shield')
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            @break
        @case('ruler')
            <path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z" />
            <path d="m14.5 12.5 2-2M11.5 9.5l2-2M8.5 6.5l2-2" />
            @break
        @case('trash-2')
            <path d="M3 6h18" />
            <path d="M8 6V4h8v2" />
            <path d="m19 6-1 14H6L5 6" />
            @break
        @case('eye-off')
            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
            <line x1="2" x2="22" y1="2" y2="22" />
            @break
        @default
            <circle cx="12" cy="12" r="9" />
    @endswitch
</svg>
