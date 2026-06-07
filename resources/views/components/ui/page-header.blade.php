@props([
    'title',
    'description' => null,
])

<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
    <div>
        <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">{{ $title }}</h1>
        @if ($description)
            <p class="mt-2 max-w-3xl text-sm text-slate-600 sm:text-base">{{ $description }}</p>
        @endif
    </div>

    @if ($slot->isNotEmpty())
        <div class="flex flex-wrap items-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
