@if ($errors->any())
    <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 px-4 py-4 text-sm text-rose-800">
        <div class="flex items-start gap-3">
            <x-ui.icon name="alert-triangle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
            <div>
                <p class="font-semibold">Data belum bisa disimpan.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
