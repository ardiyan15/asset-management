-- Penghapusan (disposal) aset: pengajuan oleh user, disetujui/ditolak admin per aset.
-- asset.is_disposed                 : 0 = aktif, 1 = sudah dihapus (data tetap disimpan untuk riwayat & BAST)
-- asset_disposal_details.status     : 0 = diajukan, 1 = disetujui, 2 = ditolak
-- asset_disposals.method            : musnah | jual | hibah | lainnya

ALTER TABLE simanis.asset ADD is_disposed TINYINT NOT NULL DEFAULT 0 AFTER asset_condition;
ALTER TABLE simanis.asset ADD disposed_at DATETIME NULL AFTER is_disposed;

CREATE TABLE simanis.`asset_disposals` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	code varchar(50) NOT NULL,
	method varchar(20) NOT NULL,
	reason TEXT NOT NULL,
	requested_by INT NOT NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NULL,
	PRIMARY KEY (id),
	UNIQUE KEY asset_disposals_code_unique (code)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE simanis.`asset_disposal_details` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	disposal_id BIGINT UNSIGNED NOT NULL,
	asset_id INT NOT NULL,
	room_id INT NOT NULL,
	condition_at_request TINYINT NOT NULL,
	status TINYINT NOT NULL DEFAULT 0,
	reviewed_by INT NULL,
	reviewed_at DATETIME NULL,
	review_notes varchar(255) NULL,
	created_at DATETIME NOT NULL,
	updated_at DATETIME NULL,
	PRIMARY KEY (id),
	KEY asset_disposal_details_asset_status_idx (asset_id, status),
	CONSTRAINT asset_disposal_details_header_fk FOREIGN KEY (disposal_id) REFERENCES simanis.`asset_disposals` (id) ON DELETE CASCADE
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
