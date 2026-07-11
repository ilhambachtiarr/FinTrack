-- ============================================================
-- DDL FASE 1: Skema Database db_keuangan_pribadi
-- Versi  : 1.1.0
-- MySQL  : 8.0.30
-- Charset: utf8mb4 / utf8mb4_unicode_ci
-- ============================================================

-- ─────────────────────────────────────────────
-- 1. DATABASE
-- ─────────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS db_keuangan_pribadi
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE db_keuangan_pribadi;

-- ─────────────────────────────────────────────
-- 0. HAPUS TABEL LAMA (urutan penting: child dulu, baru parent)
--    Aman dijalankan ulang tanpa error.
-- ─────────────────────────────────────────────
DROP TABLE IF EXISTS tujuan_keuangan;
DROP TABLE IF EXISTS transaksi;
DROP TABLE IF EXISTS kategori;
DROP TABLE IF EXISTS akun;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS ci_sessions;


-- ─────────────────────────────────────────────
-- 2. ci_sessions  (wajib untuk sess_driver=database)
-- ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS ci_sessions (
    id         VARCHAR(128) NOT NULL,
    ip_address VARCHAR(45)  NOT NULL,
    timestamp  INT UNSIGNED NOT NULL DEFAULT 0,
    data       BLOB         NOT NULL,
    PRIMARY KEY (id),
    KEY idx_ci_sessions_timestamp (timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 3. USERS
-- ─────────────────────────────────────────────
CREATE TABLE users (
    id                    INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    name                  VARCHAR(100)  NOT NULL,
    email                 VARCHAR(150)  NOT NULL,
    password_hash         VARCHAR(255)  NOT NULL,
    failed_login_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until          DATETIME      NULL DEFAULT NULL,
    created_at            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                  ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 4. AKUN (rekening / dompet / e-wallet)
-- ─────────────────────────────────────────────
CREATE TABLE akun (
    id          INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id     INT UNSIGNED  NOT NULL,
    nama_akun   VARCHAR(100)  NOT NULL,
    jenis_akun  VARCHAR(50)   NOT NULL DEFAULT 'bank',
    saldo       DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    kode_warna  VARCHAR(7)    NOT NULL DEFAULT '#3B82F6',
    ikon        VARCHAR(50)   NULL DEFAULT NULL,
    is_active   TINYINT(1)    NOT NULL DEFAULT 1,
    urutan      TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                      ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_akun_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_akun_user        (user_id),
    INDEX idx_akun_user_active (user_id, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 5. KATEGORI
-- ─────────────────────────────────────────────
CREATE TABLE kategori (
    id             INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id        INT UNSIGNED  NOT NULL,
    nama_kategori  VARCHAR(100)  NOT NULL,
    tipe           ENUM('pemasukan','pengeluaran') NOT NULL,
    ikon           VARCHAR(50)   DEFAULT NULL,
    kode_warna     VARCHAR(7)    NOT NULL DEFAULT '#6B7280',
    is_default     TINYINT(1)    NOT NULL DEFAULT 0,
    created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_kategori_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_kategori_user_tipe (user_id, tipe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 6. TRANSAKSI
-- ─────────────────────────────────────────────
CREATE TABLE transaksi (
    id                  INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id             INT UNSIGNED  NOT NULL,
    akun_id             INT UNSIGNED  NOT NULL,
    kategori_id         INT UNSIGNED  NULL DEFAULT NULL,
    tipe                ENUM('pemasukan','pengeluaran','transfer') NOT NULL,
    jumlah              DECIMAL(15,2) NOT NULL,
    tanggal_transaksi   DATE          NOT NULL,
    catatan             VARCHAR(255)  DEFAULT NULL,
    lampiran            VARCHAR(255)  DEFAULT NULL,
    target_akun_id      INT UNSIGNED  NULL DEFAULT NULL,
    deleted_at          DATETIME      NULL DEFAULT NULL,
    created_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                              ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_transaksi_user        FOREIGN KEY (user_id)
        REFERENCES users(id)    ON DELETE CASCADE,
    CONSTRAINT fk_transaksi_akun        FOREIGN KEY (akun_id)
        REFERENCES akun(id)     ON DELETE RESTRICT,
    CONSTRAINT fk_transaksi_kategori    FOREIGN KEY (kategori_id)
        REFERENCES kategori(id) ON DELETE SET NULL,
    CONSTRAINT fk_transaksi_target_akun FOREIGN KEY (target_akun_id)
        REFERENCES akun(id)     ON DELETE RESTRICT,
    INDEX idx_transaksi_user_tanggal  (user_id, tanggal_transaksi),
    INDEX idx_transaksi_akun          (akun_id),
    INDEX idx_transaksi_tipe          (tipe),
    INDEX idx_transaksi_deleted       (deleted_at),
    INDEX idx_transaksi_user_kategori (user_id, kategori_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 7. TUJUAN KEUANGAN (financial goals)
-- ─────────────────────────────────────────────
CREATE TABLE tujuan_keuangan (
    id              INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    user_id         INT UNSIGNED  NOT NULL,
    nama_tujuan     VARCHAR(150)  NOT NULL,
    deskripsi       VARCHAR(255)  NULL DEFAULT NULL,
    target_jumlah   DECIMAL(15,2) NOT NULL,
    saldo_saat_ini  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    tgl_target      DATE          DEFAULT NULL,
    ikon            VARCHAR(50)   NULL DEFAULT NULL,
    kode_warna      VARCHAR(7)    NOT NULL DEFAULT '#10B981',
    status          ENUM('aktif','tercapai','dibatalkan') NOT NULL DEFAULT 'aktif',
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                                          ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_goal_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_goal_user        (user_id),
    INDEX idx_goal_user_status (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─────────────────────────────────────────────
-- 8. SEED: Kategori default sistem (user_id = 0)
--    user_id = 0 = template sistem, bukan user nyata.
--    FK sementara dinonaktifkan agar user_id=0 bisa masuk.
--    Setiap user baru akan mendapat SALINAN dari baris ini (FASE 2).
-- ─────────────────────────────────────────────
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO kategori
    (user_id, nama_kategori, tipe, ikon, kode_warna, is_default)
VALUES
    -- PEMASUKAN
    (0, 'Gaji',           'pemasukan',    'wallet',          '#10B981', 1),
    (0, 'Freelance',      'pemasukan',    'briefcase',       '#3B82F6', 1),
    (0, 'Investasi',      'pemasukan',    'trending-up',     '#8B5CF6', 1),
    (0, 'Bonus',          'pemasukan',    'gift',            '#F59E0B', 1),
    (0, 'Lainnya',        'pemasukan',    'plus-circle',     '#6B7280', 1),
    -- PENGELUARAN
    (0, 'Makanan',        'pengeluaran',  'utensils',        '#EF4444', 1),
    (0, 'Transportasi',   'pengeluaran',  'car',             '#F97316', 1),
    (0, 'Belanja',        'pengeluaran',  'shopping-bag',    '#EC4899', 1),
    (0, 'Tagihan',        'pengeluaran',  'zap',             '#EAB308', 1),
    (0, 'Kesehatan',      'pengeluaran',  'heart-pulse',     '#14B8A6', 1),
    (0, 'Hiburan',        'pengeluaran',  'gamepad-2',       '#A855F7', 1),
    (0, 'Pendidikan',     'pengeluaran',  'graduation-cap',  '#0EA5E9', 1),
    (0, 'Tabungan',       'pengeluaran',  'piggy-bank',      '#22C55E', 1),
    (0, 'Lainnya',        'pengeluaran',  'more-horizontal', '#6B7280', 1);

SET FOREIGN_KEY_CHECKS = 1;

-- ─────────────────────────────────────────────
-- VERIFIKASI AKHIR
-- ─────────────────────────────────────────────
SELECT 'Setup FASE 1 selesai!' AS status;
SELECT table_name, table_rows
FROM information_schema.tables
WHERE table_schema = 'db_keuangan_pribadi'
ORDER BY table_name;

