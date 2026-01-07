ALTER TABLE simanis.`transaction` ADD notes TEXT NULL;

ALTER TABLE simanis.transaction_details DROP COLUMN notes;
ALTER TABLE simanis.transaction_details ADD transaction_id BIGINT UNSIGNED NULL;