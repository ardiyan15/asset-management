-- Stock opname (audit fisik aset per ruangan)
-- stock_opname.status          : 0 = sedang berjalan, 1 = selesai
-- stock_opname_details.result  : 0 = belum/tidak ditemukan, 1 = ditemukan, 2 = salah ruangan
--   (result 2 = aset terdaftar di ruangan lain tapi fisiknya ditemukan di ruangan yang diaudit)

CREATE TABLE simanis.`stock_opname` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	code varchar(50) NOT NULL,
	room_id INT NOT NULL,
	user_id INT NOT NULL,
	status TINYINT NOT NULL DEFAULT 0,
	notes TEXT NULL,
	started_at DATETIME NOT NULL,
	finished_at DATETIME NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NULL,
	PRIMARY KEY (id),
	UNIQUE KEY stock_opname_code_unique (code),
	KEY stock_opname_room_status_idx (room_id, status)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE simanis.`stock_opname_details` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	stock_opname_id BIGINT UNSIGNED NOT NULL,
	asset_id INT NOT NULL,
	expected_room_id INT NOT NULL,
	result TINYINT NOT NULL DEFAULT 0,
	checked_at DATETIME NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NULL,
	PRIMARY KEY (id),
	UNIQUE KEY stock_opname_details_asset_unique (stock_opname_id, asset_id),
	CONSTRAINT stock_opname_details_header_fk FOREIGN KEY (stock_opname_id) REFERENCES simanis.`stock_opname` (id) ON DELETE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
