-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping data for table projet3.contact: ~14 rows (approximately)
/*!40000 ALTER TABLE `contact` DISABLE KEYS */;
INSERT INTO `contact` (`id`, `user_create_id`, `is_company`, `name`, `phone1`, `phone2`, `email`, `created_at`) VALUES
	(1, 2, 1, 'Entreprise #1', '03.89.77.00.01', NULL, 'email@example.com', '2022-01-01 00:00:00'),
	(2, 2, 1, 'Entreprise 2', NULL, '06.89.77.00.02', 'email@entreprise.fr', '2022-01-01 00:00:00'),
	(3, 2, 0, 'Default Name', NULL, NULL, 'emailtrèslong@emailongetchiant.com', '2022-01-01 00:00:00'),
	(4, 2, 1, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(5, 2, 1, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(6, 2, 0, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(7, 2, 1, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(8, 2, 0, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(9, 2, 1, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(10, 2, 1, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(11, 2, 0, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(12, 2, 0, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(13, 2, 0, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00'),
	(14, 2, 0, 'Default Name', NULL, NULL, NULL, '2022-01-01 00:00:00');
/*!40000 ALTER TABLE `contact` ENABLE KEYS */;

-- Dumping data for table projet3.contact_contact_type: ~13 rows (approximately)
/*!40000 ALTER TABLE `contact_contact_type` DISABLE KEYS */;
INSERT INTO `contact_contact_type` (`contact_id`, `contact_type_id`) VALUES
	(1, 1),
	(2, 1),
	(2, 2),
	(3, 1),
	(3, 2),
	(3, 3),
	(4, 2),
	(5, 3),
	(6, 4),
	(8, 1),
	(8, 2),
	(8, 3),
	(8, 4);
/*!40000 ALTER TABLE `contact_contact_type` ENABLE KEYS */;

-- Dumping data for table projet3.contact_extrafields: ~0 rows (approximately)
/*!40000 ALTER TABLE `contact_extrafields` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_extrafields` ENABLE KEYS */;

-- Dumping data for table projet3.contact_extrafield_value: ~0 rows (approximately)
/*!40000 ALTER TABLE `contact_extrafield_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_extrafield_value` ENABLE KEYS */;

-- Dumping data for table projet3.contact_type: ~4 rows (approximately)
/*!40000 ALTER TABLE `contact_type` DISABLE KEYS */;
INSERT INTO `contact_type` (`id`, `label`, `color`) VALUES
	(1, 'Client', '#5DFFE4'),
	(2, 'Fournisseur', '#5DBEFF'),
	(3, 'Prestataire', '#D55DFF'),
	(4, 'Collaborateur', '#FFB95D');
/*!40000 ALTER TABLE `contact_type` ENABLE KEYS */;

-- Dumping data for table projet3.doctrine_migration_versions: ~0 rows (approximately)
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
	('DoctrineMigrations\\Version20220215192855', '2022-02-15 19:29:03', 708);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;

-- Dumping data for table projet3.event: ~12 rows (approximately)
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` (`id`, `event_type_id`, `planning_id`, `date_start`, `date_end`, `label`, `description`, `color`, `is_important`) VALUES
	(1, 1, 1, '2022-02-16 19:30:00', '2022-02-16 19:40:00', 'Réunion avec machin', NULL, NULL, 0),
	(2, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(3, 3, 1, '2022-02-26 19:35:36', '2022-02-26 19:35:43', 'La fête au village', NULL, NULL, 0),
	(4, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(5, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(6, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(7, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(8, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(9, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(10, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(11, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0),
	(12, 2, 1, '2022-02-17 19:35:08', '2022-02-17 19:35:16', 'Tache #2', NULL, NULL, 0);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;

-- Dumping data for table projet3.event_contact: ~0 rows (approximately)
/*!40000 ALTER TABLE `event_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_contact` ENABLE KEYS */;

-- Dumping data for table projet3.event_type: ~3 rows (approximately)
/*!40000 ALTER TABLE `event_type` DISABLE KEYS */;
INSERT INTO `event_type` (`id`, `label`, `color`) VALUES
	(1, ' Réunion', '#0000FF'),
	(2, 'Tache', '#00FF00'),
	(3, 'Fête', '#FF0000');
/*!40000 ALTER TABLE `event_type` ENABLE KEYS */;

-- Dumping data for table projet3.planning: ~1 rows (approximately)
/*!40000 ALTER TABLE `planning` DISABLE KEYS */;
INSERT INTO `planning` (`id`, `planning_owner_id`, `label`, `color`) VALUES
	(1, 2, 'Mon planning', NULL),
	(2, 3, 'No Title', NULL);
/*!40000 ALTER TABLE `planning` ENABLE KEYS */;

-- Dumping data for table projet3.team: ~0 rows (approximately)
/*!40000 ALTER TABLE `team` DISABLE KEYS */;
INSERT INTO `team` (`id`, `color`, `label`, `description`, `is_private`, `user_id`) VALUES
	(1, '#5DFFE4', 'Team dev', NULL, 0, 2),
	(2, '#FFB95D', 'Team compta', NULL, 0, 2);
/*!40000 ALTER TABLE `team` ENABLE KEYS */;

-- Dumping data for table projet3.user: ~4 rows (approximately)
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `last_name`, `first_name`, `email`, `roles`, `password`, `phone`, `picture`, `created_at`) VALUES
	(2, 'Roess', 'Thomas', 'thomas_roess@hotmail.fr', '["ROLE_ADMIN"]', '$2y$13$QAig1RaN/SAYul/snPsmpu1VM742Y2oXWWeL.Vbm26hhr.O0zE8Te', '0', 'thomas_roess@hotmail.fr/3.0.png', '2022-02-15 19:49:57'),
	(3, 'Roess', 'Thomas2', 'thomas_roess@hotmail.fr2', '["ROLE_ADMIN"]', '$2y$13$j8JGQwsMXzuz/LTtPpZ6heY7WPAfZk8wkTuQp/AqWjgSb6pFbds76', '0', NULL, '2022-02-16 20:46:09'),
	(4, 'Roess', 'Thomas3', 'thomas_roess@hotmail.fr3', '["ROLE_ADMIN"]', '$2y$13$j8JGQwsMXzuz/LTtPpZ6heY7WPAfZk8wkTuQp/AqWjgSb6pFbds76', '0', NULL, '2022-02-16 20:46:09'),
	(5, 'Roess', 'Thomas4', 'thomas_roess@hotmail.fr4', '["ROLE_ADMIN"]', '$2y$13$j8JGQwsMXzuz/LTtPpZ6heY7WPAfZk8wkTuQp/AqWjgSb6pFbds76', '0', NULL, '2022-02-16 20:46:09');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;

-- Dumping data for table projet3.user_event: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_event` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_event` ENABLE KEYS */;

-- Dumping data for table projet3.user_planning: ~2 rows (approximately)
/*!40000 ALTER TABLE `user_planning` DISABLE KEYS */;
INSERT INTO `user_planning` (`user_id`, `planning_id`) VALUES
	(2, 2),
	(3, 1);
/*!40000 ALTER TABLE `user_planning` ENABLE KEYS */;

-- Dumping data for table projet3.user_team: ~0 rows (approximately)
/*!40000 ALTER TABLE `user_team` DISABLE KEYS */;
INSERT INTO `user_team` (`user_id`, `team_id`) VALUES
	(3, 2),
	(4, 1),
	(4, 2),
	(5, 1),
	(5, 2);
/*!40000 ALTER TABLE `user_team` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
