<?php
$is_open  = (int) $opname['status'] === 0;
$expected = array_values(array_filter($details, function ($d) { return (int) $d['result'] !== 2; }));
$wrong    = array_values(array_filter($details, function ($d) { return (int) $d['result'] === 2; }));
$total    = (int) $opname['total_expected'];
$found    = (int) $opname['total_found'];
$percent  = $total > 0 ? round($found / $total * 100) : 0;

// Kolom kondisi: dropdown saat opname berjalan, badge saat sudah selesai
$condition_cell = function ($d) use ($is_open) {
    if (!$is_open || empty($d['serial_number'])) {
        return isset($d['asset_condition']) ? asset_condition_badge($d['asset_condition']) : '-';
    }
    $html = '<select class="form-control form-control-sm opn-condition" data-detail-id="' . (int) $d['id'] . '" data-prev="' . (int) $d['asset_condition'] . '">';
    foreach (asset_condition_options() as $value => $label) {
        $html .= '<option value="' . $value . '"' . ((int) $d['asset_condition'] === $value ? ' selected' : '') . '>' . $label . '</option>';
    }
    return $html . '</select>';
};

// Kolom foto bukti: jumlah foto opname + tombol kamera saat opname berjalan
$photo_cell = function ($d) use ($is_open, $photo_counts) {
    $count = (int) ($photo_counts[$d['asset_id']] ?? 0);
    $badge = '<span class="badge badge-' . ($count ? 'info' : 'light') . ' js-photo-count" data-asset-id="' . (int) $d['asset_id'] . '">'
        . '<i class="fas fa-image mr-1"></i>' . $count . '</span>';
    if (!$is_open || empty($d['serial_number'])) {
        return $count && !empty($d['serial_number'])
            ? '<a href="' . base_url('scan/' . rawurlencode($d['serial_number'])) . '" target="_blank" title="Lihat foto">' . $badge . '</a>'
            : $badge;
    }
    return '<label class="btn btn-sm btn-light border mb-0 js-photo-btn" title="Foto bukti kondisi">'
        . '<i class="fas fa-camera"></i>'
        . '<input type="file" class="d-none js-photo-input" accept="image/jpeg,image/png,image/webp" capture="environment" multiple data-asset-id="' . (int) $d['asset_id'] . '">'
        . '</label> ' . $badge;
};
?>
<style>
    .opn-stat { border: 0; border-left: .25rem solid; border-radius: .5rem; }
    .opn-stat .num { font-size: 1.6rem; font-weight: 800; line-height: 1; }
    .opn-stat .lbl { font-size: .72rem; text-transform: uppercase; letter-spacing: .05em; font-weight: 700; }
    #scanResult { min-height: 1rem; }
    #reader { max-width: 360px; }
    #reader video { border-radius: .5rem; }
    tr.row-found td { background: #f0fbf5; }
    .opn-check { width: 1.25rem; height: 1.25rem; cursor: pointer; }
    .opn-condition { min-width: 8.5rem; }
</style>

<div class="container-fluid">
    <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>

    <a href="<?= base_url('opname'); ?>" class="small d-inline-block mb-2"><i class="fas fa-arrow-left mr-1"></i> Daftar opname</a>

    <div class="card rounded p-3 mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-start">
            <div class="mb-2">
                <h1 class="h4 text-gray-800 mb-1">
                    <?= html_escape($opname['code']); ?>
                    <?php if ($is_open) : ?>
                        <span class="badge badge-warning align-middle ml-1">Berjalan</span>
                    <?php else : ?>
                        <span class="badge badge-success align-middle ml-1">Selesai</span>
                    <?php endif; ?>
                </h1>
                <div class="text-gray-800">
                    <i class="fas fa-door-open mr-1 text-primary"></i>
                    <b><?= html_escape($opname['room_name']); ?></b>
                    <?php if (!empty($opname['room_description'])) : ?>(<?= html_escape($opname['room_description']); ?>)<?php endif; ?>
                    <span class="text-muted">· <?= html_escape($opname['building_name'] . ' · ' . $opname['floor_name']); ?></span>
                </div>
                <div class="small text-muted mt-1">
                    Petugas <b><?= html_escape($opname['username'] ?? '-'); ?></b>
                    · Mulai <?= date('d M Y H:i', strtotime($opname['started_at'])); ?>
                    <?php if (!$is_open && $opname['finished_at']) : ?>
                        · Selesai <?= date('d M Y H:i', strtotime($opname['finished_at'])); ?>
                    <?php endif; ?>
                </div>
                <?php if (!empty($opname['notes'])) : ?>
                    <div class="small mt-1"><i class="fas fa-sticky-note mr-1 text-muted"></i><?= html_escape($opname['notes']); ?></div>
                <?php endif; ?>
            </div>
            <div class="text-nowrap">
                <?php if ($is_open) : ?>
                    <button type="button" id="btnCancel" class="btn btn-outline-danger btn-sm"><i class="fas fa-times mr-1"></i> Batalkan</button>
                    <button type="button" id="btnFinish" class="btn btn-success btn-sm"><i class="fas fa-flag-checkered mr-1"></i> Selesaikan Opname</button>
                <?php else : ?>
                    <a href="<?= base_url('opname/print_report/' . $token); ?>" target="_blank" class="btn btn-dark btn-sm"><i class="fas fa-file-pdf mr-1"></i> Cetak Berita Acara</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 col-lg-3 mb-3">
            <div class="card opn-stat p-3 h-100" style="border-left-color:#4e73df">
                <div class="lbl text-primary">Terdaftar</div>
                <div class="num text-gray-800" data-stat="expected"><?= $total; ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card opn-stat p-3 h-100" style="border-left-color:#1cc88a">
                <div class="lbl text-success">Ditemukan</div>
                <div class="num text-gray-800" data-stat="found"><?= $found; ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card opn-stat p-3 h-100" style="border-left-color:#e74a3b">
                <div class="lbl text-danger"><?= $is_open ? 'Belum dicek' : 'Tidak ditemukan'; ?></div>
                <div class="num text-gray-800" data-stat="missing"><?= (int) $opname['total_missing']; ?></div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card opn-stat p-3 h-100" style="border-left-color:#f6c23e">
                <div class="lbl text-warning">Salah ruangan</div>
                <div class="num text-gray-800" data-stat="wrong_room"><?= (int) $opname['total_wrong_room']; ?></div>
            </div>
        </div>
    </div>

    <div class="progress mb-3" style="height: .6rem;">
        <div class="progress-bar bg-success" id="opnProgress" role="progressbar" style="width: <?= $percent; ?>%" aria-valuenow="<?= $percent; ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <?php if ($is_open) : ?>
        <div class="card rounded p-3 mb-3">
            <label for="serialInput" class="font-weight-bold text-gray-800 mb-2"><i class="fas fa-barcode mr-1"></i> Scan label QR atau ketik nomor seri</label>
            <form id="scanForm" autocomplete="off">
                <div class="input-group">
                    <input type="text" class="form-control" id="serialInput" placeholder="mis. L505SAN21040027" autofocus>
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Cek</button>
                        <button class="btn btn-outline-primary" type="button" id="btnCamera" title="Scan pakai kamera"><i class="fas fa-camera"></i></button>
                    </div>
                </div>
            </form>
            <small class="text-muted d-block mt-1">Scanner barcode USB/Bluetooth langsung bisa dipakai di kolom ini. Bisa juga scan label pakai kamera HP lalu tekan <b>Tandai ditemukan</b> di halaman aset.</small>
            <div id="reader" class="mt-3 d-none"></div>
            <div id="scanResult" class="mt-3"></div>
        </div>
    <?php endif; ?>

    <div class="card rounded p-3 mb-3">
        <h2 class="h6 font-weight-bold text-gray-800 mb-3">Aset terdaftar di ruangan ini (<?= count($expected); ?>)</h2>
        <?php if (empty($expected)) : ?>
            <p class="text-muted mb-0">Tidak ada aset yang tercatat di ruangan ini saat opname dimulai.</p>
        <?php else : ?>
            <div class="table-responsive">
                <table class="table table-sm mb-0" id="expectedTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Merk</th>
                            <th>Nomor Seri</th>
                            <th>Status</th>
                            <th>Kondisi</th>
                            <th>Foto</th>
                            <?php if ($is_open) : ?><th class="text-center">Ada?</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expected as $i => $d) : ?>
                            <?php $is_found = (int) $d['result'] === 1; ?>
                            <tr data-detail-id="<?= $d['id']; ?>" class="<?= $is_found ? 'row-found' : ''; ?>">
                                <td><?= $i + 1; ?></td>
                                <td>
                                    <?= html_escape($d['asset_name'] ?? '(aset dihapus)'); ?>
                                    <?php if (!empty($d['on_loan_to'])) : ?>
                                        <div><span class="badge badge-info" title="Aset sedang dipinjam, wajar kalau tidak ada di ruangan"><i class="fas fa-hand-holding mr-1"></i>Dipinjam: <?= html_escape($d['on_loan_to']); ?></span></div>
                                    <?php endif; ?>
                                </td>
                                <td><?= html_escape($d['merk'] ?? '-'); ?></td>
                                <td><code><?= html_escape($d['serial_number'] ?? '-'); ?></code></td>
                                <td class="js-status">
                                    <?php if ($is_found) : ?>
                                        <span class="badge badge-success">Ditemukan</span>
                                    <?php elseif ($is_open) : ?>
                                        <span class="badge badge-light">Belum dicek</span>
                                    <?php else : ?>
                                        <span class="badge badge-danger">Tidak ditemukan</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $condition_cell($d); ?></td>
                                <td class="text-nowrap"><?= $photo_cell($d); ?></td>
                                <?php if ($is_open) : ?>
                                    <td class="text-center"><input type="checkbox" class="opn-check" value="<?= $d['id']; ?>" <?= $is_found ? 'checked' : ''; ?> aria-label="Tandai ditemukan"></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div class="card rounded p-3 mb-4 <?= empty($wrong) ? 'd-none' : ''; ?>" id="wrongCard">
        <h2 class="h6 font-weight-bold text-gray-800 mb-1">Aset dari ruangan lain yang ditemukan di sini</h2>
        <p class="small text-muted">Lokasinya di sistem tidak sesuai dengan fisiknya. Tindak lanjuti dengan transaksi pindah aset.</p>
        <div class="table-responsive">
            <table class="table table-sm mb-0" id="wrongTable">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Nomor Seri</th>
                        <th>Tercatat di</th>
                        <th>Kondisi</th>
                            <th>Foto</th>
                        <?php if ($is_open) : ?><th></th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($wrong as $d) : ?>
                        <tr data-detail-id="<?= $d['id']; ?>">
                            <td><?= html_escape($d['asset_name'] ?? '(aset dihapus)'); ?></td>
                            <td><?= html_escape($d['merk'] ?? '-'); ?></td>
                            <td><code><?= html_escape($d['serial_number'] ?? '-'); ?></code></td>
                            <td><span class="badge badge-warning"><?= html_escape($d['expected_room_name'] ?? '-'); ?></span></td>
                            <td><?= $condition_cell($d); ?></td>
                                <td class="text-nowrap"><?= $photo_cell($d); ?></td>
                            <?php if ($is_open) : ?>
                                <td class="text-right"><button type="button" class="btn btn-sm btn-link text-danger js-remove-wrong" title="Hapus dari daftar"><i class="fas fa-times"></i></button></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($is_open) : ?>
    <form id="finishForm" method="post" action="<?= base_url('opname/finish'); ?>" class="d-none">
        <input type="hidden" name="token" value="<?= html_escape($token); ?>">
    </form>
    <form id="cancelForm" method="post" action="<?= base_url('opname/cancel'); ?>" class="d-none">
        <input type="hidden" name="token" value="<?= html_escape($token); ?>">
    </form>

    <script>
        (function() {
            const token = <?= json_encode($token); ?>;
            const checkUrl = <?= json_encode(base_url('opname/check')); ?>;
            const toggleUrl = <?= json_encode(base_url('opname/toggle')); ?>;
            const conditionUrl = <?= json_encode(base_url('opname/set_condition')); ?>;
            const conditionOptions = <?= json_encode(asset_condition_options()); ?>;
            const photoUrl = <?= json_encode(base_url('photo/upload')); ?>;
            const $input = $('#serialInput');
            const $result = $('#scanResult');
            const alertClass = {
                found: 'success',
                already: 'info',
                wrong_room: 'warning',
                not_found: 'danger',
                invalid: 'danger',
                closed: 'danger',
                disposed: 'danger'
            };
            let scanner = null;
            let lastCode = '';
            let lastAt = 0;

            function updateSummary(s) {
                if (!s) return;
                $.each(s, function(key, val) {
                    $('[data-stat="' + key + '"]').text(val);
                });
                const pct = s.expected > 0 ? Math.round(s.found / s.expected * 100) : 0;
                $('#opnProgress').css('width', pct + '%').attr('aria-valuenow', pct);
            }

            function markRow(detailId, found) {
                const $row = $('#expectedTable tr[data-detail-id="' + detailId + '"]');
                $row.toggleClass('row-found', found);
                $row.find('.opn-check').prop('checked', found);
                $row.find('.js-status').html(found ?
                    '<span class="badge badge-success">Ditemukan</span>' :
                    '<span class="badge badge-light">Belum dicek</span>');
            }

            function conditionSelect(detailId, current) {
                const $sel = $('<select class="form-control form-control-sm opn-condition">')
                    .attr('data-detail-id', detailId).attr('data-prev', current);
                $.each(conditionOptions, function(value, label) {
                    $sel.append($('<option>').val(value).text(label).prop('selected', String(value) === String(current)));
                });
                return $sel;
            }

            function addWrongRow(res) {
                const a = res.asset;
                const $tr = $('<tr>').attr('data-detail-id', res.detail_id)
                    .append($('<td>').text(a.asset_name))
                    .append($('<td>').text(a.merk))
                    .append($('<td>').append($('<code>').text(a.serial_number)))
                    .append($('<td>').append($('<span class="badge badge-warning">').text(a.room_name || '-')))
                    .append($('<td>').append(conditionSelect(res.detail_id, a.asset_condition)))
                    .append($('<td class="text-nowrap">').html(
                        '<label class="btn btn-sm btn-light border mb-0 js-photo-btn" title="Foto bukti kondisi"><i class="fas fa-camera"></i>' +
                        '<input type="file" class="d-none js-photo-input" accept="image/jpeg,image/png,image/webp" capture="environment" multiple data-asset-id="' + parseInt(a.id_asset, 10) + '"></label> ' +
                        '<span class="badge badge-light js-photo-count" data-asset-id="' + parseInt(a.id_asset, 10) + '"><i class="fas fa-image mr-1"></i>0</span>'))
                    .append('<td class="text-right"><button type="button" class="btn btn-sm btn-link text-danger js-remove-wrong" title="Hapus dari daftar"><i class="fas fa-times"></i></button></td>');
                $('#wrongTable tbody').prepend($tr);
                $('#wrongCard').removeClass('d-none');
            }

            function showResult(status, message) {
                const $alert = $('<div class="alert py-2 mb-0">').addClass('alert-' + (alertClass[status] || 'secondary')).text(message);
                $result.empty().append($alert);
                if (navigator.vibrate) navigator.vibrate(status === 'found' ? 80 : [60, 60, 60]);
            }

            function submitSerial(value) {
                value = $.trim(value);
                if (!value) return;
                $.post(checkUrl, {
                    token: token,
                    serial: value
                }, null, 'json').done(function(res) {
                    showResult(res.status, res.message);
                    if (res.status === 'found') markRow(res.detail_id, true);
                    if (res.status === 'wrong_room') addWrongRow(res);
                    updateSummary(res.summary);
                }).fail(function() {
                    showResult('invalid', 'Gagal menghubungi server. Coba lagi.');
                }).always(function() {
                    $input.val('').trigger('focus');
                });
            }

            $('#scanForm').on('submit', function(e) {
                e.preventDefault();
                submitSerial($input.val());
            });

            $('#expectedTable').on('change', '.opn-check', function() {
                const $cb = $(this);
                const found = $cb.is(':checked');
                $.post(toggleUrl, {
                    token: token,
                    detail_id: $cb.val(),
                    found: found ? 1 : 0
                }, null, 'json').done(function(res) {
                    if (res.status !== 'ok') {
                        $cb.prop('checked', !found);
                        return showResult('invalid', 'Gagal menyimpan perubahan.');
                    }
                    markRow($cb.val(), found);
                    updateSummary(res.summary);
                }).fail(function() {
                    $cb.prop('checked', !found);
                    showResult('invalid', 'Gagal menghubungi server. Coba lagi.');
                });
            });

            $('#expectedTable, #wrongTable').on('change', '.opn-condition', function() {
                const $sel = $(this);
                const prev = $sel.attr('data-prev');
                $.post(conditionUrl, {
                    token: token,
                    detail_id: $sel.attr('data-detail-id'),
                    condition: $sel.val()
                }, null, 'json').done(function(res) {
                    if (res.status === 'failed' || res.status === 'closed') {
                        $sel.val(prev);
                        return showResult('invalid', res.message);
                    }
                    $sel.attr('data-prev', $sel.val());
                    if (res.marked_found) markRow(res.detail_id, true);
                    showResult('found', res.message);
                    updateSummary(res.summary);
                }).fail(function() {
                    $sel.val(prev);
                    showResult('invalid', 'Gagal menghubungi server. Coba lagi.');
                });
            });

            // Upload foto bukti kondisi (AJAX), langsung dari kamera HP
            $('#expectedTable, #wrongTable').on('change', '.js-photo-input', function() {
                const input = this;
                if (!input.files.length) return;
                const assetId = $(input).data('asset-id');
                const $btn = $(input).closest('.js-photo-btn').addClass('disabled');
                const fd = new FormData();
                fd.append('asset_id', assetId);
                fd.append('source', 'opname');
                fd.append('opname_token', token);
                $.each(input.files, function(i, f) {
                    fd.append('photos[]', f);
                });

                $.ajax({
                    url: photoUrl,
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                }).done(function(res) {
                    const ok = res.status === 'ok' || res.status === 'partial';
                    if (ok) {
                        $('.js-photo-count[data-asset-id="' + assetId + '"]').each(function() {
                            const n = (parseInt($(this).text(), 10) || 0) + res.photos.length;
                            $(this).removeClass('badge-light').addClass('badge-info').html('<i class="fas fa-image mr-1"></i>' + n);
                        });
                    }
                    showResult(ok ? (res.status === 'ok' ? 'found' : 'already') : 'invalid',
                        res.message + (res.errors && res.errors.length ? ' ' + res.errors.join(' ') : ''));
                }).fail(function() {
                    showResult('invalid', 'Gagal mengupload foto. Coba lagi.');
                }).always(function() {
                    $btn.removeClass('disabled');
                    input.value = '';
                });
            });

            $('#wrongTable').on('click', '.js-remove-wrong', function() {
                const $tr = $(this).closest('tr');
                $.post(toggleUrl, {
                    token: token,
                    detail_id: $tr.data('detail-id'),
                    found: 0
                }, null, 'json').done(function(res) {
                    if (res.status !== 'ok') return;
                    $tr.remove();
                    if ($('#wrongTable tbody tr').length === 0) $('#wrongCard').addClass('d-none');
                    updateSummary(res.summary);
                });
            });

            // Kamera di browser butuh secure context (HTTPS atau localhost)
            $('#btnCamera').on('click', function() {
                if (scanner) {
                    scanner.stop().catch(function() {});
                    scanner = null;
                    $('#reader').addClass('d-none').empty();
                    return;
                }
                if (!window.isSecureContext || !navigator.mediaDevices) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Kamera butuh HTTPS',
                        html: 'Browser hanya mengizinkan kamera di halaman HTTPS.<br>Untuk sekarang, scan label pakai <b>kamera HP</b> lalu tekan <b>Tandai ditemukan</b> di halaman aset, atau pakai scanner barcode di kolom input.'
                    });
                    return;
                }
                const start = function() {
                    $('#reader').removeClass('d-none');
                    scanner = new Html5Qrcode('reader');
                    scanner.start({
                        facingMode: 'environment'
                    }, {
                        fps: 10,
                        qrbox: 220
                    }, function(text) {
                        const now = Date.now();
                        if (text === lastCode && now - lastAt < 2500) return;
                        lastCode = text;
                        lastAt = now;
                        submitSerial(text);
                    }).catch(function(err) {
                        scanner = null;
                        $('#reader').addClass('d-none');
                        showResult('invalid', 'Kamera tidak bisa dibuka: ' + err);
                    });
                };
                if (window.Html5Qrcode) return start();
                $.getScript('https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js').done(start).fail(function() {
                    showResult('invalid', 'Gagal memuat library scanner kamera.');
                });
            });

            $('#btnFinish').on('click', function() {
                const missing = parseInt($('[data-stat="missing"]').text(), 10) || 0;
                Swal.fire({
                    icon: 'question',
                    title: 'Selesaikan opname?',
                    html: missing > 0 ?
                        '<b>' + missing + '</b> aset belum ditemukan dan akan dicatat sebagai <b>tidak ditemukan</b>.' :
                        'Semua aset terdaftar sudah ditemukan.',
                    showCancelButton: true,
                    confirmButtonText: 'Selesaikan',
                    cancelButtonText: 'Kembali'
                }).then(function(r) {
                    if (r.value) $('#finishForm').trigger('submit');
                });
            });

            $('#btnCancel').on('click', function() {
                Swal.fire({
                    icon: 'warning',
                    title: 'Batalkan opname?',
                    text: 'Semua hasil scan di sesi ini akan dihapus.',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    confirmButtonText: 'Ya, batalkan',
                    cancelButtonText: 'Kembali'
                }).then(function(r) {
                    if (r.value) $('#cancelForm').trigger('submit');
                });
            });
        })();
    </script>
<?php endif; ?>
