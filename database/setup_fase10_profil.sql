-- ─────────────────────────────────────────────
-- DDL FASE 10: Tambah kolom foto_profil ke tabel users
-- ─────────────────────────────────────────────
USE db_keuangan_pribadi;

ALTER TABLE users 
ADD COLUMN foto_profil VARCHAR(255) DEFAULT NULL AFTER password_hash;

SELECT 'Kolom foto_profil berhasil ditambahkan!' AS status;
