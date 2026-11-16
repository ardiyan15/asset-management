-- Kondisi fisik aset + log perubahannya.
-- asset.asset_condition : 1 = Baik, 2 = Rusak Ringan, 3 = Rusak Berat
--   (nama kolom bukan `condition` karena CONDITION adalah reserved word di MySQL)
-- asset_condition_logs.source : manual | scan | opname

ALTER TABLE simanis.asset ADD asset_condition TINYINT NOT NULL DEFAULT 1 AFTER placement_status;

CREATE TABLE simanis.`asset_condition_logs` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	asset_id INT NOT NULL,
	old_condition TINYINT NULL,
	new_condition TINYINT NOT NULL,
	notes varchar(255) NULL,
	source varchar(20) NOT NULL DEFAULT 'manual',
	stock_opname_id BIGINT UNSIGNED NULL,
	user_id INT NULL,
	created_at DATETIME NOT NULL,
	PRIMARY KEY (id),
	KEY asset_condition_logs_asset_idx (asset_id, id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
