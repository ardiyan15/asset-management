-- Data pembelian aset + parameter penyusutan garis lurus per kategori.
--
-- asset.category_id      : kategori aset (sebelumnya hanya tertanam di nomor seri)
-- asset.purchase_date    : tanggal perolehan (awal penyusutan)
-- asset.purchase_price   : harga perolehan (Rp)
-- asset.vendor           : pemasok
-- asset.warranty_until   : garansi berlaku sampai tanggal ini
-- categories.useful_life_years : masa manfaat (tahun); NULL = belum diatur, aset tidak disusutkan
-- categories.residual_percent  : nilai sisa (% dari harga perolehan) di akhir masa manfaat

ALTER TABLE simanis.asset ADD category_id INT NULL AFTER merk;
ALTER TABLE simanis.asset ADD purchase_date DATE NULL AFTER disposed_at;
ALTER TABLE simanis.asset ADD purchase_price DECIMAL(15,2) NULL AFTER purchase_date;
ALTER TABLE simanis.asset ADD vendor varchar(100) NULL AFTER purchase_price;
ALTER TABLE simanis.asset ADD warranty_until DATE NULL AFTER vendor;

ALTER TABLE simanis.categories ADD useful_life_years TINYINT UNSIGNED NULL AFTER name;
ALTER TABLE simanis.categories ADD residual_percent DECIMAL(5,2) NOT NULL DEFAULT 0 AFTER useful_life_years;

-- Isi category_id aset lama dari nomor seri: <ruangan><KODE KATEGORI><yymm><nomor urut>.
-- Kode kategori = huruf/angka tepat sebelum 8-9 digit terakhir. Kategori aktif didahulukan.
UPDATE simanis.asset a
JOIN simanis.categories c
  ON c.status = 1 AND a.serial_number REGEXP CONCAT(c.code, '[0-9]{8,9}$')
SET a.category_id = c.id
WHERE a.category_id IS NULL;

UPDATE simanis.asset a
JOIN simanis.categories c
  ON a.serial_number REGEXP CONCAT(c.code, '[0-9]{8,9}$')
SET a.category_id = c.id
WHERE a.category_id IS NULL;

-- Contoh masa manfaat (silakan sesuaikan di menu Data Kategori)
UPDATE simanis.categories SET useful_life_years = 4, residual_percent = 0  WHERE code = 'ELC' AND useful_life_years IS NULL;
UPDATE simanis.categories SET useful_life_years = 8, residual_percent = 10 WHERE code = 'FUR' AND useful_life_years IS NULL;
UPDATE simanis.categories SET useful_life_years = 8, residual_percent = 0  WHERE code = 'SAN' AND useful_life_years IS NULL;
