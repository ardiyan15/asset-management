<?php
$status_badge = [
    0 => '<span class="badge badge-warning">Menunggu</span>',
    1 => '<span class="badge badge-success">Disetujui</span>',
    2 => '<span class="badge badge-secondary">Ditolak</span>',
];
$pending_count  = count(array_filter($details, function ($d) { return (int) $d['status'] === 0; }));
$approved_count = count(array_filter($details, function ($d) { return (int) $d['status'] === 1; }));
$can_review     = $is_admin && $pending_count > 0;
?>
<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
    <a href="<?= base_url('disposal'); ?>" class="small d-inline-block mb-2"><i class="fas fa-arrow-left mr-1"></i> Daftar pengajuan</a>

    <div class="card rounded p-3 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-start">
            <div class="mb-2">
                <h1 class="h4 text-gray-800 mb-1">
                    <?= html_escape($disposal['code']); ?>
                    <?php if ($pending_count > 0) : ?>
                        <span class="badge badge-warning align-middle ml-1">Menunggu persetujuan</span>
                    <?php else : ?>
                        <span class="badge badge-success align-middle ml-1">Selesai ditinjau</span>
                    <?php endif; ?>
                </h1>
                <div class="small text-muted">
                    Diajukan <b><?= html_escape($disposal['requested_by_name'] ?? '-'); ?></b>
                    · <?= date('d M Y H:i', strtotime($disposal['created_at'])); ?>
                    · <?= html_escape($disposal['building_names'] ?: '-'); ?>
                </div>
            </div>
            <div class="text-nowrap">
                <?php if ($approved_count > 0) : ?>
                    <a href="<?= base_url('disposal/print_bast/' . $token); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="fas fa-file-pdf mr-1"></i> Cetak BAST Penghapusan</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3 mb-2">
                <div class="small text-muted text-uppercase font-weight-bold">Metode</div>
                <div class="text-gray-800"><?= html_escape(Disposal_model::method_label($disposal['method'])); ?></div>
            </div>
            <div class="col-md-9 mb-2">
                <div class="small text-muted text-uppercase font-weight-bold">Alasan</div>
                <div class="text-gray-800"><?= nl2br(html_escape($disposal['reason'])); ?></div>
            </div>
        </div>
    </div>

    <div class="card rounded p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h2 class="h6 font-weight-bold text-gray-800 mb-0">Aset yang diajukan (<?= count($details); ?>)</h2>
            <?php if ($can_review) : ?>
                <div class="mt-2 mt-md-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnReject"><i class="fas fa-times mr-1"></i> Tolak terpilih</button>
                    <button type="button" class="btn btn-success btn-sm" id="btnApprove"><i class="fas fa-check mr-1"></i> Setujui terpilih</button>
                    <button type="button" class="btn btn-danger btn-sm" id="btnApproveAll"><i class="fas fa-check-double mr-1"></i> Setujui semua (<?= $pending_count; ?>)</button>
                </div>
            <?php endif; ?>
        </div>

        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <?php if ($can_review) : ?><th style="width: 2rem;"><input type="checkbox" id="checkAll" aria-label="Pilih semua"></th><?php endif; ?>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Nomor Seri</th>
                        <th>Lokasi</th>
                        <th>Kondisi saat diajukan</th>
                        <th>Status</th>
                        <th>Ditinjau</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $d) : ?>
                        <?php $pending = (int) $d['status'] === 0; ?>
                        <tr>
                            <?php if ($can_review) : ?>
                                <td><?php if ($pending) : ?><input type="checkbox" class="detail-check" value="<?= $d['id']; ?>"><?php endif; ?></td>
                            <?php endif; ?>
                            <td><?= html_escape($d['asset_name'] ?? '(aset dihapus)'); ?></td>
                            <td><?= html_escape($d['merk'] ?? '-'); ?></td>
                            <td>
                                <?php if (!empty($d['serial_number'])) : ?>
                                    <a href="<?= base_url('scan/' . rawurlencode($d['serial_number'])); ?>" target="_blank"><code><?= html_escape($d['serial_number']); ?></code></a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= html_escape($d['room_name'] ?? '-'); ?>
                                <div class="small text-muted"><?= html_escape(($d['building_name'] ?? '-') . ' · ' . ($d['floor_name'] ?? '-')); ?></div>
                            </td>
                            <td><?= asset_condition_badge($d['condition_at_request']); ?></td>
                            <td><?= $status_badge[(int) $d['status']] ?? '-'; ?></td>
                            <td class="small">
                                <?php if (!$pending) : ?>
                                    <?= html_escape($d['reviewed_by_name'] ?? '-'); ?> · <?= date('d M Y H:i', strtotime($d['reviewed_at'])); ?>
                                    <?php if (!empty($d['review_notes'])) : ?>
                                        <div class="text-muted"><i class="fas fa-comment-alt mr-1"></i><?= html_escape($d['review_notes']); ?></div>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (!$is_admin && $pending_count > 0) : ?>
            <p class="small text-muted mt-3 mb-0"><i class="fas fa-hourglass-half mr-1"></i> Menunggu persetujuan admin. Selama diajukan, aset tidak bisa dipindahkan.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($can_review) : ?>
    <form id="reviewForm" method="post" class="d-none">
        <input type="hidden" name="token" value="<?= html_escape($token); ?>">
        <input type="hidden" name="notes" id="reviewNotes">
        <input type="hidden" name="all" id="reviewAll" value="">
        <div id="reviewIds"></div>
    </form>

    <script>
        (function() {
            const approveUrl = <?= json_encode(base_url('disposal/approve')); ?>;
            const rejectUrl = <?= json_encode(base_url('disposal/reject')); ?>;

            function selectedIds() {
                return $('.detail-check:checked').map(function() {
                    return $(this).val();
                }).get();
            }

            function submitReview(url, ids, all, notes) {
                const $ids = $('#reviewIds').empty();
                ids.forEach(function(id) {
                    $ids.append($('<input type="hidden" name="detail_ids[]">').val(id));
                });
                $('#reviewAll').val(all ? '1' : '');
                $('#reviewNotes').val(notes || '');
                $('#reviewForm').attr('action', url).trigger('submit');
            }

            function needSelection(ids) {
                if (ids.length > 0) return false;
                Swal.fire({
                    icon: 'info',
                    title: 'Belum ada aset dipilih',
                    text: 'Centang aset yang ingin ditinjau.'
                });
                return true;
            }

            $('#checkAll').on('change', function() {
                $('.detail-check').prop('checked', $(this).is(':checked'));
            });

            $('#btnApprove').on('click', function() {
                const ids = selectedIds();
                if (needSelection(ids)) return;
                Swal.fire({
                    icon: 'warning',
                    title: 'Setujui penghapusan?',
                    html: '<b>' + ids.length + '</b> aset akan ditandai <b>sudah dihapus</b> dan tidak muncul lagi di daftar aset aktif.',
                    input: 'text',
                    inputPlaceholder: 'Catatan (opsional)',
                    showCancelButton: true,
                    confirmButtonColor: '#1cc88a',
                    confirmButtonText: 'Setujui',
                    cancelButtonText: 'Batal'
                }).then(function(r) {
                    if (r.value !== undefined) submitReview(approveUrl, ids, false, r.value);
                });
            });

            $('#btnApproveAll').on('click', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Setujui semua?',
                    html: 'Semua aset yang masih menunggu di pengajuan ini akan ditandai <b>sudah dihapus</b>.',
                    input: 'text',
                    inputPlaceholder: 'Catatan (opsional)',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Setujui semua',
                    cancelButtonText: 'Batal'
                }).then(function(r) {
                    if (r.value !== undefined) submitReview(approveUrl, [], true, r.value);
                });
            });

            $('#btnReject').on('click', function() {
                const ids = selectedIds();
                if (needSelection(ids)) return;
                Swal.fire({
                    icon: 'question',
                    title: 'Tolak ' + ids.length + ' aset?',
                    input: 'text',
                    inputPlaceholder: 'Alasan penolakan (wajib)',
                    inputValidator: function(value) {
                        if (!value || !value.trim()) return 'Alasan penolakan wajib diisi';
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal'
                }).then(function(r) {
                    if (r.value) submitReview(rejectUrl, ids, false, r.value);
                });
            });
        })();
    </script>
<?php endif; ?>
