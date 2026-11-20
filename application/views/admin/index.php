<?php
$is_admin = $user['role_id'] == '1';

// Tanggal hari ini dalam Bahasa Indonesia
$hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$today = $hari[(int) date('w')] . ', ' . date('j') . ' ' . $bulan[(int) date('n')] . ' ' . date('Y');

$condition_total = array_sum($condition_counts);
$condition_tone  = [1 => 'success', 2 => 'warning', 3 => 'danger'];

$damaged = array_values(array_filter($condition_locations, function ($l) {
    return $l[2] + $l[3] > 0;
}));
?>
<div class="container-fluid dash">

    <div class="page-head enter" style="--d: 0ms">
        <div>
            <div class="eyebrow"><?= $today; ?></div>
            <h1>Ringkasan aset</h1>
            <p class="lede">Halo, <?= html_escape($user['username']); ?>. Ini posisi aset <?= $is_admin ? 'di semua gedung' : 'di gedung Anda'; ?> hari ini: jumlah, kondisi, dan perpindahan terakhir.</p>
        </div>
        <div class="actions">
            <a href="<?= base_url('report'); ?>" class="btn btn-secondary"><i class="fas fa-chart-bar" aria-hidden="true"></i> Rekap aset</a>
            <a href="<?= base_url('transaction'); ?>" class="btn btn-primary"><i class="fas fa-exchange-alt" aria-hidden="true"></i> Pindahkan aset</a>
        </div>
    </div>

    <!-- Angka utama -->
    <div class="card kpi-strip enter mb-4" style="--d: 60ms">
        <a class="kpi" href="<?= base_url('asset'); ?>">
            <span class="kpi-label"><i class="fas fa-box" aria-hidden="true"></i> Total aset</span>
            <span class="kpi-value num"><?= number_format((int) $summaries['total_assets'], 0, ',', '.'); ?></span>
            <span class="kpi-foot">Buka data aset <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="kpi" href="<?= base_url('History'); ?>">
            <span class="kpi-label"><i class="fas fa-exchange-alt" aria-hidden="true"></i> Transaksi pindah</span>
            <span class="kpi-value num"><?= number_format((int) $summaries['total_transactions'], 0, ',', '.'); ?></span>
            <span class="kpi-foot">Lihat riwayat <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="kpi" href="<?= base_url('buildings'); ?>">
            <span class="kpi-label"><i class="fas fa-door-open" aria-hidden="true"></i> Ruangan</span>
            <span class="kpi-value num"><?= number_format((int) $summaries['total_rooms'], 0, ',', '.'); ?></span>
            <span class="kpi-foot">Gedung &amp; ruangan <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        <?php if ($is_admin) : ?>
            <a class="kpi" href="<?= base_url('admin/list_user'); ?>">
                <span class="kpi-label"><i class="fas fa-users" aria-hidden="true"></i> Pengguna</span>
                <span class="kpi-value num"><?= number_format((int) $summaries['total_users'], 0, ',', '.'); ?></span>
                <span class="kpi-foot">Kelola pengguna <i class="fas fa-arrow-right" aria-hidden="true"></i></span>
            </a>
        <?php else : ?>
            <div class="kpi">
                <span class="kpi-label"><i class="fas fa-users" aria-hidden="true"></i> Pengguna</span>
                <span class="kpi-value num"><?= number_format((int) $summaries['total_users'], 0, ',', '.'); ?></span>
                <span class="kpi-foot">Akun aktif di SIMANIS</span>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <!-- Kondisi aset -->
        <div class="col-xl-5 mb-4">
            <div class="card h-100 enter" style="--d: 120ms">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Kondisi aset</h6>
                    <span class="small text-muted"><span class="num"><?= number_format($condition_total, 0, ',', '.'); ?></span> aset aktif</span>
                </div>
                <div class="card-body">
                    <div class="cond-bar" role="img" aria-label="Sebaran kondisi aset">
                        <?php foreach (asset_condition_options() as $value => $label) : ?>
                            <?php
                            $count = $condition_counts[$value] ?? 0;
                            $pct   = $condition_total > 0 ? $count / $condition_total * 100 : 0;
                            ?>
                            <?php if ($pct > 0) : ?>
                                <span class="seg seg-<?= $condition_tone[$value]; ?>" style="width: <?= round($pct, 2); ?>%"></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <ul class="cond-list">
                        <?php foreach (asset_condition_options() as $value => $label) : ?>
                            <?php
                            $count = $condition_counts[$value] ?? 0;
                            $pct   = $condition_total > 0 ? round($count / $condition_total * 100) : 0;
                            ?>
                            <li>
                                <a href="<?= base_url('asset?condition=' . $value); ?>">
                                    <span class="dot dot-<?= $condition_tone[$value]; ?>" aria-hidden="true"></span>
                                    <span class="cond-name"><?= $label; ?></span>
                                    <span class="cond-pct num"><?= $pct; ?>%</span>
                                    <span class="cond-count num"><?= number_format($count, 0, ',', '.'); ?></span>
                                    <i class="fas fa-chevron-right" aria-hidden="true"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Transaksi per bulan -->
        <div class="col-xl-7 mb-4">
            <div class="card h-100 enter" style="--d: 180ms">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Perpindahan aset per bulan</h6>
                    <span class="small text-muted">12 bulan terakhir</span>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 260px;">
                        <canvas id="monthlyChart" aria-label="Grafik transaksi pindah aset per bulan" role="img"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistik aset per nama -->
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap: 8px;">
                    <h6 class="m-0">Jumlah aset per jenis</h6>
                    <form action="<?= base_url('admin/index'); ?>" method="POST" class="form-inline">
                        <label for="dashRoom" class="sr-only">Saring ruangan</label>
                        <select id="dashRoom" name="room_id" class="form-control form-control-sm filter-room" onchange="this.form.submit()">
                            <option value="">Semua ruangan</option>
                            <?php foreach ($rooms as $room) : ?>
                                <option value="<?= $room['id']; ?>" <?= ($room_id == $room["id"]) ? 'selected' : ''; ?>>
                                    <?= html_escape($room['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>
                <div class="card-body">
                    <?php if (empty($assets)) : ?>
                        <div class="empty-state">
                            <i class="fas fa-box-open" aria-hidden="true"></i>
                            Belum ada aset di ruangan ini.
                        </div>
                    <?php else : ?>
                        <div style="position: relative; height: 300px;">
                            <canvas id="myChart" aria-label="Grafik jumlah aset per jenis" role="img"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Kategori -->
        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="m-0">Aset per kategori</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($asset_categories)) : ?>
                        <div class="empty-state">
                            <i class="fas fa-tags" aria-hidden="true"></i>
                            Belum ada kategori yang dipakai.
                        </div>
                    <?php else : ?>
                        <div style="position: relative; height: 180px;">
                            <canvas id="categoryChart" aria-label="Grafik aset per kategori" role="img"></canvas>
                        </div>
                        <ul class="legend-list mt-3">
                            <?php foreach ($asset_categories as $i => $cat) : ?>
                                <li>
                                    <span class="swatch" data-series="<?= $i; ?>" aria-hidden="true"></span>
                                    <span class="legend-name"><?= html_escape($cat['label']); ?></span>
                                    <span class="num"><?= (int) $cat['total']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Aktivitas terkini -->
        <div class="col-xl-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Perpindahan terakhir</h6>
                    <a href="<?= base_url('History'); ?>" class="small font-weight-bold">Semua riwayat</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover recent-table">
                            <thead>
                                <tr>
                                    <th>Aset</th>
                                    <th>Rute</th>
                                    <th>Kode &amp; tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recent_transactions)) : ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state">
                                                <i class="fas fa-route" aria-hidden="true"></i>
                                                Belum ada aset yang dipindahkan.
                                            </div>
                                        </td>
                                    </tr>
                                <?php else : ?>
                                    <?php $trx_status = [0 => ['warning', 'Dikirim'], 1 => ['success', 'Diterima'], 2 => ['secondary', 'Ditolak']]; ?>
                                    <?php foreach ($recent_transactions as $trans) : ?>
                                        <?php $st = $trx_status[(int) $trans['status']] ?? ['light', '-']; ?>
                                        <tr>
                                            <td>
                                                <div class="font-weight-bold text-gray-800"><?= html_escape($trans['asset_name'] ?? '-') ?></div>
                                                <span class="code-tag"><?= html_escape($trans['serial_number'] ?? '-') ?></span>
                                            </td>
                                            <td class="route">
                                                <span><?= html_escape($trans['source_name'] ?? '-') ?></span>
                                                <i class="fas fa-long-arrow-alt-right" aria-label="ke"></i>
                                                <span><?= html_escape($trans['destination_name'] ?? '-') ?></span>
                                            </td>
                                            <td class="text-nowrap">
                                                <div class="num small text-gray-800"><?= html_escape($trans['trx_code'] ?? '-') ?></div>
                                                <div class="small text-muted"><?= date('d M Y, H:i', strtotime($trans['created_at'])) ?></div>
                                            </td>
                                            <td><span class="badge badge-<?= $st[0] ?>"><?= $st[1] ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aset rusak -->
        <div class="col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h6 class="m-0">Aset rusak per <?= $is_admin ? 'gedung' : 'ruangan'; ?></h6>
                    <a href="<?= base_url('report?group=' . ($is_admin ? 'building' : 'room')); ?>" class="small font-weight-bold">Rekap</a>
                </div>
                <div class="card-body">
                    <?php if (empty($damaged)) : ?>
                        <div class="empty-state">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            Tidak ada aset rusak. Semua aset aktif dalam kondisi baik.
                        </div>
                    <?php else : ?>
                        <div style="position: relative; height: <?= max(220, min(300, count($damaged) * 34 + 70)); ?>px;">
                            <canvas id="damagedChart" aria-label="Grafik aset rusak" role="img"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var C = window.SIMANIS_COLORS;
        var grid = { color: C.line, zeroLineColor: C.line, drawBorder: false };
        var wholeNumber = function (v) { return v % 1 === 0 ? v : ''; };

        // Warna swatch legenda kategori mengikuti palet seri
        document.querySelectorAll('.legend-list .swatch').forEach(function (el) {
            el.style.background = C.series[el.getAttribute('data-series') % C.series.length];
        });

        // Jumlah aset per jenis
        var assetCanvas = document.getElementById('myChart');
        if (assetCanvas) {
            new Chart(assetCanvas, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($assets, 'asset_name')); ?>,
                    datasets: [{
                        label: 'Jumlah aset',
                        data: <?= json_encode(array_map('intval', array_column($assets, 'total'))); ?>,
                        backgroundColor: C.accent,
                        hoverBackgroundColor: '#0a4757'
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    scales: {
                        xAxes: [{ gridLines: { display: false, drawBorder: false }, barPercentage: .6, ticks: { maxRotation: 0, autoSkip: true } }],
                        yAxes: [{ ticks: { beginAtZero: true, padding: 8, callback: wholeNumber }, gridLines: grid }]
                    }
                }
            });
        }

        // Aset per kategori
        var catCanvas = document.getElementById('categoryChart');
        if (catCanvas) {
            var catData = <?= json_encode(array_map('intval', array_column($asset_categories, 'total'))); ?>;
            new Chart(catCanvas, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode(array_column($asset_categories, 'label')); ?>,
                    datasets: [{
                        data: catData,
                        backgroundColor: catData.map(function (_, i) { return C.series[i % C.series.length]; }),
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: { display: false },
                    cutoutPercentage: 72
                }
            });
        }

        // Transaksi pindah aset per bulan (dikirim / diterima / ditolak)
        var monthly = <?= json_encode($monthly_transactions); ?>;
        new Chart(document.getElementById('monthlyChart'), {
            type: 'bar',
            data: {
                labels: monthly.labels,
                datasets: [
                    { label: 'Diterima', backgroundColor: C.accent, data: monthly[1] },
                    { label: 'Menunggu', backgroundColor: C.warning, data: monthly[0] },
                    { label: 'Ditolak', backgroundColor: '#cfcbbf', data: monthly[2] }
                ]
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom', labels: { padding: 16 } },
                scales: {
                    xAxes: [{ stacked: true, barPercentage: .55, gridLines: { display: false, drawBorder: false } }],
                    yAxes: [{ stacked: true, ticks: { beginAtZero: true, callback: wholeNumber }, gridLines: grid }]
                },
                tooltips: { mode: 'index' }
            }
        });

        // Aset rusak per gedung (admin) / ruangan (user)
        var damaged = <?= json_encode($damaged); ?>;
        if (damaged.length) {
            new Chart(document.getElementById('damagedChart'), {
                type: 'horizontalBar',
                data: {
                    labels: damaged.map(function (d) { return d.label; }),
                    datasets: [
                        { label: 'Rusak ringan', backgroundColor: C.warning, data: damaged.map(function (d) { return d[2]; }) },
                        { label: 'Rusak berat', backgroundColor: C.danger, data: damaged.map(function (d) { return d[3]; }) }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: { position: 'bottom', labels: { padding: 16 } },
                    scales: {
                        xAxes: [{ stacked: true, ticks: { beginAtZero: true, callback: wholeNumber }, gridLines: grid }],
                        yAxes: [{ stacked: true, barPercentage: .6, gridLines: { display: false } }]
                    }
                }
            });
        }
    })();
</script>
