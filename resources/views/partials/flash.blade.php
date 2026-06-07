@if (session('success') || session('error'))
    <div class="mb-6 space-y-3">
        @if (session('success'))
            <div class="surface flex items-start gap-3 border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="surface flex items-start gap-3 border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <x-ui.icon name="alert-triangle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
                <p>{{ session('error') }}</p>
            </div>
        @endif
    </div>
@endif
