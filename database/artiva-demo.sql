/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `artworks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `artworks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `artist_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `preview_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `file_format` varchar(20) NOT NULL DEFAULT 'PNG',
  `resolution` varchar(40) DEFAULT NULL,
  `file_size_kb` int(10) unsigned DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `downloads_count` int(10) unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `artworks_slug_unique` (`slug`),
  KEY `artworks_category_id_foreign` (`category_id`),
  KEY `artworks_is_active_price_index` (`is_active`,`price`),
  KEY `artworks_is_featured_index` (`is_featured`),
  KEY `artworks_is_active_index` (`is_active`),
  CONSTRAINT `artworks_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `artworks` WRITE;
/*!40000 ALTER TABLE `artworks` DISABLE KEYS */;
INSERT INTO `artworks` VALUES
(1,1,'Kai','kai','Kai','The quiet confidence of a young swordsman caught between two worlds. Delivered as a high-resolution file with a personal-use licence.','previews/kai.svg','artworks/kai.svg',45.00,'SVG','3000 x 4000 px',242,1,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(2,2,'Jp','jp','Jp Comano','A portrait study in warm neutrals, built from layered brushwork and light. Delivered as a high-resolution file with a personal-use licence.','previews/jp.svg','artworks/jp.svg',38.00,'SVG','3000 x 4000 px',242,1,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(3,1,'Captain Valor','captain-valor','R. Mendoza','A decorated captain rendered in bold shapes and navy shadow. Delivered as a high-resolution file with a personal-use licence.','previews/captain-valor.svg','artworks/captain-valor.svg',65.00,'SVG','3000 x 4000 px',242,1,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(4,4,'Archery','archery','Kai Soriano','Focus, breath and release — a concept piece for a forest tournament. Delivered as a high-resolution file with a personal-use licence.','previews/archery.svg','artworks/archery.svg',52.50,'SVG','3000 x 4000 px',242,1,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(5,1,'Dan - The Boss','dan-the-boss','Dan Villareal','The boss of the block, illustrated with hard light and brass accents. Delivered as a high-resolution file with a personal-use licence.','previews/dan-the-boss.svg','artworks/dan-the-boss.svg',72.00,'SVG','3000 x 4000 px',242,1,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(6,5,'Afterglow','afterglow','Mara Santos','Warm bands of colour that linger like the last minutes of sunset. Delivered as a high-resolution file with a personal-use licence.','previews/afterglow.svg','artworks/afterglow.svg',34.00,'SVG','3000 x 4000 px',242,0,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(7,3,'Tidewalker','tidewalker','Aya Nakamura','A traveller who reads the sea like a map of old promises. Delivered as a high-resolution file with a personal-use licence.','previews/tidewalker.svg','artworks/tidewalker.svg',58.00,'SVG','3000 x 4000 px',242,1,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(8,4,'Glass City','glass-city','Liam Cruz','A skyline study in cold blues and fractured reflections. Delivered as a high-resolution file with a personal-use licence.','previews/glass-city.svg','artworks/glass-city.svg',61.00,'SVG','3000 x 4000 px',242,0,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(9,2,'Stillness','stillness','Jp Comano','A minimal portrait in lavender tones about pauses and patience. Delivered as a high-resolution file with a personal-use licence.','previews/stillness.svg','artworks/stillness.svg',41.00,'SVG','3000 x 4000 px',242,0,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(10,6,'Ember Field','ember-field','Mara Santos','Late summer hills lit from behind by a low amber sun. Delivered as a high-resolution file with a personal-use licence.','previews/ember-field.svg','artworks/ember-field.svg',46.00,'SVG','3000 x 4000 px',242,0,1,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(11,6,'Nightfall Ridge','nightfall-ridge','Kai Soriano','A mountain ridge at dusk, painted in indigo and silver. Delivered as a high-resolution file with a personal-use licence.','previews/nightfall-ridge.svg','artworks/nightfall-ridge.svg',49.00,'SVG','3000 x 4000 px',242,0,1,0,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(12,3,'Paper Cranes','paper-cranes','Aya Nakamura','A wish folded a thousand times and set loose over a sleeping town. Delivered as a high-resolution file with a personal-use licence.','previews/paper-cranes.svg','artworks/paper-cranes.svg',55.00,'SVG','3000 x 4000 px',242,0,1,0,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(13,5,'Static Bloom','static-bloom','Liam Cruz','Soft geometry and digital grain arranged into a quiet bloom. Delivered as a high-resolution file with a personal-use licence.','previews/static-bloom.svg','artworks/static-bloom.svg',29.00,'SVG','3000 x 4000 px',242,0,1,0,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(14,4,'Ledger','ledger','Dan Villareal','Prop and environment concepts for a story about borrowed time. Delivered as a high-resolution file with a personal-use licence.','previews/ledger.svg','artworks/ledger.svg',44.00,'SVG','3000 x 4000 px',242,0,1,0,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(15,1,'Sable','sable','R. Mendoza','A rogue in charcoal and silver, caught mid-decision. Delivered as a high-resolution file with a personal-use licence.','previews/sable.svg','artworks/sable.svg',68.00,'SVG','3000 x 4000 px',242,0,1,0,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(16,6,'Low Tide','low-tide','Mara Santos','Wet sand, pale sky and the long breath after a storm. Delivered as a high-resolution file with a personal-use licence.','previews/low-tide.svg','artworks/low-tide.svg',39.00,'SVG','3000 x 4000 px',242,0,1,0,'2026-10-26 23:11:47','2026-10-26 23:11:47');
/*!40000 ALTER TABLE `artworks` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cart_id` bigint(20) unsigned NOT NULL,
  `artwork_id` bigint(20) unsigned NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cart_items_cart_id_artwork_id_unique` (`cart_id`,`artwork_id`),
  KEY `cart_items_artwork_id_foreign` (`artwork_id`),
  CONSTRAINT `cart_items_artwork_id_foreign` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `carts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carts_user_id_unique` (`user_id`),
  CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(1,'Character Art','character-art','Original character designs and hero illustrations.',NULL,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(2,'Portraits','portraits','Faces, expressions and personality studies.',NULL,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(3,'Fantasy','fantasy','Worlds built from myth, magic and imagination.',NULL,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(4,'Concept Art','concept-art','Visual development art for stories and games.',NULL,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(5,'Abstract','abstract','Colour, rhythm and shape without a subject.',NULL,1,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(6,'Landscapes','landscapes','Atmospheres, horizons and quiet places.',NULL,1,'2026-10-26 23:11:47','2026-10-26 23:11:47');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'2024_01_01_000001_create_users_table',1),
(2,'2024_01_01_000002_create_categories_table',1),
(3,'2024_01_01_000003_create_artworks_table',1),
(4,'2024_01_01_000004_create_carts_table',1),
(5,'2024_01_01_000005_create_cart_items_table',1),
(6,'2024_01_01_000006_create_wishlists_table',1),
(7,'2024_01_01_000007_create_orders_table',1),
(8,'2024_01_01_000008_create_order_items_table',1),
(9,'2024_01_01_000009_create_payments_table',1),
(10,'2024_01_01_000010_create_contact_messages_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `artwork_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `artist_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT 1,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_artwork_id_foreign` (`artwork_id`),
  CONSTRAINT `order_items_artwork_id_foreign` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES
