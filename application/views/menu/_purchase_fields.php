<?php
/**
 * Field data pembelian, dipakai form tambah aset (modal) dan edit aset.
 * @var array|null $purchase         data aset (untuk edit), null untuk tambah
 * @var int|string $warranty_months  jumlah bulan garansi (edit)
 */
$purchase        = $purchase ?? [];
$warranty_months = $warranty_months ?? '';
?>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="purchase_date">Tanggal Perolehan</label>
        <input type="date" class="form-control" id="purchase_date" name="purchase_date" max="<?= date('Y-m-d'); ?>" value="<?= html_escape($purchase['purchase_date'] ?? ''); ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="purchase_price">Harga Perolehan (Rp)</label>
        <input type="number" class="form-control" id="purchase_price" name="purchase_price" min="0" step="1" placeholder="mis. 12000000" value="<?= isset($purchase['purchase_price']) && $purchase['purchase_price'] !== null ? (int) $purchase['purchase_price'] : ''; ?>">
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="vendor">Vendor / Pemasok</label>
        <input type="text" class="form-control" id="vendor" name="vendor" maxlength="100" placeholder="mis. PT Sumber Elektronik" value="<?= html_escape($purchase['vendor'] ?? ''); ?>">
    </div>
    <div class="form-group col-md-6">
        <label for="warranty_months">Garansi (bulan)</label>
        <input type="number" class="form-control" id="warranty_months" name="warranty_months" min="0" max="240" step="1" placeholder="mis. 12" value="<?= html_escape((string) $warranty_months); ?>">
        <small class="form-text text-muted">Dihitung dari tanggal perolehan.</small>
    </div>
</div>
