-- DATA DEMO: data pembelian (harga, tanggal, vendor, garansi) untuk aset contoh.
-- Bukan migrasi skema; jalankan setelah 007-add-asset-purchase-depreciation.sql kalau perlu data contoh.
-- Hanya mengisi aset yang purchase_price-nya masih kosong, jadi data yang sudah diisi manual tidak tertimpa
-- dan file ini aman dijalankan berulang. Nama vendor fiktif.
--
-- Variasi yang sengaja dibuat untuk demo laporan Nilai Aset:
--   - Elektronik 2021 (masa manfaat 4 th)  → sudah habis disusutkan (nilai buku Rp 0)
--   - Furnitur 2020-2021 (8 th, sisa 10%)  → masih di tengah masa manfaat
--   - Aquarium 2018 (Furnitur)             → masa manfaat habis, nilai buku mentok di nilai sisa 10%
--   - Aset 2025-2026                       → baru, garansi masih aktif / hampir habis

UPDATE simanis.asset a
JOIN (
    SELECT 'M101ELC21040001' AS sn, '2021-03-15' AS pd, 4250000  AS price, 'CV Sejuk Abadi'           AS vendor, '2022-03-15' AS wu UNION ALL
    SELECT 'M101ELC21040002',        '2021-03-20',       175000,           'Toko Serba Ada',                    NULL         UNION ALL
    SELECT 'M101FUR21040003',        '2021-02-10',       1850000,          'CV Mebel Jaya',                     '2021-08-10' UNION ALL
    SELECT 'M101ELC21040004',        '2021-03-20',       1250000,          'Toko Elektronik Makmur',            '2022-03-20' UNION ALL
    SELECT 'M101FUR21040005',        '2021-02-10',       950000,           'CV Mebel Jaya',                     NULL         UNION ALL
    SELECT 'M102FUR21040006',        '2021-02-10',       1850000,          'CV Mebel Jaya',                     '2021-08-10' UNION ALL
    SELECT 'M102ELC21040007',        '2021-03-15',       12000000,         'CV Sejuk Abadi',                    '2022-03-15' UNION ALL
    SELECT 'M102ELC21040008',        '2021-03-20',       175000,           'Toko Serba Ada',                    NULL         UNION ALL
    SELECT 'M102SAN21040009',        '2021-01-25',       285000,           'UD Sumber Plastik',                 NULL         UNION ALL
    SELECT 'M102FUR21040010',        '2020-11-05',       1450000,          'CV Alat Kantor Sentosa',            '2021-11-05' UNION ALL
    SELECT 'M102ELC21040011',        '2021-04-05',       350000,           'Toko Alat Kesehatan Medika',        NULL         UNION ALL
    SELECT 'M201FUR21040012',        '2021-02-10',       2450000,          'CV Mebel Jaya',                     '2021-08-10' UNION ALL
    SELECT 'M201FUR21040013',        '2021-02-10',       1350000,          'CV Mebel Jaya',                     NULL         UNION ALL
    SELECT 'M201ELC21040014',        '2021-03-20',       1250000,          'Toko Elektronik Makmur',            '2022-03-20' UNION ALL
    SELECT 'M201ELC21040015',        '2021-01-15',       425000,           'CV Telekomindo',                    '2022-01-15' UNION ALL
    SELECT 'M201FUR21040016',        '2020-12-01',       1150000,          'Toko Gorden Indah',                 NULL         UNION ALL
    SELECT 'M202FUR21040017',        '2019-08-20',       3750000,          'CV Mebel Jaya',                     '2020-08-20' UNION ALL
    SELECT 'M202ELC21040018',        '2021-03-01',       5499000,          'Toko Elektronik Makmur',            '2023-03-01' UNION ALL
    SELECT 'M202FUR21040019',        '2020-11-05',       1750000,          'CV Alat Kantor Sentosa',            NULL         UNION ALL
    SELECT 'L102FUR21040020',        '2020-12-01',       2300000,          'Toko Gorden Indah',                 NULL         UNION ALL
    SELECT 'L102FUR21040021',        '2021-02-10',       2950000,          'CV Mebel Jaya',                     '2022-02-10' UNION ALL
    SELECT 'L102ELC21040022',        '2021-01-15',       425000,           'CV Telekomindo',                    '2022-01-15' UNION ALL
    SELECT 'L202FUR21040023',        '2018-06-12',       1600000,          'Toko Akuarium Bahari',              NULL         UNION ALL
    SELECT 'L202SAN21040024',        '2021-01-25',       285000,           'UD Sumber Plastik',                 NULL         UNION ALL
    SELECT 'L202ELC21040025',        '2021-01-15',       425000,           'CV Telekomindo',                    '2022-01-15' UNION ALL
    SELECT 'M204ASAN21040026',       '2021-04-10',       850000,           'Toko Furnitur Nusantara',           NULL         UNION ALL
    SELECT 'L505SAN21040027',        '2021-04-01',       6500000,          'CV Sejuk Abadi',                    '2022-04-01' UNION ALL
    SELECT 'L600ELC261100028',       '2025-12-10',       3200000,          'Toko Elektronik Makmur',            '2026-12-10' UNION ALL
    SELECT 'L505ELC261100029',       '2026-11-01',       2799000,          'Toko Gadget Prima',                 '2027-11-01' UNION ALL
    SELECT 'L600ELC261100030',       '2026-11-10',       13500000,         'PT Mitra Komputer Utama',           '2028-11-10'
) s ON s.sn = a.serial_number
SET a.purchase_date  = s.pd,
    a.purchase_price = s.price,
    a.vendor         = s.vendor,
    a.warranty_until = s.wu
WHERE a.purchase_price IS NULL;
