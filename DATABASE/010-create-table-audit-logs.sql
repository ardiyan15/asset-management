-- Audit log aktivitas user: siapa melakukan apa, terhadap data apa, kapan, dan dari mana.
-- Tabel ini hanya ditambah (append-only); aplikasi tidak menyediakan ubah/hapus log.
--
-- action      : kode aksi bertitik, mis. auth.login, asset.update, disposal.approve, report.export
-- module      : bagian depan action (auth, asset, transaction, ...), disimpan terpisah untuk filter
-- entity_*    : data yang disentuh (tipe, id, label yang mudah dibaca seperti nomor seri / kode transaksi)
-- changes     : JSON {"field": {"old": .., "new": ..}} untuk aksi ubah data (tanpa password)
-- building_id : gedung terkait (untuk filter); NULL kalau tidak terkait gedung tertentu

CREATE TABLE simanis.`audit_logs` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	user_id INT NULL,
	username varchar(40) NULL,
	role_id INT NULL,
	action varchar(60) NOT NULL,
	module varchar(30) NOT NULL,
	description varchar(255) NOT NULL,
	entity_type varchar(30) NULL,
	entity_id varchar(64) NULL,
	entity_label varchar(150) NULL,
	changes JSON NULL,
	building_id INT NULL,
	ip_address varchar(45) NULL,
	user_agent varchar(255) NULL,
	url varchar(255) NULL,
	created_at DATETIME NOT NULL,
	PRIMARY KEY (id),
	KEY audit_logs_created_idx (created_at),
	KEY audit_logs_user_idx (user_id, created_at),
	KEY audit_logs_module_idx (module, created_at),
	KEY audit_logs_entity_idx (entity_type, entity_id)
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;
