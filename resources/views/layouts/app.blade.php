<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Sumber Alam Jaya</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tom-select@2/dist/css/tom-select.min.css">
    <style>
        .ts-wrapper .ts-control { border: 1px solid #e2e8f0; border-radius: 0.5rem; padding: 0.625rem 1rem; font-size: 0.875rem; line-height: 1.25rem; height: 2.5rem; min-height: 0; overflow: hidden; box-shadow: none; background: #fff; }
        .ts-wrapper.focus .ts-control { border-color: #3b5bdb; box-shadow: 0 0 0 3px rgba(59,91,219,.12); }
        .ts-wrapper .ts-control input { font-size: 0.875rem; }
        .ts-dropdown { border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 16px rgba(0,0,0,.08); margin-top: 2px; }
        .ts-dropdown .ts-dropdown-content { max-height: 220px; }
        .ts-dropdown .option { font-size: 0.875rem; padding: 0.45rem 0.75rem; }
        .ts-dropdown .option.active { background: #eef2ff; color: #1e40af; }
        .ts-dropdown-input-wrap { padding: 0.35rem 0.5rem; border-bottom: 1px solid #f1f5f9; }
        .ts-dropdown-input-wrap input { width: 100%; border: 1px solid #e2e8f0; border-radius: 0.375rem; padding: 0.35rem 0.6rem; font-size: 0.8125rem; outline: none; }
    </style>
</head>
<body class="min-h-screen bg-app-glow">
    <div class="relative min-h-screen lg:grid lg:grid-cols-[280px_minmax(0,1fr)]">
        @include('partials.sidebar')

        <div class="min-w-0">
            @include('partials.topbar')

            <main class="px-4 pb-8 pt-6 sm:px-6 lg:px-10">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ── Sidebar ──────────────────────────────────────────────
            const sidebar = document.querySelector('[data-sidebar]');
            const overlay = document.querySelector('[data-sidebar-overlay]');
            const openButton = document.querySelector('[data-sidebar-open]');
            const closeButton = document.querySelector('[data-sidebar-close]');

            const openSidebar = () => {
                sidebar?.classList.remove('-translate-x-full');
                overlay?.classList.remove('hidden');
            };
            const closeSidebar = () => {
                sidebar?.classList.add('-translate-x-full');
                overlay?.classList.add('hidden');
            };

            openButton?.addEventListener('click', openSidebar);
            closeButton?.addEventListener('click', closeSidebar);
            overlay?.addEventListener('click', closeSidebar);

            // ── Confirm (native browser dialog) ───────────────────────
            document.addEventListener('submit', function (e) {
                const form = e.target;
                if (!form.dataset.confirm) return;
                if (!window.confirm(form.dataset.confirm)) e.preventDefault();
            });

            // ── Bulk Select ───────────────────────────────────────────
            function updateBulkBar() {
                const checked = document.querySelectorAll('[data-row-cb]:checked');
                const bulkBar = document.querySelector('[data-bulk-bar]');
                const countEl = document.querySelector('[data-bulk-count]');
                const selectAll = document.querySelector('[data-select-all]');
                const total = document.querySelectorAll('[data-row-cb]').length;

                if (bulkBar) {
                    if (checked.length > 0) {
                        bulkBar.classList.remove('hidden');
                        bulkBar.classList.add('flex');
                    } else {
                        bulkBar.classList.add('hidden');
                        bulkBar.classList.remove('flex');
                    }
                }
                if (countEl) countEl.textContent = checked.length;
                if (selectAll) {
                    selectAll.indeterminate = checked.length > 0 && checked.length < total;
                    selectAll.checked = total > 0 && checked.length === total;
                }
            }

            document.addEventListener('change', function (e) {
                if (e.target.matches('[data-select-all]')) {
                    document.querySelectorAll('[data-row-cb]').forEach(cb => cb.checked = e.target.checked);
                    updateBulkBar();
                } else if (e.target.matches('[data-row-cb]')) {
                    updateBulkBar();
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2/dist/js/tom-select.complete.min.js"></script>
    @stack('scripts')
</body>
</html>