(1,1,1,'Kai','Kai',45.00,1,45.00,'2026-06-08 10:15:00','2026-06-08 10:15:00'),
(2,1,3,'Captain Valor','R. Mendoza',65.00,1,65.00,'2026-06-08 10:15:00','2026-06-08 10:15:00'),
(3,2,5,'Dan - The Boss','Dan Villareal',72.00,1,72.00,'2026-07-17 11:15:00','2026-07-17 11:15:00'),
(4,3,2,'Jp','Jp Comano',38.00,1,38.00,'2026-08-03 12:15:00','2026-08-03 12:15:00'),
(5,3,4,'Archery','Kai Soriano',52.50,1,52.50,'2026-08-03 12:15:00','2026-08-03 12:15:00'),
(6,3,6,'Afterglow','Mara Santos',34.00,1,34.00,'2026-08-03 12:15:00','2026-08-03 12:15:00'),
(7,4,7,'Tidewalker','Aya Nakamura',58.00,1,58.00,'2026-09-21 13:15:00','2026-09-21 13:15:00'),
(8,5,8,'Glass City','Liam Cruz',61.00,1,61.00,'2026-10-06 14:15:00','2026-10-06 14:15:00'),
(9,5,9,'Stillness','Jp Comano',41.00,1,41.00,'2026-10-06 14:15:00','2026-10-06 14:15:00'),
(10,6,10,'Ember Field','Mara Santos',46.00,1,46.00,'2026-10-12 15:15:00','2026-10-12 15:15:00');
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `order_number` varchar(30) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(40) NOT NULL DEFAULT 'simulated',
  `billing_name` varchar(255) NOT NULL,
  `billing_email` varchar(255) NOT NULL,
  `billing_phone` varchar(30) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_status_index` (`status`),
  KEY `orders_payment_status_index` (`payment_status`),
  CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES
(1,2,'ART-20260608-NVN4U',110.00,0.00,110.00,'completed','paid','card','Jp Reyes','customer@artiva.test','+1 555 0111','12 Gallery Lane, Suite 3',NULL,'2026-06-08 10:15:00','2026-06-08 10:15:00','2026-06-08 10:15:00'),
(2,3,'ART-20260717-X65N4',72.00,0.00,72.00,'completed','paid','card','Mara Santos','mara@artiva.test','+1 555 0112','12 Gallery Lane, Suite 4',NULL,'2026-07-17 11:15:00','2026-07-17 11:15:00','2026-07-17 11:15:00'),
(3,4,'ART-20260803-ANBET',124.50,0.00,124.50,'completed','paid','card','Liam Cruz','liam@artiva.test','+1 555 0113','12 Gallery Lane, Suite 5',NULL,'2026-08-03 12:15:00','2026-08-03 12:15:00','2026-08-03 12:15:00'),
(4,5,'ART-20260921-CAZJG',58.00,0.00,58.00,'processing','paid','card','Aya Nakamura','aya@artiva.test',NULL,'12 Gallery Lane, Suite 6',NULL,'2026-09-21 13:15:00','2026-09-21 13:15:00','2026-09-21 13:15:00'),
(5,2,'ART-20261006-GMI4H',102.00,0.00,102.00,'processing','paid','card','Jp Reyes','customer@artiva.test','+1 555 0111','12 Gallery Lane, Suite 7',NULL,'2026-10-06 14:15:00','2026-10-06 14:15:00','2026-10-06 14:15:00'),
(6,3,'ART-20261012-BSTIZ',46.00,0.00,46.00,'completed','paid','card','Mara Santos','mara@artiva.test','+1 555 0112','12 Gallery Lane, Suite 8',NULL,'2026-10-12 15:15:00','2026-10-12 15:15:00','2026-10-12 15:15:00');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) unsigned NOT NULL,
  `method` varchar(40) NOT NULL DEFAULT 'simulated',
  `transaction_id` varchar(60) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_status_index` (`status`),
  CONSTRAINT `payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES
(1,1,'card','SIM-INQJJ5I5T3QJ',110.00,'paid','{\"gateway\":\"Artiva Simulated Gateway\",\"message\":\"Seeded demo transaction \\u2014 no real money moved.\",\"card_last_four\":\"4242\"}','2026-06-08 10:15:00','2026-06-08 10:15:00','2026-06-08 10:15:00'),
(2,2,'card','SIM-ORTY6XNP1LLU',72.00,'paid','{\"gateway\":\"Artiva Simulated Gateway\",\"message\":\"Seeded demo transaction \\u2014 no real money moved.\",\"card_last_four\":\"4242\"}','2026-07-17 11:15:00','2026-07-17 11:15:00','2026-07-17 11:15:00'),
(3,3,'card','SIM-QCT7SKSO5NSZ',124.50,'paid','{\"gateway\":\"Artiva Simulated Gateway\",\"message\":\"Seeded demo transaction \\u2014 no real money moved.\",\"card_last_four\":\"4242\"}','2026-08-03 12:15:00','2026-08-03 12:15:00','2026-08-03 12:15:00'),
(4,4,'card','SIM-O4H7IOAG7E2K',58.00,'paid','{\"gateway\":\"Artiva Simulated Gateway\",\"message\":\"Seeded demo transaction \\u2014 no real money moved.\",\"card_last_four\":\"4242\"}','2026-09-21 13:15:00','2026-09-21 13:15:00','2026-09-21 13:15:00'),
(5,5,'card','SIM-HGS4UFPVQNT4',102.00,'paid','{\"gateway\":\"Artiva Simulated Gateway\",\"message\":\"Seeded demo transaction \\u2014 no real money moved.\",\"card_last_four\":\"4242\"}','2026-10-06 14:15:00','2026-10-06 14:15:00','2026-10-06 14:15:00'),
(6,6,'card','SIM-4KUMMASAM6MO',46.00,'paid','{\"gateway\":\"Artiva Simulated Gateway\",\"message\":\"Seeded demo transaction \\u2014 no real money moved.\",\"card_last_four\":\"4242\"}','2026-10-12 15:15:00','2026-10-12 15:15:00','2026-10-12 15:15:00');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'Artiva Admin','admin@artiva.test',NULL,'$2y$12$C3Jl9S3a7LudzY/636wane/u3riNsDYJPTyFg3YUi7JO1hnMgxqgq','admin','+1 555 0100',NULL,NULL,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(2,'Jp Reyes','customer@artiva.test',NULL,'$2y$12$rzN6HK3zWAozNGXizCfBqeTActUvOZTsGOOvbq2REmRhCJ0wjnBD6','customer','+1 555 0111',NULL,NULL,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(3,'Mara Santos','mara@artiva.test',NULL,'$2y$12$xc.Iy4zovUWL395q09r47.EXjS/zQqUEv0Mvbh9JaNPWrERwFbevK','customer','+1 555 0112',NULL,NULL,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(4,'Liam Cruz','liam@artiva.test',NULL,'$2y$12$ThPEKtVNOLnw2VlpY9Aj/OFqs.6rInOwT0V//3D52uhyde99FocGm','customer','+1 555 0113',NULL,NULL,'2026-10-26 23:11:47','2026-10-26 23:11:47'),
(5,'Aya Nakamura','aya@artiva.test',NULL,'$2y$12$O0rc.cW5Iw4W4To92dOG4OfYP2cuT.KOHyJNFIPKfV86/TXPpNCdq','customer',NULL,NULL,NULL,'2026-10-26 23:11:47','2026-10-26 23:11:47');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `wishlists` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `artwork_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_artwork_id_unique` (`user_id`,`artwork_id`),
  KEY `wishlists_artwork_id_foreign` (`artwork_id`),
  CONSTRAINT `wishlists_artwork_id_foreign` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlists_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

