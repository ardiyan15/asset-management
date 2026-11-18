-- Foto aset (satu aset bisa punya banyak foto). File disimpan di uploads/assets/<asset_id>/,
-- nama file acak; setiap upload di-encode ulang ke JPEG (EXIF dibuang) + thumbnail.
--
-- source          : manual (halaman aset) | scan (halaman scan QR) | opname (bukti saat stock opname)
-- stock_opname_id : diisi kalau foto diambil sebagai bukti kondisi saat opname
-- is_primary      : foto utama yang tampil di daftar aset & halaman scan (maks. satu per aset)

CREATE TABLE simanis.`asset_photos` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	asset_id INT NOT NULL,
	file_name varchar(100) NOT NULL,
	thumb_name varchar(100) NOT NULL,
	original_name varchar(150) NULL,
	width SMALLINT UNSIGNED NULL,
	height SMALLINT UNSIGNED NULL,
	size_bytes INT UNSIGNED NULL,
	caption varchar(150) NULL,
	source varchar(20) NOT NULL DEFAULT 'manual',
	stock_opname_id BIGINT UNSIGNED NULL,
	is_primary TINYINT NOT NULL DEFAULT 0,
	uploaded_by INT NULL,
	created_at DATETIME NOT NULL,
	PRIMARY KEY (id),
	KEY asset_photos_asset_idx (asset_id, is_primary),
	KEY asset_photos_opname_idx (stock_opname_id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
