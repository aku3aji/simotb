@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-ui.page-header title="Ringkasan Operasional" description="Pantau performa inventaris, transaksi, dan piutang toko secara real-time.">
        <div class="rounded-md border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">
            <span id="live-clock" class="tabular-nums">–</span>
        </div>
    </x-ui.page-header>

    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        <x-ui.stat-card
            title="Penjualan Bersih Bulan Ini"
            :value="'Rp ' . number_format($totalPenjualan, 0, ',', '.')"
            icon="shopping-cart"
            variant="success"
            hint="Net"
            :compact="true"
        />
        <x-ui.stat-card
            title="Total Pembelian Bulan Ini"
            :value="'Rp ' . number_format($totalPembelian, 0, ',', '.')"
            icon="receipt"
            variant="brand"
            :compact="true"
        />
        <x-ui.stat-card
            title="Total Retur Bulan Ini"
            :value="'Rp ' . number_format($totalReturBulanIni, 0, ',', '.')"
            icon="rotate-ccw"
            variant="danger"
            :compact="true"
        />
        <x-ui.stat-card
            title="Stok Menipis"
            :value="$stokMenipis . ' item'"
            icon="alert-triangle"
            variant="danger"
            hint="Cek"
            :compact="true"
        />
        <x-ui.stat-card
            title="Total Piutang"
            :value="'Rp ' . number_format($totalPiutang, 0, ',', '.')"
            icon="wallet"
            variant="warning"
            :compact="true"
        />
    </div>

    <section class="surface mt-4 overflow-hidden">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 px-6 py-5">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900">Grafik Arus Transaksi 30 Hari Terakhir</h2>
                <p class="mt-1 text-sm text-slate-500">Perbandingan nilai harian penjualan, pembelian, retur, dan pembayaran piutang.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4 text-sm">
                <span class="flex items-center gap-1.5"><span class="inline-block h-3 w-3 rounded-full bg-emerald-500"></span> Penjualan</span>
                <span class="flex items-center gap-1.5"><span class="inline-block h-3 w-3 rounded-full bg-blue-500"></span> Pembelian</span>
                <span class="flex items-center gap-1.5"><span class="inline-block h-3 w-3 rounded-full bg-rose-500"></span> Retur</span>
                <span class="flex items-center gap-1.5"><span class="inline-block h-3 w-3 rounded-full bg-amber-500"></span> Bayar Piutang</span>
            </div>
        </div>
        <div class="px-6 py-5">
            <canvas id="chartTransaksi" height="80"></canvas>
        </div>
    </section>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(360px,0.9fr)]">
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-2xl font-extrabold text-slate-900">Aktivitas Transaksi Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500">Lima transaksi terakhir yang masuk ke sistem.</p>
            </div>

            @if ($penjualanTerbaru->isEmpty())
                <x-ui.empty-state title="Belum ada transaksi penjualan" description="Transaksi terbaru akan muncul di sini setelah admin mulai mencatat penjualan." icon="shopping-cart" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualanTerbaru as $item)
                                <tr>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</div>
                                        <div class="mt-1 text-xs text-slate-500">Dicatat oleh {{ $item->user->name ?? '-' }}</div>
                                    </td>
                                    <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="font-semibold text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $item->status_pembayaran === 'lunas' ? 'badge-success' : ($item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                            {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="space-y-6">
            <div class="surface p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">Peringatan Stok</h2>
                        <p class="mt-1 text-sm text-slate-500">Barang yang mendekati atau sudah melewati batas minimum.</p>
                    </div>
                    <span class="badge badge-danger">{{ $barangMenipis->count() }} item</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($barangMenipis as $item)
                        <div class="rounded-lg border border-slate-200 px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $item->nama }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $item->kode_barang }} • {{ $item->kategori->nama ?? '-' }} • {{ $item->merek->nama ?? 'Tanpa merek' }}</p>
                                </div>
                                <span class="badge {{ $item->stok <= 0 ? 'badge-danger' : 'badge-warning' }}">
                                    {{ $item->stok <= 0 ? 'Habis' : 'Menipis' }}
                                </span>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Stok saat ini</span>
                                <span class="font-bold text-slate-900">{{ $item->stok }} {{ $item->satuan->singkatan ?? $item->satuan->nama ?? '' }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Batas minimum</span>
                                <span class="font-semibold text-slate-700">{{ $item->stok_minimum }}</span>
                            </div>
                        </div>
                    @empty
                        <x-ui.empty-state title="Stok masih aman" description="Belum ada barang yang masuk kategori menipis." icon="check-circle" />
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const ctx = document.getElementById('chartTransaksi');
            if (!ctx) return;

            const labels   = @json($chartLabels);
            const penjualan = @json($chartValuesPenjualan);
            const pembelian = @json($chartValuesPembelian);
            const retur     = @json($chartValuesRetur);
            const piutang   = @json($chartValuesPiutang);

            function makeDataset(label, data, color) {
                return {
                    label,
                    data,
                    borderColor: color,
                    backgroundColor: color.replace('1)', '0.08)'),
                    borderWidth: 2,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                    fill: true,
                    tension: 0.4,
                };
            }

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        makeDataset('Penjualan',       penjualan, 'rgba(16, 185, 129, 1)'),
                        makeDataset('Pembelian',       pembelian, 'rgba(59, 130, 246, 1)'),
                        makeDataset('Retur',           retur,     'rgba(239, 68, 68, 1)'),
                        makeDataset('Bayar Piutang',   piutang,   'rgba(245, 158, 11, 1)'),
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#94a3b8', maxRotation: 45 }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            beginAtZero: true,
                            ticks: {
                                font: { size: 11 },
                                color: '#94a3b8',
                                callback: function (value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>
    <script>
        (function () {
            const el = document.getElementById('live-clock');
            if (!el) return;

            const bulan = ['Januari','Februari','Maret','April','Mei','Juni',
                           'Juli','Agustus','September','Oktober','November','Desember'];
            const hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

            function pad(n) { return String(n).padStart(2, '0'); }

            function tick() {
                const d = new Date();
                el.textContent =
                    hari[d.getDay()] + ', ' +
                    d.getDate() + ' ' + bulan[d.getMonth()] + ' ' + d.getFullYear() +
                    ' — ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + ':' + pad(d.getSeconds());
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endpush

@endsection
