-- Peminjaman aset sementara (proyektor, laptop, dll). Berbeda dengan pindah ruangan:
-- lokasi aset (asset.room_id) tidak berubah, aset hanya dibawa peminjam sampai tanggal kembali.
--
-- asset_loans.status : 0 = sedang dipinjam, 1 = sudah dikembalikan
--   Terlambat = status 0 dan due_date < hari ini (dihitung, tidak disimpan).
-- condition_out / condition_in : kondisi aset saat dipinjam / dikembalikan (1 Baik, 2 Rusak Ringan, 3 Rusak Berat)
-- last_notified_at : mencegah email pengingat keterlambatan terkirim berulang dalam sehari

CREATE TABLE simanis.`asset_loans` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	code varchar(50) NOT NULL,
	asset_id INT NOT NULL,
	borrower_name varchar(100) NOT NULL,
	borrower_unit varchar(100) NULL,
	borrower_phone varchar(30) NULL,
	purpose varchar(255) NULL,
	loaned_at DATETIME NOT NULL,
	due_date DATE NOT NULL,
	returned_at DATETIME NULL,
	condition_out TINYINT NOT NULL,
	condition_in TINYINT NULL,
	return_notes varchar(255) NULL,
	status TINYINT NOT NULL DEFAULT 0,
	extended_count TINYINT UNSIGNED NOT NULL DEFAULT 0,
	last_notified_at DATETIME NULL,
	created_by INT NULL,
	returned_by INT NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NULL,
	PRIMARY KEY (id),
	UNIQUE KEY asset_loans_code_unique (code),
	KEY asset_loans_asset_status_idx (asset_id, status),
	KEY asset_loans_status_due_idx (status, due_date)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
