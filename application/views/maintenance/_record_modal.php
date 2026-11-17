<?php
/**
 * Modal "Catat servis / perbaikan".
 * Tombol pemicu: .js-record dengan data-mode="schedule" (+ data-token, data-title) atau data-mode="repair".
 * @var array  $assets
 * @var array  $types
 * @var string $back   path kembali setelah simpan
 */
?>
<div class="modal fade" id="recordModal" role="dialog" aria-labelledby="recordModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recordModalLabel">Catat Servis</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form method="post" action="<?= base_url('maintenance/record'); ?>" id="recordForm">
                <input type="hidden" name="token" value="">
                <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                <div class="modal-body">
                    <p class="font-weight-bold text-gray-800 js-record-title"></p>
                    <div class="js-repair-only">
                        <div class="form-group">
                            <label for="record_asset">Aset</label>
                            <select class="form-control js-asset" id="record_asset" name="asset_id" style="width: 100%;">
                                <option value="">-- Pilih aset --</option>
                                <?php foreach ($assets as $a) : ?>
                                    <option value="<?= $a['id_asset']; ?>"><?= html_escape($a['asset_name'] . ' — ' . $a['serial_number'] . ' (' . $a['room_name'] . ', ' . $a['building_name'] . ')'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="record_type">Jenis</label>
                            <select class="form-control" id="record_type" name="type">
                                <?php foreach ($types as $value => $label) : ?>
                                    <option value="<?= $value; ?>" <?= $value === 'perbaikan' ? 'selected' : ''; ?>><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="performed_at">Tanggal dikerjakan</label>
                            <input type="date" class="form-control" id="performed_at" name="performed_at" max="<?= date('Y-m-d'); ?>" value="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="form-group col-6">
                            <label for="record_cost">Biaya (Rp)</label>
                            <input type="number" class="form-control" id="record_cost" name="cost" min="0" step="1" placeholder="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="record_description">Keterangan pekerjaan</label>
                        <textarea class="form-control" id="record_description" name="description" rows="2" maxlength="2000" required placeholder="mis. Cuci indoor & outdoor, tambah freon"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label for="record_vendor">Teknisi / vendor</label>
                            <input type="text" class="form-control" id="record_vendor" name="vendor" maxlength="100" placeholder="opsional">
                        </div>
                        <div class="form-group col-6">
                            <label for="condition_after">Kondisi setelahnya</label>
                            <select class="form-control" id="condition_after" name="condition_after">
                                <option value="">Tidak berubah</option>
                                <?php foreach (asset_condition_options() as $value => $label) : ?>
                                    <option value="<?= $value; ?>"><?= $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <small class="text-muted js-schedule-only">Setelah disimpan, jatuh tempo berikutnya dihitung dari tanggal dikerjakan.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        $(document).on('click', '.js-record', function() {
            const d = $(this).data();
            const $form = $('#recordForm');
            const isSchedule = d.mode === 'schedule';

            $form[0].reset();
            $form.find('[name="token"]').val(isSchedule ? d.token : '');
            $('#recordModalLabel').text(isSchedule ? 'Catat Servis Terjadwal' : 'Catat Perbaikan / Perawatan');
            $form.find('.js-record-title').text(isSchedule ? d.title : '').toggleClass('d-none', !isSchedule);
            $form.find('.js-repair-only').toggleClass('d-none', isSchedule);
            $form.find('.js-schedule-only').toggleClass('d-none', !isSchedule);
            $('#record_asset').prop('required', !isSchedule).val(d.asset || '').trigger('change');
            $('#recordModal').modal('show');
        });
    });
</script>
