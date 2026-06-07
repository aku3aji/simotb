@php
    $currentUser = auth()->user();
    $avatarText = collect(explode(' ', $currentUser->name ?? 'P U'))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
    $settingsUrl = $currentUser->isOwner()
        ? route('pengguna.user.index')
        : route('profil.edit');
@endphp

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="flex items-center gap-3 px-4 py-4 sm:px-6 lg:px-10">
        <button type="button" class="btn btn-secondary px-2.5 py-2 lg:hidden" data-sidebar-open>
            <x-ui.icon name="menu" class="h-4 w-4" />
        </button>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">

            {{-- Notification Bell --}}
            <div class="relative hidden sm:block" id="notifWrapper">
                <button type="button" id="notifBtn"
                    class="btn btn-secondary relative px-2.5 py-2"
                    title="Notifikasi">
                    <x-ui.icon name="bell" class="h-4 w-4" />
                    <span id="notifBadge"
                        class="absolute -right-1 -top-1 hidden h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">
                        0
                    </span>
                </button>

                {{-- Dropdown --}}
                <div id="notifDropdown"
                    class="absolute right-0 top-full z-50 mt-2 hidden w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <p class="text-sm font-bold text-slate-900">Notifikasi</p>
                        <button type="button" id="notifBacaSemua"
                            class="text-xs font-semibold text-brand-700 hover:underline">
                            Tandai semua dibaca
                        </button>
                    </div>
                    <div id="notifList" class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                        <p class="px-4 py-6 text-center text-sm text-slate-400">Memuat notifikasi...</p>
                    </div>
                    <div class="border-t border-slate-100 px-4 py-2 text-center">
                        <span class="text-xs text-slate-400">20 notifikasi terakhir ditampilkan</span>
                    </div>
                </div>
            </div>

            {{-- Settings --}}
            <a href="{{ $settingsUrl }}" class="btn btn-secondary hidden px-2.5 py-2 sm:inline-flex" title="Pengaturan">
                <x-ui.icon name="settings" class="h-4 w-4" />
            </a>

            <div class="hidden h-10 w-px bg-slate-200 sm:block"></div>

            <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-slate-900">{{ $currentUser->name }}</p>
                    <p class="text-xs text-slate-500">{{ ucfirst($currentUser->role) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 font-bold text-white">
                    {{ $avatarText ?: 'PU' }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 transition hover:text-slate-700" title="Logout">
                        <x-ui.icon name="log-out" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@once
    @push('scripts')
        <script>
        (function () {
            const btn       = document.getElementById('notifBtn');
            const dropdown  = document.getElementById('notifDropdown');
            const badge     = document.getElementById('notifBadge');
            const list      = document.getElementById('notifList');
            const bacaBtn   = document.getElementById('notifBacaSemua');
            const dataUrl   = '{{ route('notifikasi.data') }}';
            const bacaUrl   = '{{ route('notifikasi.baca-semua') }}';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                              ?? '{{ csrf_token() }}';

            // Icon color map
            const warnaMap = {
                success : 'bg-emerald-500',
                danger  : 'bg-rose-500',
                warning : 'bg-amber-500',
                brand   : 'bg-brand-700',
            };

            // Module icon abbreviations
            const ikonMap = {
                'package'       : 'BR',
                'shopping-cart' : 'PJ',
                'receipt'       : 'PB',
                'user'          : 'PL',
                'truck'         : 'VD',
                'tag'           : 'KT',
                'ruler'         : 'ST',
                'bookmark'      : 'MK',
                'user-check'    : 'PG',
                'clock'         : 'AB',
                'boxes'         : 'SO',
                'rotate-ccw'    : 'RT',
                'wallet'        : 'PT',
                'shield'        : 'US',
                'bell'          : 'NT',
            };

            function renderList(items) {
                if (!items || items.length === 0) {
                    list.innerHTML = '<p class="px-4 py-8 text-center text-sm text-slate-400">Belum ada notifikasi.</p>';
                    return;
                }
                list.innerHTML = items.map(n => {
                    const bg    = warnaMap[n.warna] ?? 'bg-slate-500';
                    const abbr  = ikonMap[n.ikon] ?? '??';
                    const bg2   = n.dibaca ? 'bg-white' : 'bg-blue-50/60';
                    const dot   = n.dibaca ? '' : '<span class="absolute right-3 top-3 h-2 w-2 rounded-full bg-blue-500"></span>';
                    const href  = n.tautan ? `href="${n.tautan}"` : '';
                    const tag   = n.tautan ? 'a' : 'div';
                    return `
                        <${tag} ${href} class="relative flex items-start gap-3 px-4 py-3 ${bg2} hover:bg-slate-50 transition">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full ${bg} text-[11px] font-bold text-white mt-0.5">${abbr}</div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900 leading-tight">${escHtml(n.judul)}</p>
                                <p class="mt-0.5 text-xs text-slate-500 line-clamp-2">${escHtml(n.pesan)}</p>
                                <p class="mt-1 text-[11px] text-slate-400">${escHtml(n.waktu)}</p>
                            </div>
                            ${dot}
                        </${tag}>
                    `;
                }).join('');
            }

            function escHtml(str) {
                return String(str ?? '')
                    .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                    .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
            }

            function updateBadge(count) {
                if (count > 0) {
                    badge.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                } else {
                    badge.classList.add('hidden');
                    badge.classList.remove('flex');
                }
            }

            async function fetchCount() {
                try {
                    const res = await fetch(dataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    updateBadge(data.unread);
                } catch (_) {}
            }

            async function fetchAndRender() {
                list.innerHTML = '<p class="px-4 py-6 text-center text-sm text-slate-400">Memuat...</p>';
                try {
                    const res = await fetch(dataUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    updateBadge(data.unread);
                    renderList(data.items);
                } catch (_) {
                    list.innerHTML = '<p class="px-4 py-6 text-center text-sm text-rose-500">Gagal memuat notifikasi.</p>';
                }
            }

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const open = !dropdown.classList.contains('hidden');
                dropdown.classList.toggle('hidden', open);
                if (!open) fetchAndRender();
            });

            bacaBtn.addEventListener('click', async function () {
                await fetch(bacaUrl, {
                    method  : 'POST',
                    headers : { 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                });
                updateBadge(0);
                // refresh list to show all as read
                const items = list.querySelectorAll('[class*="bg-blue-50"]');
                items.forEach(el => el.classList.replace('bg-blue-50/60', 'bg-white'));
                list.querySelectorAll('.bg-blue-500').forEach(dot => dot.remove());
            });

            document.addEventListener('click', function (e) {
                if (!document.getElementById('notifWrapper').contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });

            // Poll unread count every 60 seconds
            fetchCount();
            setInterval(fetchCount, 60000);
        })();
        </script>
    @endpush
@endonce
