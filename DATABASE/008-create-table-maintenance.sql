-- Jadwal maintenance aset + riwayat perawatan/perbaikan beserta biayanya.
--
-- maintenance_schedules.interval_unit : day | week | month | year
--   next_due_date dihitung ulang setiap kali servis dicatat: tanggal servis + interval.
--   last_notified_at mencegah email pengingat yang sama terkirim berkali-kali dalam sehari.
-- maintenance_logs.type : rutin (dari jadwal) | perbaikan (insidental)
--   condition_after opsional; kalau diisi, kondisi aset ikut diperbarui (asset_condition_logs source 'maintenance').
-- user.email : alamat penerima notifikasi jatuh tempo (diisi di Edit Profil)

ALTER TABLE simanis.`user` ADD email varchar(100) NULL AFTER username;

CREATE TABLE simanis.`maintenance_schedules` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	asset_id INT NOT NULL,
	title varchar(100) NOT NULL,
	interval_value SMALLINT UNSIGNED NOT NULL,
	interval_unit varchar(10) NOT NULL,
	next_due_date DATE NOT NULL,
	last_done_date DATE NULL,
	remind_days_before SMALLINT UNSIGNED NOT NULL DEFAULT 7,
	notes varchar(255) NULL,
	is_active TINYINT NOT NULL DEFAULT 1,
	last_notified_at DATETIME NULL,
	created_by INT NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NULL,
	PRIMARY KEY (id),
	KEY maintenance_schedules_due_idx (is_active, next_due_date),
	KEY maintenance_schedules_asset_idx (asset_id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE simanis.`maintenance_logs` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	asset_id INT NOT NULL,
	schedule_id BIGINT UNSIGNED NULL,
	type varchar(20) NOT NULL DEFAULT 'rutin',
	performed_at DATE NOT NULL,
	description TEXT NOT NULL,
	vendor varchar(100) NULL,
	cost DECIMAL(15,2) NOT NULL DEFAULT 0,
	condition_after TINYINT NULL,
	performed_by INT NULL,
	created_at DATETIME NOT NULL,
	PRIMARY KEY (id),
	KEY maintenance_logs_asset_idx (asset_id, performed_at),
	KEY maintenance_logs_date_idx (performed_at),
	CONSTRAINT maintenance_logs_schedule_fk FOREIGN KEY (schedule_id) REFERENCES simanis.`maintenance_schedules` (id) ON DELETE SET NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
