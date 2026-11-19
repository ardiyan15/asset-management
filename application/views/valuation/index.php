<?php
$pct_book = $totals['cost'] > 0 ? round($totals['book_value'] / $totals['cost'] * 100) : 0;
?>
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
            <p class="small text-muted mb-0">
                Penyusutan garis lurus per kategori, per <b><?= date('d M Y', strtotime($filters['as_of'])); ?></b> · <?= html_escape($scope_label); ?>.
                Atur masa manfaat & nilai sisa di <?= $user['role_id'] === '1' ? '<a href="' . base_url('category') . '">Data Kategori</a>' : 'Data Kategori (admin)'; ?>.
            </p>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="<?= base_url('valuation/export_excel' . ($query ? '?' . $query : '')); ?>" class="btn btn-success btn-sm"><i class="fas fa-file-excel mr-1"></i> Excel</a>
            <a href="<?= base_url('valuation/export_pdf' . ($query ? '?' . $query : '')); ?>" target="_blank" class="btn btn-danger btn-sm"><i class="fas fa-file-pdf mr-1"></i> PDF</a>
        </div>
    </div>

    <form method="get" action="<?= base_url('valuation'); ?>" class="card rounded p-3 mb-3">
        <div class="form-row align-items-end">
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
                <label for="category_id" class="small font-weight-bold">Kategori</label>
                <select class="form-control form-control-sm" id="category_id" name="category_id">
                    <option value="">Semua kategori</option>
                    <?php foreach ($categories as $c) : ?>
                        <option value="<?= $c['id']; ?>" <?= $filters['category_id'] === (int) $c['id'] ? 'selected' : ''; ?>><?= html_escape($c['name']); ?></option>
                    <?php endforeach; ?>
                    <option value="none" <?= $filters['category_id'] === 'none' ? 'selected' : ''; ?>>Tanpa kategori</option>
                </select>
            </div>
            <div class="form-group col-md-3 mb-2">
                <label for="as_of" class="small font-weight-bold">Nilai per tanggal</label>
                <input type="date" class="form-control form-control-sm" id="as_of" name="as_of" value="<?= html_escape($filters['as_of']); ?>">
            </div>
            <div class="form-group col-md-3 mb-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter mr-1"></i> Terapkan</button>
                <a href="<?= base_url('valuation'); ?>" class="btn btn-link btn-sm">Reset</a>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Harga Perolehan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= rupiah($totals['cost']); ?></div>
                    <div class="small text-muted"><?= $totals['with_price']; ?> dari <?= $totals['count']; ?> aset</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Akumulasi Penyusutan</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= rupiah($totals['accumulated']); ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Nilai Buku</div>
                    <div class="h5 mb-1 font-weight-bold text-gray-800"><?= rupiah($totals['book_value']); ?></div>
                    <div class="progress progress-sm">
                        <div class="progress-bar bg-success" style="width: <?= $pct_book; ?>%"></div>
                    </div>
                    <div class="small text-muted"><?= $pct_book; ?>% dari harga perolehan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Perlu dilengkapi</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totals['missing']; ?> <span class="small text-muted font-weight-normal">aset tanpa data beli</span></div>
                    <?php if ($totals['warranty_soon'] > 0) : ?>
                        <div class="small text-warning font-weight-bold"><?= $totals['warranty_soon']; ?> garansi habis ≤ 30 hari</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card rounded p-3 mb-3">
        <h2 class="h6 font-weight-bold text-gray-800 mb-3">Ringkasan per kategori</h2>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Kategori</th>
                        <th class="text-center">Masa Manfaat</th>
                        <th class="text-center">Nilai Sisa</th>
                        <th class="text-center">Aset</th>
                        <th class="text-right">Harga Perolehan</th>
                        <th class="text-right">Akumulasi Penyusutan</th>
                        <th class="text-right">Nilai Buku</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($summary as $s) : ?>
                        <tr>
                            <td class="font-weight-bold"><?= html_escape($s['name']); ?></td>
                            <td class="text-center"><?= $s['life_years'] ? (int) $s['life_years'] . ' th' : '<span class="text-muted">-</span>'; ?></td>
                            <td class="text-center"><?= $s['residual_percent'] !== null ? (float) $s['residual_percent'] . '%' : '-'; ?></td>
                            <td class="text-center"><?= $s['count']; ?><?= $s['with_price'] < $s['count'] ? ' <span class="small text-muted">(' . $s['with_price'] . ' berharga)</span>' : ''; ?></td>
                            <td class="text-right"><?= rupiah($s['cost']); ?></td>
                            <td class="text-right"><?= rupiah($s['accumulated']); ?></td>
                            <td class="text-right font-weight-bold"><?= rupiah($s['book_value']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($summary)) : ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada aset</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td>Total</td>
                        <td></td>
                        <td></td>
                        <td class="text-center"><?= $totals['count']; ?></td>
                        <td class="text-right"><?= rupiah($totals['cost']); ?></td>
                        <td class="text-right"><?= rupiah($totals['accumulated']); ?></td>
                        <td class="text-right"><?= rupiah($totals['book_value']); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <h2 class="h6 font-weight-bold text-gray-800 mb-3">Detail aset</h2>
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="valuationTable">
                <thead>
                    <tr>
                        <th>Aset</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Perolehan</th>
                        <th>Garansi</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Akumulasi</th>
                        <th class="text-right">Nilai Buku</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <?php $d = $row['depreciation']; ?>
                        <tr>
                            <td>
                                <a href="<?= base_url('asset/detail/' . (int) $row['id_asset']); ?>" class="font-weight-bold"><?= html_escape($row['asset_name']); ?></a> <span class="text-muted small"><?= html_escape($row['merk']); ?></span>
                                <div><code class="small"><?= html_escape($row['serial_number']); ?></code></div>
                            </td>
                            <td><?= html_escape($row['category_name'] ?? '-'); ?></td>
                            <td><?= html_escape($row['room_name']); ?><div class="small text-muted"><?= html_escape($row['building_name']); ?></div></td>
                            <td data-order="<?= html_escape($row['purchase_date'] ?? ''); ?>">
                                <?php if ($row['purchase_date']) : ?>
                                    <?= date('d M Y', strtotime($row['purchase_date'])); ?>
                                    <div class="small text-muted"><?= html_escape($row['vendor'] ?? ''); ?><?= $d ? ' · umur ' . asset_age_label($d['age_months']) : ''; ?></div>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['warranty'] ? '<span class="badge badge-' . $row['warranty']['color'] . '">' . html_escape($row['warranty']['label']) . '</span>' : '<span class="text-muted">-</span>'; ?></td>
                            <td class="text-right" data-order="<?= $d ? $d['price'] : -1; ?>"><?= $d ? rupiah($d['price']) : '<span class="text-muted">-</span>'; ?></td>
                            <td class="text-right" data-order="<?= $d ? $d['accumulated'] : -1; ?>">
                                <?php if ($d && $d['configured']) : ?>
                                    <?= rupiah($d['accumulated']); ?>
                                    <div class="small text-muted"><?= $d['percent_depreciated']; ?>%<?= $d['fully_depreciated'] ? ' · habis' : ''; ?></div>
                                <?php elseif ($d) : ?>
                                    <span class="small text-muted">tidak disusutkan</span>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right font-weight-bold" data-order="<?= $d ? $d['book_value'] : -1; ?>"><?= $d ? rupiah($d['book_value']) : '<span class="text-muted font-weight-normal">-</span>'; ?></td>
                            <td class="text-right">
                                <?php if (!$d) : ?>
                                    <a href="<?= base_url('asset/update/' . $row['id_asset']); ?>" class="btn btn-sm btn-outline-secondary" title="Lengkapi data pembelian"><i class="fas fa-pen"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#valuationTable').DataTable({
            order: [],
            pageLength: 25,
            columnDefs: [{
                orderable: false,
                targets: -1
            }],
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
</script>
