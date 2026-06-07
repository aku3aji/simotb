@props([
    'title' => 'Belum ada data',
    'description' => 'Data akan muncul di sini setelah Anda menambahkannya.',
    'icon' => 'file-text',
])

<div class="flex flex-col items-center justify-center px-6 py-14 text-center">
    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
        <x-ui.icon :name="$icon" class="h-7 w-7" />
    </div>
    <h3 class="text-lg font-bold text-slate-900">{{ $title }}</h3>
    <p class="mt-2 max-w-md text-sm text-slate-500">{{ $description }}</p>
</div>
