CREATE TABLE simanis.`transaction` (
	id BIGINT UNSIGNED auto_increment NOT NULL,
	transaction_id varchar(100) NOT NULL,
	created_at DATETIME NULL,
	updated_at DATETIME NULL
)
ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_0900_ai_ci;