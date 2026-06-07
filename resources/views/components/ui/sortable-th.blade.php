@props(['column', 'label', 'sortBy', 'sortDir'])

@php
    $isActive = $sortBy === $column;
    $nextDir = ($isActive && $sortDir === 'asc') ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery(['sort' => $column, 'dir' => $nextDir, 'page' => 1]);
@endphp

<th>
    <a href="{{ $url }}" class="inline-flex items-center gap-1 whitespace-nowrap hover:text-slate-900 {{ $isActive ? 'text-slate-900' : '' }}">
        <span>{{ $label }}</span>
        @if ($isActive)
            <x-ui.icon name="{{ $sortDir === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="h-3.5 w-3.5 text-brand-600" />
        @else
            <x-ui.icon name="chevrons-up-down" class="h-3.5 w-3.5 text-slate-300" />
        @endif
    </a>
</th>
