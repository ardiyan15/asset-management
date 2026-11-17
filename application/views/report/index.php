<?php
$conditions  = asset_condition_options();
$cond_colors = [1 => '#1cc88a', 2 => '#f6c23e', 3 => '#e74a3b'];
$show_disposed_col = $filters['status'] !== 'active';
?>
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-gray-800 mb-1"><?= $title; ?> per <?= $group_label; ?></h1>
            <p class="small text-muted mb-0"><?= html_escape(implode(' · ', $filter_labels)); ?></p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="<?= base_url('report/export_excel' . ($query ? '?' . $query : '')); ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel mr-1"></i> Excel</a>
            <a href="<?= base_url('report/export_pdf' . ($query ? '?' . $query : '')); ?>" target="_blank" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
        </div>
    </div>

    <form method="get" action="<?= base_url('report'); ?>" class="card rounded p-3 mb-3">
        <div class="form-row align-items-end">
            <div class="form-group col-md-2 mb-2">
                <label for="group" class="small font-weight-bold">Kelompokkan per</label>
                <select class="form-control form-control-sm" id="group" name="group">
                    <?php foreach ($group_options as $value => $label) : ?>
                        <?php if ($value === 'building' && $user['role_id'] !== '1') continue; ?>
                        <option value="<?= $value; ?>" <?= $filters['group'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($user['role_id'] === '1') : ?>
                <div class="form-group col-md-2 mb-2">
                    <label for="building_id" class="small font-weight-bold">Gedung</label>
                    <select class="form-control form-control-sm" id="building_id" name="building_id">
                        <option value="">Semua gedung</option>
                        <?php foreach ($buildings as $b) : ?>
                            <option value="<?= $b['id']; ?>" <?= $filters['building_id'] === (int) $b['id'] ? 'selected' : ''; ?>><?= html_escape($b['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="form-group col-md-2 mb-2">
                <label for="category_id" class="small font-weight-bold">Kategori</label>
                <select class="form-control form-control-sm" id="category_id" name="category_id">
                    <option value="">Semua kategori</option>
                    <?php foreach ($categories as $c) : ?>
                        <option value="<?= $c['id']; ?>" <?= $filters['category_id'] === (int) $c['id'] ? 'selected' : ''; ?>><?= html_escape($c['name']); ?></option>
                    <?php endforeach; ?>
                    <option value="none" <?= $filters['category_id'] === 'none' ? 'selected' : ''; ?>>Tanpa kategori</option>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="condition" class="small font-weight-bold">Kondisi</label>
                <select class="form-control form-control-sm" id="condition" name="condition">
                    <option value="">Semua kondisi</option>
                    <?php foreach ($conditions as $value => $label) : ?>
                        <option value="<?= $value; ?>" <?= $filters['condition'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="status" class="small font-weight-bold">Status</label>
                <select class="form-control form-control-sm" id="status" name="status">
                    <?php foreach ($status_options as $value => $label) : ?>
                        <option value="<?= $value; ?>" <?= $filters['status'] === $value ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter mr-1"></i> Terapkan</button>
                <a href="<?= base_url('report'); ?>" class="btn btn-link btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jumlah Aset</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totals['total']; ?></div>
                    <div class="small text-muted"><?= count($groups); ?> <?= strtolower($group_label); ?></div>
                </div>
            </div>
        </div>
        <?php foreach ([2, 3] as $c) : ?>
            <?php $pct = $totals['total'] > 0 ? round($totals['conditions'][$c] / $totals['total'] * 100) : 0; ?>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-<?= asset_condition_color($c); ?> shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-<?= asset_condition_color($c); ?> text-uppercase mb-1"><?= $conditions[$c]; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totals['conditions'][$c]; ?> <span class="small text-muted font-weight-normal">aset · <?= $pct; ?>%</span></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Nilai Buku</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= rupiah($totals['book_value']); ?></div>
                    <div class="small text-muted">dari perolehan <?= rupiah($totals['cost']); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-7 mb-3">
            <div class="card rounded p-3 h-100">
                <h2 class="h6 font-weight-bold text-gray-800 mb-3">Rekap per <?= strtolower($group_label); ?></h2>
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th><?= $group_label; ?></th>
                                <th class="text-center">Aset</th>
                                <?php foreach ($conditions as $c => $label) : ?>
                                    <th class="text-center"><?= $label; ?></th>
                                <?php endforeach; ?>
                                <?php if ($show_disposed_col) : ?><th class="text-center">Dihapus</th><?php endif; ?>
                                <th class="text-right">Nilai Buku</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groups as $g) : ?>
                                <tr>
                                    <td class="font-weight-bold"><?= html_escape($g['label']); ?></td>
                                    <td class="text-center"><?= $g['total']; ?></td>
                                    <?php foreach ($conditions as $c => $label) : ?>
                                        <td class="text-center <?= $c > 1 && $g['conditions'][$c] > 0 ? 'text-' . asset_condition_color($c) . ' font-weight-bold' : ''; ?>"><?= $g['conditions'][$c]; ?></td>
                                    <?php endforeach; ?>
                                    <?php if ($show_disposed_col) : ?><td class="text-center"><?= $g['disposed']; ?></td><?php endif; ?>
                                    <td class="text-right"><?= rupiah($g['book_value']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($groups)) : ?>
                                <tr><td colspan="<?= 4 + count($conditions); ?>" class="text-center text-muted">Tidak ada aset sesuai filter</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td>Total</td>
                                <td class="text-center"><?= $totals['total']; ?></td>
                                <?php foreach ($conditions as $c => $label) : ?>
                                    <td class="text-center"><?= $totals['conditions'][$c]; ?></td>
                                <?php endforeach; ?>
                                <?php if ($show_disposed_col) : ?><td class="text-center"><?= $totals['disposed']; ?></td><?php endif; ?>
                                <td class="text-right"><?= rupiah($totals['book_value']); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-5 mb-3">
            <div class="card rounded p-3 h-100">
                <h2 class="h6 font-weight-bold text-gray-800 mb-3">Kondisi per <?= strtolower($group_label); ?></h2>
                <div style="position: relative; height: <?= max(220, min(480, count($groups) * 34 + 80)); ?>px;">
                    <canvas id="recapChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <h2 class="h6 font-weight-bold text-gray-800 mb-3">Detail aset (<?= count($rows); ?>)</h2>
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="recapTable">
                <thead>
                    <tr>
                        <th>Aset</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th class="text-right">Nilai Buku</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <?php $d = $row['depreciation']; ?>
                        <tr>
                            <td>
                                <b><?= html_escape($row['asset_name']); ?></b> <span class="small text-muted"><?= html_escape($row['merk']); ?></span>
                                <div><a href="<?= base_url('scan/' . rawurlencode($row['serial_number'])); ?>" target="_blank"><code class="small"><?= html_escape($row['serial_number']); ?></code></a></div>
                            </td>
                            <td><?= html_escape($row['category_name'] ?? '-'); ?></td>
                            <td><?= html_escape($row['room_name']); ?><div class="small text-muted"><?= html_escape($row['building_name'] . ' · ' . $row['floor_name']); ?></div></td>
                            <td data-order="<?= (int) $row['asset_condition']; ?>"><?= asset_condition_badge($row['asset_condition']); ?></td>
                            <td>
                                <?php if ((int) $row['is_disposed'] === 1) : ?>
                                    <span class="badge badge-dark">Dihapus</span>
                                <?php elseif ((int) $row['placement_status'] !== 1) : ?>
                                    <span class="badge badge-info">Dipindahkan</span>
                                <?php else : ?>
                                    <span class="badge badge-light">Di ruangan</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right" data-order="<?= $d && (int) $row['is_disposed'] === 0 ? $d['book_value'] : -1; ?>">
                                <?= $d && (int) $row['is_disposed'] === 0 ? rupiah($d['book_value']) : '<span class="text-muted">-</span>'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function() {
        const groups = <?= json_encode(array_map(function ($g) { return ['label' => $g['label'], 'conditions' => $g['conditions']]; }, $groups)); ?>;
        const conditions = <?= json_encode($conditions); ?>;
        const colors = <?= json_encode($cond_colors); ?>;

        if (groups.length && window.Chart) {
            new Chart(document.getElementById('recapChart'), {
                type: 'horizontalBar',
                data: {
                    labels: groups.map(function(g) {
                        return g.label.length > 28 ? g.label.slice(0, 27) + '…' : g.label;
                    }),
                    datasets: Object.keys(conditions).map(function(c) {
                        return {
                            label: conditions[c],
                            backgroundColor: colors[c],
                            data: groups.map(function(g) {
                                return g.conditions[c];
                            })
                        };
                    })
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        position: 'bottom'
                    },
                    scales: {
                        xAxes: [{
                            stacked: true,
                            ticks: {
                                beginAtZero: true,
                                // Chart.js 2.4 belum punya opsi precision; sembunyikan tick pecahan
                                callback: function(value) {
                                    return value % 1 === 0 ? value : '';
                                }
                            }
                        }],
                        yAxes: [{
                            stacked: true,
                            gridLines: {
                                display: false
                            }
                        }]
                    }
                }
            });
        }

        $(function() {
            $('#recapTable').DataTable({
                order: [],
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
        });
    })();
</script>
