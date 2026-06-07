@props([
    'title',
    'value',
    'icon' => 'layout-dashboard',
    'variant' => 'brand',
    'hint' => null,
    'compact' => false,
])

@php
    $variantClasses = [
        'brand' => 'bg-brand-50 text-brand-700',
        'success' => 'bg-accent-50 text-accent-700',
        'danger' => 'bg-rose-50 text-rose-700',
        'warning' => 'bg-amber-50 text-amber-700',
    ][$variant] ?? 'bg-slate-100 text-slate-700';
@endphp

@if ($compact)
<div class="surface p-4">
    <div class="flex items-center gap-3">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $variantClasses }}">
            <x-ui.icon :name="$icon" class="h-5 w-5" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="truncate text-[11px] font-semibold uppercase tracking-[0.1em] text-slate-500">{{ $title }}</p>
            <p class="mt-1 truncate text-xl font-extrabold text-slate-900">{{ $value }}</p>
        </div>
        @if ($hint)
            <span class="badge {{ $variant === 'danger' ? 'badge-danger' : ($variant === 'success' ? 'badge-success' : 'badge-primary') }} shrink-0 text-[10px]">{{ $hint }}</span>
        @endif
    </div>
</div>
@else
<div class="surface p-5">
    <div class="flex items-start justify-between gap-4">
        <div class="space-y-3">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $variantClasses }}">
                <x-ui.icon :name="$icon" class="h-6 w-6" />
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-500">{{ $title }}</p>
                <p class="mt-2 text-3xl font-extrabold text-slate-900">{{ $value }}</p>
            </div>
        </div>

        @if ($hint)
            <span class="badge {{ $variant === 'danger' ? 'badge-danger' : ($variant === 'success' ? 'badge-success' : 'badge-primary') }}">{{ $hint }}</span>
        @endif
    </div>
</div>
@endif
