<div class="container-fluid">
    <a href="<?= base_url('maintenance'); ?>" class="small d-inline-block mb-2"><i class="fas fa-arrow-left mr-1"></i> Jadwal maintenance</a>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
            <p class="small text-muted mb-0">Perawatan rutin & perbaikan yang sudah dikerjakan, beserta biayanya.</p>
        </div>
        <a href="<?= base_url('maintenance/export_history' . ($query ? '?' . $query : '')); ?>" class="btn btn-success btn-sm mt-2 mt-md-0"><i class="fas fa-file-excel mr-1"></i> Excel</a>
    </div>

    <form method="get" action="<?= base_url('maintenance/history'); ?>" class="card rounded p-3 mb-3">
        <div class="form-row align-items-end">
            <div class="form-group col-md-2 mb-2">
                <label for="year" class="small font-weight-bold">Tahun</label>
                <select class="form-control form-control-sm" id="year" name="year">
                    <?php foreach ($years as $y) : ?>
                        <option value="<?= $y; ?>" <?= $filters['year'] === $y ? 'selected' : ''; ?>><?= $y; ?></option>
                    <?php endforeach; ?>
                    <option value="all" <?= $filters['year'] === 0 ? 'selected' : ''; ?>>Semua tahun</option>
                </select>
            </div>
            <div class="form-group col-md-3 mb-2">
                <label for="type" class="small font-weight-bold">Jenis</label>
                <select class="form-control form-control-sm" id="type" name="type">
                    <option value="">Semua jenis</option>
                    <?php foreach ($types as $value => $label) : ?>
                        <option value="<?= $value; ?>" <?= $filters['type'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($user['role_id'] === '1') : ?>
                <div class="form-group col-md-3 mb-2">
                    <label for="building_id" class="small font-weight-bold">Gedung</label>
                    <select class="form-control form-control-sm" id="building_id" name="building_id">
                        <option value="">Semua gedung</option>
                        <?php foreach ($buildings as $b) : ?>
                            <option value="<?= $b['id']; ?>" <?= $filters['building_id'] === (int) $b['id'] ? 'selected' : ''; ?>><?= html_escape($b['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="form-group col-md-3 mb-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter mr-1"></i> Terapkan</button>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-xl-4 mb-3">
            <div class="card border-left-primary shadow h-100 py-2 mb-3">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total biaya <?= $filters['year'] ?: 'semua tahun'; ?></div>
                    <div class="h4 mb-0 font-weight-bold text-gray-800"><?= rupiah($total_cost); ?></div>
                    <div class="small text-muted"><?= count($logs); ?> pekerjaan</div>
                    <hr class="my-2">
                    <?php foreach ($types as $value => $label) : ?>
                        <div class="d-flex justify-content-between small">
                            <span><?= $label; ?></span>
                            <b><?= rupiah($by_type[$value] ?? 0); ?></b>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-8 mb-3">
            <div class="card shadow h-100">
                <div class="card-body">
                    <h2 class="h6 font-weight-bold text-gray-800 mb-3">Biaya per bulan <?= $filters['year'] ?: '(semua tahun digabung)'; ?></h2>
                    <div style="position: relative; height: 220px;">
                        <canvas id="costChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <?php if (empty($logs)) : ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-clipboard fa-3x mb-3 text-gray-300"></i>
                <p class="mb-0">Belum ada riwayat di periode ini.</p>
            </div>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover" id="historyTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Aset</th>
                            <th>Pekerjaan</th>
                            <th>Vendor</th>
                            <th>Kondisi setelah</th>
                            <th>Dicatat</th>
                            <th class="text-right">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td class="text-nowrap" data-order="<?= html_escape($log['performed_at']); ?>"><?= date('d M Y', strtotime($log['performed_at'])); ?></td>
                                <td>
                                    <?= html_escape($log['asset_name']); ?>
                                    <div class="small text-muted"><?= html_escape($log['room_name'] . ', ' . $log['building_name']); ?></div>
                                </td>
                                <td>
                                    <span class="badge <?= $log['type'] === 'rutin' ? 'badge-info' : 'badge-danger'; ?>"><?= html_escape($types[$log['type']] ?? $log['type']); ?></span>
                                    <?= $log['schedule_title'] ? '<b>' . html_escape($log['schedule_title']) . '</b>' : ''; ?>
                                    <div class="small"><?= html_escape($log['description']); ?></div>
                                </td>
                                <td><?= html_escape($log['vendor'] ?? '-'); ?></td>
                                <td><?= $log['condition_after'] ? asset_condition_badge($log['condition_after']) : '<span class="text-muted small">-</span>'; ?></td>
                                <td class="small"><?= html_escape($log['performed_by_name'] ?? '-'); ?></td>
                                <td class="text-right text-nowrap" data-order="<?= (float) $log['cost']; ?>"><?= rupiah($log['cost']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    (function() {
        const costs = <?= json_encode($by_month); ?>;
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const rupiah = function(v) {
            return 'Rp ' + Math.round(v).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        };

        if (window.Chart) {
            new Chart(document.getElementById('costChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Biaya',
                        backgroundColor: '#4e73df',
                        data: costs
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                callback: function(v) {
                                    return v >= 1000000 ? (v / 1000000) + ' jt' : (v >= 1000 ? (v / 1000) + ' rb' : v);
                                }
                            }
                        }]
                    },
                    tooltips: {
                        callbacks: {
                            label: function(item) {
                                return rupiah(item.yLabel);
                            }
                        }
                    }
                }
            });
        }

        $(function() {
            if ($('#historyTable').length) {
                $('#historyTable').DataTable({
                    order: [
                        [0, 'desc']
                    ],
                    pageLength: 25,
                    language: {
                        search: 'Cari',
                        lengthMenu: '_MENU_',
                        info: 'Jumlah data: <b>_TOTAL_</b>',
                        infoEmpty: 'Tidak ada data',
                        paginate: {
                            previous: 'Sebelumnya',
                            next: 'Selanjutnya'
                        }
                    }
                });
            }
        });
    })();
</script>
