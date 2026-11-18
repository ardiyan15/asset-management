<?php
/**
 * Galeri foto aset + form upload. Tanpa ketergantungan JS Bootstrap (dipakai juga di halaman scan).
 *
 * @var array  $asset     minimal id_asset
 * @var array  $photos    hasil Photo_model::for_asset()
 * @var bool   $can_edit  boleh upload / hapus / pilih utama
 * @var string $back      path kembali setelah aksi (asset/update/<id> atau scan/<serial>)
 * @var bool   $camera    tampilkan atribut capture (kamera HP) di input file
 */
$camera  = $camera ?? false;
$result  = $this->session->flashdata('photo_result');
$sources = Photo_model::SOURCES;
?>
<style>
    .ph-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: .6rem; }
    .ph-item { position: relative; border: 1px solid #e3e6f0; border-radius: .5rem; overflow: hidden; background: #f8f9fc; }
    .ph-item img { width: 100%; aspect-ratio: 1 / 1; object-fit: cover; display: block; }
    .ph-item .ph-badge { position: absolute; top: .35rem; left: .35rem; }
    .ph-item .ph-meta { padding: .3rem .45rem; font-size: .72rem; line-height: 1.25; color: #6e707e; }
    .ph-item .ph-actions { display: flex; gap: .25rem; padding: 0 .45rem .45rem; }
    .ph-item .ph-actions form { margin: 0; flex: 1; }
    .ph-item .ph-actions button { width: 100%; padding: .15rem .3rem; font-size: .72rem; }
</style>

<?php if (!empty($result)) : ?>
    <div class="alert alert-<?= $result['status'] === 'ok' ? 'success' : ($result['status'] === 'partial' ? 'warning' : 'danger'); ?> py-2 small">
        <?= html_escape($result['message']); ?>
        <?php foreach ($result['errors'] ?? [] as $err) : ?>
            <div><?= html_escape($err); ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if (empty($photos)) : ?>
    <p class="small text-muted mb-2">Belum ada foto.</p>
<?php else : ?>
    <div class="ph-grid mb-3">
        <?php foreach ($photos as $p) : ?>
            <div class="ph-item">
                <a href="<?= Photo_model::url($p['asset_id'], $p['file_name']); ?>" target="_blank" rel="noopener" title="Buka ukuran penuh">
                    <img src="<?= Photo_model::url($p['asset_id'], $p['thumb_name']); ?>" alt="<?= html_escape($p['caption'] ?: 'Foto aset'); ?>" loading="lazy">
                </a>
                <?php if ((int) $p['is_primary'] === 1) : ?>
                    <span class="badge badge-primary ph-badge">Utama</span>
                <?php elseif ($p['source'] === 'opname') : ?>
                    <span class="badge badge-info ph-badge">Opname</span>
                <?php endif; ?>
                <div class="ph-meta">
                    <?php if ($p['caption']) : ?><div class="text-gray-800"><?= html_escape($p['caption']); ?></div><?php endif; ?>
                    <?= date('d M Y', strtotime($p['created_at'])); ?> · <?= html_escape($p['uploaded_by_name'] ?? '-'); ?>
                    <?php if ($p['opname_code']) : ?><div><?= html_escape($p['opname_code']); ?></div><?php elseif ($p['source'] !== 'manual') : ?><div><?= html_escape($sources[$p['source']] ?? $p['source']); ?></div><?php endif; ?>
                </div>
                <?php if ($can_edit) : ?>
                    <div class="ph-actions">
                        <?php if ((int) $p['is_primary'] !== 1) : ?>
                            <form method="post" action="<?= base_url('photo/set_primary'); ?>">
                                <input type="hidden" name="photo_id" value="<?= (int) $p['id']; ?>">
                                <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                                <button type="submit" class="btn btn-light border" title="Jadikan foto utama"><i class="fas fa-star"></i></button>
                            </form>
                        <?php endif; ?>
                        <form method="post" action="<?= base_url('photo/delete'); ?>" onsubmit="return confirm('Hapus foto ini?');">
                            <input type="hidden" name="photo_id" value="<?= (int) $p['id']; ?>">
                            <input type="hidden" name="back" value="<?= html_escape($back); ?>">
                            <button type="submit" class="btn btn-light border text-danger" title="Hapus foto"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($can_edit) : ?>
    <?php if (count($photos) < Photo_model::MAX_PER_ASSET) : ?>
        <form method="post" action="<?= base_url('photo/upload'); ?>" enctype="multipart/form-data">
            <input type="hidden" name="asset_id" value="<?= (int) $asset['id_asset']; ?>">
            <input type="hidden" name="source" value="<?= $camera ? 'scan' : 'manual'; ?>">
            <input type="hidden" name="back" value="<?= html_escape($back); ?>">
            <div class="form-group mb-2">
                <input type="file" name="photos[]" class="form-control-file" accept="image/jpeg,image/png,image/webp" multiple required <?= $camera ? 'capture="environment"' : ''; ?>>
                <small class="form-text text-muted">JPG / PNG / WEBP, maks. 10 MB per foto, <?= Photo_model::MAX_PER_ASSET - count($photos); ?> slot tersisa. Foto otomatis diperkecil.</small>
            </div>
            <div class="form-group mb-2">
                <input type="text" name="caption" class="form-control form-control-sm" maxlength="150" placeholder="Keterangan (opsional), mis. tampak depan / label serial">
            </div>
            <button type="submit" class="btn btn-primary btn-sm<?= $camera ? ' btn-block' : ''; ?>"><i class="fas fa-camera mr-1"></i> Upload foto</button>
        </form>
    <?php else : ?>
        <p class="small text-muted mb-0">Batas <?= Photo_model::MAX_PER_ASSET; ?> foto per aset tercapai. Hapus foto lama untuk menambah.</p>
    <?php endif; ?>
<?php endif; ?>
