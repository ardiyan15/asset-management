<style>
    .audit-action { font-family: SFMono-Regular, Menlo, Consolas, monospace; font-size: .75rem; }
    #auditTable td { vertical-align: top; }
    .changes-table td, .changes-table th { padding: .3rem .5rem; font-size: .85rem; }
    .changes-table .old { color: #b03a2e; text-decoration: line-through; }
    .changes-table .new { color: #1e7e34; font-weight: 600; }
</style>

<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 text-gray-800 mb-1"><?= $title; ?></h1>
            <p class="small text-muted mb-0">Catatan siapa melakukan apa, kapan, dan dari mana. Log hanya bisa dibaca, tidak bisa diubah atau dihapus dari aplikasi.</p>
        </div>
        <a href="<?= base_url('activity/export' . ($query ? '?' . $query : '')); ?>" class="btn btn-success btn-sm mt-2 mt-md-0"><i class="fas fa-file-excel mr-1"></i> Excel</a>
    </div>

    <form method="get" action="<?= base_url('activity'); ?>" class="card rounded p-3 mb-3">
        <div class="form-row align-items-end">
            <div class="form-group col-md-2 mb-2">
                <label for="date_from" class="small font-weight-bold">Dari</label>
                <input type="date" class="form-control form-control-sm" id="date_from" name="date_from" value="<?= html_escape($filters['date_from']); ?>">
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="date_to" class="small font-weight-bold">Sampai</label>
                <input type="date" class="form-control form-control-sm" id="date_to" name="date_to" value="<?= html_escape($filters['date_to']); ?>">
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="user_id" class="small font-weight-bold">User</label>
                <select class="form-control form-control-sm" id="user_id" name="user_id">
                    <option value="">Semua user</option>
                    <?php foreach ($users as $u) : ?>
                        <option value="<?= $u['id_user']; ?>" <?= $filters['user_id'] === (int) $u['id_user'] ? 'selected' : ''; ?>><?= html_escape($u['username']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="module" class="small font-weight-bold">Modul</label>
                <select class="form-control form-control-sm" id="module" name="module">
                    <option value="">Semua modul</option>
                    <?php foreach ($modules as $key => $label) : ?>
                        <option value="<?= $key; ?>" <?= $filters['module'] === $key ? 'selected' : ''; ?>><?= $label; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="action" class="small font-weight-bold">Aksi</label>
                <select class="form-control form-control-sm" id="action" name="action">
                    <option value="">Semua aksi</option>
                    <?php foreach ($actions as $a) : ?>
                        <option value="<?= html_escape($a); ?>" <?= $filters['action'] === $a ? 'selected' : ''; ?>><?= html_escape($a); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label for="building_id" class="small font-weight-bold">Gedung</label>
                <select class="form-control form-control-sm" id="building_id" name="building_id">
                    <option value="">Semua gedung</option>
                    <?php foreach ($buildings as $b) : ?>
                        <option value="<?= $b['id']; ?>" <?= $filters['building_id'] === (int) $b['id'] ? 'selected' : ''; ?>><?= html_escape($b['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-row align-items-end">
            <div class="form-group col-md-6 mb-0">
                <input type="text" class="form-control form-control-sm" name="q" value="<?= html_escape($filters['q']); ?>" placeholder="Cari keterangan, nomor seri / kode, username, atau IP">
            </div>
            <div class="form-group col-md-6 mb-0 mt-2 mt-md-0">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter mr-1"></i> Terapkan</button>
                <a href="<?= base_url('activity'); ?>" class="btn btn-link btn-sm">Reset (7 hari terakhir)</a>
            </div>
        </div>
    </form>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Aktivitas</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($summary['total'] ?? 0, 0, ',', '.'); ?></div>
                    <div class="small text-muted"><?= (int) ($summary['users'] ?? 0); ?> user aktif</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="text-decoration-none" href="<?= base_url('activity?' . http_build_query(array_filter(array_merge($filters, ['action' => 'auth.login_failed', 'module' => ''])))); ?>">
                <div class="card border-left-danger shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Login gagal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($summary['login_failed'] ?? 0); ?></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Aksi kritis</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($summary['critical'] ?? 0); ?></div>
                    <div class="small text-muted">hapus aset/user/gedung, setujui penghapusan</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <a class="text-decoration-none" href="<?= base_url('activity?' . http_build_query(array_filter(array_merge($filters, ['module' => 'report', 'action' => ''])))); ?>">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ekspor / cetak</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= (int) ($summary['exports'] ?? 0); ?></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <div class="table-responsive">
            <table class="table table-sm table-hover" id="auditTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 9rem;">Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Keterangan</th>
                        <th>Data</th>
                        <th>IP</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Detail log -->
<div class="modal fade" id="auditDetail" role="dialog" aria-labelledby="auditDetailLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="auditDetailLabel">Detail aktivitas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="font-weight-bold text-gray-800 js-desc mb-1"></p>
                <p class="small text-muted js-meta"></p>
                <div class="js-changes"></div>
                <dl class="row small mb-0 mt-3">
                    <dt class="col-sm-3">URL</dt>
                    <dd class="col-sm-9 js-url text-break"></dd>
                    <dt class="col-sm-3">User agent</dt>
                    <dd class="col-sm-9 js-agent text-break"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        const moduleColors = {
            'Login / logout': 'secondary',
            'Data aset': 'primary',
            'Pindah aset': 'info',
            'Stock opname': 'info',
            'Penghapusan aset': 'danger',
            'Maintenance': 'warning',
            'Peminjaman': 'info',
            'Data master': 'dark',
            'Pengguna': 'dark',
            'Laporan & ekspor': 'success'
        };
        // Nilai dari server sudah di-escape; nilai di "changes" di-escape di sini
        const esc = function(v) {
            return $('<div>').text(v === null || v === undefined || v === '' ? '-' : (typeof v === 'object' ? JSON.stringify(v) : String(v))).html();
        };
        let rowsCache = [];

        const table = $('#auditTable').DataTable({
            processing: true,
            serverSide: true,
            ordering: false,
            pageLength: 50,
            lengthMenu: [25, 50, 100, 200],
            ajax: {
                url: <?= json_encode(base_url('activity/data' . ($query ? '?' . $query : ''))); ?>,
                type: 'POST',
                dataSrc: function(json) {
                    rowsCache = json.data;
                    return json.data;
                }
            },
            columns: [{
                    data: 'time',
                    className: 'small text-nowrap'
                },
                {
                    data: null,
                    render: function(r) {
                        return r.user + (r.role ? '<div class="small text-muted">' + r.role + '</div>' : '');
                    }
                },
                {
                    data: null,
                    render: function(r) {
                        return '<span class="badge badge-' + (moduleColors[r.module] || 'light') + '">' + r.module + '</span><div class="audit-action text-muted">' + r.action + '</div>';
                    }
                },
                {
                    data: null,
                    render: function(r) {
                        return r.description + (r.changes ? ' <span class="badge badge-light" title="Ada detail perubahan"><i class="fas fa-exchange-alt"></i></span>' : '');
                    }
                },
                {
                    data: null,
                    className: 'small',
                    render: function(r) {
                        return (r.entity || '-') + (r.building ? '<div class="text-muted">' + r.building + '</div>' : '');
                    }
                },
                {
                    data: 'ip',
                    className: 'small text-nowrap'
                },
                {
                    data: null,
                    className: 'text-right',
                    render: function(r, type, full, meta) {
                        return '<button type="button" class="btn btn-light btn-sm js-detail" data-row="' + meta.row + '" title="Detail"><i class="fas fa-search"></i></button>';
                    }
                }
            ],
            language: {
                search: 'Cari',
                lengthMenu: '_MENU_',
                info: 'Jumlah data: <b>_TOTAL_</b>',
                infoEmpty: 'Tidak ada aktivitas',
                infoFiltered: '',
                processing: 'memuat...',
                emptyTable: 'Tidak ada aktivitas pada filter ini',
                paginate: {
                    previous: 'Sebelumnya',
                    next: 'Selanjutnya'
                }
            }
        });

        $('#auditTable').on('click', '.js-detail', function() {
            const r = rowsCache[$(this).data('row')];
            const $m = $('#auditDetail');
            $m.find('.js-desc').html(r.description);
            $m.find('.js-meta').html(r.time + ' · ' + r.user + ' · ' + r.action + (r.ip ? ' · ' + r.ip : '') + (r.entity ? ' · ' + r.entity : ''));
            $m.find('.js-url').html(r.url || '-');
            $m.find('.js-agent').html(r.agent || '-');

            const $changes = $m.find('.js-changes').empty();
            if (r.changes && Object.keys(r.changes).length) {
                const $t = $('<table class="table table-bordered changes-table mb-0"><thead><tr><th>Field</th><th>Sebelum</th><th>Sesudah</th></tr></thead><tbody></tbody></table>');
                $.each(r.changes, function(field, c) {
                    $t.find('tbody').append('<tr><td>' + esc(field) + '</td><td class="old">' + esc(c.old) + '</td><td class="new">' + esc(c.new) + '</td></tr>');
                });
                $changes.append($t);
            } else {
                $changes.append('<p class="small text-muted mb-0">Tidak ada detail perubahan field.</p>');
            }
            $m.modal('show');
        });
    });
</script>
