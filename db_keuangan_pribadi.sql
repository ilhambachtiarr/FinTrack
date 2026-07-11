/*
 Navicat Premium Data Transfer

 Source Server         : db_keuangan_pribadi
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : db_keuangan_pribadi

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 07/07/2026 09:13:59
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for akun
-- ----------------------------
DROP TABLE IF EXISTS `akun`;
CREATE TABLE `akun`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `nama_akun` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_akun` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bank',
  `saldo` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `kode_warna` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#3B82F6',
  `ikon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `urutan` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_akun_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_akun_user_active`(`user_id` ASC, `is_active` ASC) USING BTREE,
  CONSTRAINT `fk_akun_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of akun
-- ----------------------------
INSERT INTO `akun` VALUES (4, 1, 'BCA', 'bank', 811953.00, '#3b82f6', 'building-columns', 1, 0, '2026-07-03 14:52:20', '2026-07-03 22:54:20');
INSERT INTO `akun` VALUES (5, 1, 'BTN', 'bank', 4815.00, '#06b6d4', 'building-columns', 1, 0, '2026-07-03 14:53:27', '2026-07-04 09:54:27');
INSERT INTO `akun` VALUES (6, 1, 'BIBIT', 'investasi', 281480.00, '#10b981', 'chart-line', 1, 0, '2026-07-03 14:54:48', '2026-07-03 14:54:48');
INSERT INTO `akun` VALUES (7, 1, 'Emas HartaDinata', 'investasi', 2805000.00, '#fcd303', 'chart-line', 1, 0, '2026-07-03 15:02:10', '2026-07-03 15:02:10');
INSERT INTO `akun` VALUES (8, 1, 'Cash', 'tunai', 238000.00, '#ef4444', 'money-bill', 1, 0, '2026-07-03 15:11:12', '2026-07-04 15:21:30');

-- ----------------------------
-- Table structure for ci_sessions
-- ----------------------------
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions`  (
  `id` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_ci_sessions_timestamp`(`timestamp` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of ci_sessions
-- ----------------------------
INSERT INTO `ci_sessions` VALUES ('4knu8e9mjguu0jn2t710vr5bh7v11ejd', '127.0.0.1', 1783094231, 0x5F5F63695F6C6173745F726567656E65726174657C693A313738333039333938393B757365725F69647C693A313B757365725F646174617C613A333A7B733A323A226964223B693A313B733A343A226E616D65223B733A31343A22496C68616D204261636874696172223B733A353A22656D61696C223B733A32363A22696C68616D626163687469617235373840676D61696C2E636F6D223B7D);
INSERT INTO `ci_sessions` VALUES ('4qv868i9n677pqm7n8gmaa3hvb0dsqct', '127.0.0.1', 1783067570, 0x5F5F63695F6C6173745F726567656E65726174657C693A313738333036373537303B757365725F69647C693A313B757365725F646174617C613A333A7B733A323A226964223B693A313B733A343A226E616D65223B733A31343A22496C68616D204261636874696172223B733A353A22656D61696C223B733A32363A22696C68616D626163687469617235373840676D61696C2E636F6D223B7D);
INSERT INTO `ci_sessions` VALUES ('ilhcnfrcg1qu0ls16f1t5kjqm915nm3e', '127.0.0.1', 1783136394, 0x5F5F63695F6C6173745F726567656E65726174657C693A313738333133363332363B757365725F69647C693A313B757365725F646174617C613A333A7B733A323A226964223B693A313B733A343A226E616D65223B733A31343A22496C68616D204261636874696172223B733A353A22656D61696C223B733A32363A22696C68616D626163687469617235373840676D61696C2E636F6D223B7D);
INSERT INTO `ci_sessions` VALUES ('sdb8sd324liubm6crfoi6da9fq51e5qs', '127.0.0.1', 1783153512, 0x5F5F63695F6C6173745F726567656E65726174657C693A313738333135333530353B757365725F69647C693A313B757365725F646174617C613A333A7B733A323A226964223B693A313B733A343A226E616D65223B733A31343A22496C68616D204261636874696172223B733A353A22656D61696C223B733A32363A22696C68616D626163687469617235373840676D61696C2E636F6D223B7D);
INSERT INTO `ci_sessions` VALUES ('t7fulrj9uffjp4hft3seuk0a3ts8r6af', '127.0.0.1', 1783133157, 0x5F5F63695F6C6173745F726567656E65726174657C693A313738333133333135373B);

-- ----------------------------
-- Table structure for kategori
-- ----------------------------
DROP TABLE IF EXISTS `kategori`;
CREATE TABLE `kategori`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('pemasukan','pengeluaran') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ikon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kode_warna` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#6B7280',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_kategori_user_tipe`(`user_id` ASC, `tipe` ASC) USING BTREE,
  CONSTRAINT `fk_kategori_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of kategori
-- ----------------------------
INSERT INTO `kategori` VALUES (1, 0, 'Gaji', 'pemasukan', 'wallet', '#10B981', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (2, 0, 'Freelance', 'pemasukan', 'briefcase', '#3B82F6', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (3, 0, 'Investasi', 'pemasukan', 'trending-up', '#8B5CF6', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (4, 0, 'Bonus', 'pemasukan', 'gift', '#F59E0B', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (5, 0, 'Lainnya', 'pemasukan', 'plus-circle', '#6B7280', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (6, 0, 'Makanan', 'pengeluaran', 'utensils', '#EF4444', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (7, 0, 'Transportasi', 'pengeluaran', 'car', '#F97316', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (8, 0, 'Belanja', 'pengeluaran', 'shopping-bag', '#EC4899', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (9, 0, 'Tagihan', 'pengeluaran', 'zap', '#EAB308', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (10, 0, 'Kesehatan', 'pengeluaran', 'heart-pulse', '#14B8A6', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (11, 0, 'Hiburan', 'pengeluaran', 'gamepad-2', '#A855F7', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (12, 0, 'Pendidikan', 'pengeluaran', 'graduation-cap', '#0EA5E9', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (13, 0, 'Tabungan', 'pengeluaran', 'piggy-bank', '#22C55E', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (14, 0, 'Lainnya', 'pengeluaran', 'more-horizontal', '#6B7280', 1, '2026-07-02 17:43:18');
INSERT INTO `kategori` VALUES (15, 1, 'Gaji', 'pemasukan', 'wallet', '#10B981', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (16, 1, 'Freelance', 'pemasukan', 'briefcase', '#3B82F6', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (17, 1, 'Investasi', 'pemasukan', 'trending-up', '#8B5CF6', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (18, 1, 'Bonus', 'pemasukan', 'gift', '#F59E0B', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (19, 1, 'Lainnya', 'pemasukan', 'plus-circle', '#6B7280', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (20, 1, 'Makanan / Minuman', 'pengeluaran', 'utensils', '#EF4444', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (21, 1, 'Transportasi', 'pengeluaran', 'car', '#F97316', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (22, 1, 'Belanja', 'pengeluaran', 'shopping-bag', '#EC4899', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (23, 1, 'Tagihan', 'pengeluaran', 'zap', '#EAB308', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (24, 1, 'Kesehatan', 'pengeluaran', 'heart-pulse', '#14B8A6', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (25, 1, 'Hiburan', 'pengeluaran', 'gamepad-2', '#A855F7', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (26, 1, 'Pendidikan', 'pengeluaran', 'graduation-cap', '#0EA5E9', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (27, 1, 'Tabungan', 'pengeluaran', 'piggy-bank', '#22C55E', 1, '2026-07-02 17:54:51');
INSERT INTO `kategori` VALUES (28, 1, 'Lainnya', 'pengeluaran', 'more-horizontal', '#6B7280', 1, '2026-07-02 17:54:51');

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `token_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` datetime NOT NULL,
  `used_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_password_resets_user`(`user_id` ASC) USING BTREE,
  CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of password_resets
-- ----------------------------
INSERT INTO `password_resets` VALUES (1, 1, '2b714fa818b4086a18f6a9ad5038cfe814176592ceaa86d4348b0f180fb2b6da', '2026-07-03 15:06:43', '2026-07-03 14:07:46', '2026-07-03 14:06:43');

-- ----------------------------
-- Table structure for transaksi
-- ----------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `akun_id` int UNSIGNED NOT NULL,
  `kategori_id` int UNSIGNED NULL DEFAULT NULL,
  `tipe` enum('pemasukan','pengeluaran','transfer') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sumber` enum('manual','struk') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `jumlah` decimal(15, 2) NOT NULL,
  `tanggal_transaksi` date NOT NULL,
  `catatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `nama_merchant` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `ocr_raw_text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `lampiran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `target_akun_id` int UNSIGNED NULL DEFAULT NULL,
  `deleted_at` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_transaksi_kategori`(`kategori_id` ASC) USING BTREE,
  INDEX `fk_transaksi_target_akun`(`target_akun_id` ASC) USING BTREE,
  INDEX `idx_transaksi_user_tanggal`(`user_id` ASC, `tanggal_transaksi` ASC) USING BTREE,
  INDEX `idx_transaksi_akun`(`akun_id` ASC) USING BTREE,
  INDEX `idx_transaksi_tipe`(`tipe` ASC) USING BTREE,
  INDEX `idx_transaksi_deleted`(`deleted_at` ASC) USING BTREE,
  INDEX `idx_transaksi_user_kategori`(`user_id` ASC, `kategori_id` ASC) USING BTREE,
  CONSTRAINT `fk_transaksi_akun` FOREIGN KEY (`akun_id`) REFERENCES `akun` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_transaksi_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `fk_transaksi_target_akun` FOREIGN KEY (`target_akun_id`) REFERENCES `akun` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 22 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transaksi
-- ----------------------------
INSERT INTO `transaksi` VALUES (8, 1, 8, 28, 'pengeluaran', 'manual', 9000.00, '2026-07-01', 'Kabel Tis', NULL, NULL, NULL, NULL, NULL, '2026-07-03 15:12:30', '2026-07-03 15:12:30');
INSERT INTO `transaksi` VALUES (9, 1, 8, 20, 'pengeluaran', 'manual', 12000.00, '2026-07-02', 'Makan Siang di kantin kampus', NULL, NULL, NULL, NULL, NULL, '2026-07-03 15:14:26', '2026-07-03 15:14:26');
INSERT INTO `transaksi` VALUES (10, 1, 8, 25, 'pengeluaran', 'manual', 5000.00, '2026-07-02', 'Billiard patungan', NULL, NULL, NULL, NULL, NULL, '2026-07-03 15:15:05', '2026-07-03 15:15:05');
INSERT INTO `transaksi` VALUES (11, 1, 8, 21, 'pengeluaran', 'manual', 2000.00, '2026-07-02', 'Parkir Orbits Billiard', NULL, NULL, NULL, NULL, NULL, '2026-07-03 15:15:34', '2026-07-03 15:15:34');
INSERT INTO `transaksi` VALUES (12, 1, 8, 20, 'pengeluaran', 'manual', 8000.00, '2026-07-03', 'Es Buah', NULL, NULL, NULL, NULL, NULL, '2026-07-03 15:16:11', '2026-07-03 15:16:11');
INSERT INTO `transaksi` VALUES (13, 1, 8, 20, 'pengeluaran', 'manual', 8000.00, '2026-07-03', 'Mendoan + le mineral kecil di kantin kampus', NULL, NULL, NULL, NULL, NULL, '2026-07-03 15:17:38', '2026-07-03 15:17:38');
INSERT INTO `transaksi` VALUES (14, 1, 4, 28, 'pengeluaran', 'manual', 53000.00, '2026-07-03', 'Isi Token Listrik Rumah', NULL, NULL, NULL, NULL, NULL, '2026-07-03 22:54:20', '2026-07-03 22:54:20');
INSERT INTO `transaksi` VALUES (15, 1, 8, 19, 'pemasukan', 'manual', 23000.00, '2026-07-03', 'Uang Dari Bapak, Pengganti Uang Listrik', NULL, NULL, NULL, NULL, NULL, '2026-07-03 22:55:33', '2026-07-03 22:55:33');
INSERT INTO `transaksi` VALUES (16, 1, 8, 19, 'pemasukan', 'manual', 30000.00, '2026-07-04', 'Dikasih Uang Kuliah Dari Ibu', NULL, NULL, NULL, NULL, NULL, '2026-07-04 09:52:36', '2026-07-04 09:52:36');
INSERT INTO `transaksi` VALUES (17, 1, 8, 20, 'pengeluaran', 'manual', 5000.00, '2026-07-04', 'Beli Kopi di Kantin Kampus', NULL, NULL, NULL, NULL, NULL, '2026-07-04 09:53:29', '2026-07-04 09:53:29');
INSERT INTO `transaksi` VALUES (18, 1, 5, 28, 'pengeluaran', 'manual', 31000.00, '2026-07-04', 'Beli Kuota', NULL, NULL, NULL, NULL, NULL, '2026-07-04 09:54:27', '2026-07-04 09:54:27');
INSERT INTO `transaksi` VALUES (19, 1, 8, 20, 'pengeluaran', 'manual', 8000.00, '2026-07-04', 'Makan Siang di kantin kampus', NULL, NULL, NULL, NULL, NULL, '2026-07-04 10:39:18', '2026-07-04 10:39:18');
INSERT INTO `transaksi` VALUES (20, 1, 8, 28, 'pengeluaran', 'manual', 10000.00, '2026-07-04', 'Self Photo Kelas yang Laki-Laki', NULL, NULL, NULL, NULL, NULL, '2026-07-04 15:20:20', '2026-07-04 15:20:20');
INSERT INTO `transaksi` VALUES (21, 1, 8, 20, 'pengeluaran', 'manual', 22000.00, '2026-07-04', 'Makan Sore di Angkringan Mukti Guna Kompetisi Bug Bounty', NULL, NULL, NULL, NULL, NULL, '2026-07-04 15:21:30', '2026-07-04 15:21:30');

-- ----------------------------
-- Table structure for transaksi_item
-- ----------------------------
DROP TABLE IF EXISTS `transaksi_item`;
CREATE TABLE `transaksi_item`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaksi_id` int UNSIGNED NOT NULL,
  `nama_item` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` decimal(10, 2) NOT NULL DEFAULT 1.00,
  `harga_satuan` decimal(15, 2) NOT NULL,
  `subtotal` decimal(15, 2) NOT NULL,
  `urutan` smallint UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_transaksi_item_transaksi`(`transaksi_id` ASC) USING BTREE,
  CONSTRAINT `fk_transaksi_item_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of transaksi_item
-- ----------------------------

-- ----------------------------
-- Table structure for tujuan_keuangan
-- ----------------------------
DROP TABLE IF EXISTS `tujuan_keuangan`;
CREATE TABLE `tujuan_keuangan`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `nama_tujuan` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `target_jumlah` decimal(15, 2) NOT NULL,
  `saldo_saat_ini` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tgl_target` date NULL DEFAULT NULL,
  `ikon` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `kode_warna` varchar(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#10B981',
  `status` enum('aktif','tercapai','dibatalkan') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_goal_user`(`user_id` ASC) USING BTREE,
  INDEX `idx_goal_user_status`(`user_id` ASC, `status` ASC) USING BTREE,
  CONSTRAINT `fk_goal_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tujuan_keuangan
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `foto_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_login_attempts` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `locked_until` datetime NULL DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uq_users_email`(`email` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'Ilham Bachtiar', 'ilhambachtiar578@gmail.com', 'profil_1_1783062506_3774.jpg', '$2y$10$sw7LP2iPu4z42gOXfr2pF.qXPedY/znV2Zt6THg1Ie0qXjrX4JlsC', 0, NULL, '2026-07-02 17:54:51', '2026-07-04 15:19:37');

SET FOREIGN_KEY_CHECKS = 1;
