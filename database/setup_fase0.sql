-- ============================================================
-- DDL Setup Database: db_keuangan_pribadi
-- Versi: 1.0.0 (FASE 0)
-- Kompatibel: MySQL 8.0.30
-- ============================================================

-- Buat database jika belum ada
CREATE DATABASE IF NOT EXISTS `db_keuangan_pribadi`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `db_keuangan_pribadi`;

-- ============================================================
-- TABEL: ci_sessions
-- Diperlukan oleh CodeIgniter 3 saat sess_driver = 'database'
-- Struktur ini adalah STANDAR RESMI dari dokumentasi CI3
-- ============================================================
CREATE TABLE IF NOT EXISTS `ci_sessions` (
    `id`         varchar(128) NOT NULL,          -- Session ID (hash)
    `ip_address` varchar(45)  NOT NULL,          -- IP address user (support IPv6)
    `timestamp`  int(10)      UNSIGNED NOT NULL DEFAULT 0, -- Unix timestamp terakhir aktif
    `data`       blob         NOT NULL,          -- Data session (serialized)

    -- Primary key berdasarkan kombinasi id + ip (jika sess_match_ip = TRUE)
    -- Karena kita set sess_match_ip = FALSE, primary key cukup id saja
    PRIMARY KEY (`id`),
    KEY `ci_sessions_timestamp` (`timestamp`)   -- Index untuk garbage collection
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABEL: users
-- Akan diisi lebih lengkap di FASE 1 (Auth),
-- namun di sini dibuat skeleton agar FASE 0 bisa diuji
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         int(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
    `nama`       varchar(100) NOT NULL,
    `email`      varchar(150) NOT NULL,
    `password`   varchar(255) NOT NULL,           -- BCrypt hash, BUKAN plaintext
    `created_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- CATATAN PENTING untuk Developer:
-- ============================================================
-- 1. Kolom uang (saldo, jumlah, target) di tabel-tabel berikutnya
--    WAJIB menggunakan DECIMAL(15,2) — BUKAN FLOAT/DOUBLE.
--    Ini akan diterapkan penuh mulai FASE 1 ke atas.
--
-- 2. Tabel ci_sessions HARUS sudah ada sebelum aplikasi dijalankan,
--    karena CodeIgniter akan error saat mencoba menyimpan session.
--
-- 3. Selalu jalankan file ini di Navicat atau MySQL CLI:
--    mysql -u root < setup_fase0.sql
-- ============================================================
