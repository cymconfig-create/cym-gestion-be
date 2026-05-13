-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-08-2025 a las 15:33:41
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cymgestionsgsst`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `actions`
--

DROP TABLE IF EXISTS `actions`;
CREATE TABLE IF NOT EXISTS `actions` (
  `action_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `action_profiles`
--

DROP TABLE IF EXISTS `action_profiles`;
CREATE TABLE IF NOT EXISTS `action_profiles` (
  `action_profile_id` int NOT NULL AUTO_INCREMENT,
  `profile_id` int NOT NULL,
  `action_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`action_profile_id`),
  KEY `fk_action_profiles` (`profile_id`),
  KEY `fk_actions_profile` (`action_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `attachments`
--

DROP TABLE IF EXISTS `attachments`;
CREATE TABLE IF NOT EXISTS `attachments` (
  `attachment_id` int NOT NULL AUTO_INCREMENT,
  `document_id` int NOT NULL,
  `company_id` int DEFAULT NULL,
  `employee_id` int DEFAULT NULL,
  `route_file` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attachment_id`),
  KEY `fk_documents_companies` (`company_id`),
  KEY `fk_documents` (`document_id`),
  KEY `fk_documents_employees` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Guarda las rutas de los archivos adjuntos';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `nit` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(144) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `selector_person_type` int DEFAULT NULL,
  `selector_tax_regime` int DEFAULT NULL,
  `main_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complement_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_address` int DEFAULT NULL,
  `city_address` int DEFAULT NULL,
  `phone_number` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone_number` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_sgsst` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `web_site` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_ciiu` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity_employees` int DEFAULT '0',
  `selector_risk_level` int DEFAULT NULL,
  `selector_arl` int DEFAULT NULL,
  `legal_representative_id` int DEFAULT NULL,
  `system_manager_id` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_eliminated` tinyint(1) NOT NULL DEFAULT '0',
  `created_by` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`),
  KEY `fk_legal_representative` (`legal_representative_id`),
  KEY `fk_system_manager` (`system_manager_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversations`
--

DROP TABLE IF EXISTS `conversations`;
CREATE TABLE IF NOT EXISTS `conversations` (
  `conversation_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` bigint UNSIGNED DEFAULT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('message','ticket') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `status` enum('open','closed','pending') COLLATE utf8mb4_unicode_ci DEFAULT 'open',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`conversation_id`),
  KEY `company_id` (`company_id`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conversation_user`
--

DROP TABLE IF EXISTS `conversation_user`;
CREATE TABLE IF NOT EXISTS `conversation_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `last_read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `conversation_id` (`conversation_id`,`user_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documents`
--

DROP TABLE IF EXISTS `documents`;
CREATE TABLE IF NOT EXISTS `documents` (
  `document_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `percentage` float DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`document_id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `documents`
--

INSERT INTO `documents` (`document_id`, `code`, `name`, `percentage`, `status`, `created_at`, `updated_at`) VALUES
(1, 'LOGO', 'Logo de la Empresa', NULL, 1, '2025-02-24 02:30:32', '2025-04-19 04:12:45'),
(2, 'CAMARA_COMERCIO', 'Cámara de Comercio', NULL, 1, '2025-02-24 02:30:32', '2025-04-19 04:13:30'),
(3, 'ACTA_CONSTITUCION', 'Acta de Constitución', NULL, 1, '2025-02-24 02:30:32', '2025-04-19 04:17:23'),
(4, 'RUT', 'Registro DIAN (RUT)', NULL, 1, '2025-02-24 02:30:32', '2025-04-19 04:15:01'),
(5, 'REPRESENTANTE_LEGAL', 'Representante Legal', NULL, 1, '2025-02-24 02:30:32', '2025-04-19 04:17:19'),
(6, 'DOC_IDENTIDAD', 'Documento de Identidad', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:08:48'),
(7, 'HOJA_VIDA', 'Hoja de Vida', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:08:53'),
(8, 'CONTRATO_LABORAL', 'Contrato Laboral', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:08:55'),
(9, 'CERT_EPS', 'Certificado EPS', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:08:59'),
(10, 'CERT_PENSIONES', 'Certificado Pensiones', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:02'),
(11, 'CERT_CESANTIAS', 'Certificado Cesantías', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:05'),
(12, 'CERT_CAJA_COMP_FAM', 'Certificado Caja Compensación Familiar', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:08'),
(13, 'CERT_ARL', 'Certificado ARL', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:11'),
(14, 'CERT_MEDICO_INGRESO', 'Certificado Médico de Ingreso', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:13'),
(15, 'RECOMEN_OCUPACIONAL', 'Recomendaciones Ocupacionales', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:17'),
(16, 'CERT_MEDICO_EGRESO', 'Certificado Médico de Egreso', NULL, 1, '2025-04-21 05:07:19', '2025-04-21 05:09:19'),
(17, 'PHOTO_PROFILE', 'Imagen del perfil', NULL, 1, '2025-05-03 06:56:26', '0000-00-00 00:00:00'),
(18, 'SLIDES', 'Slides', NULL, 1, '2025-02-24 02:30:32', '2025-04-19 04:12:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `employee_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `selector_identification` int NOT NULL,
  `identification_number` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `place_of_issue` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `residence_address` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `complement_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `place_of_birth` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone_number` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selector_academic_level` int DEFAULT NULL,
  `selector_arl` int DEFAULT NULL,
  `selector_eps` int DEFAULT NULL,
  `selector_pension_fund` int DEFAULT NULL,
  `selector_severance_fund` int DEFAULT NULL,
  `selector_type_of_contract` int DEFAULT NULL,
  `job_position` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_date` datetime DEFAULT NULL,
  `selector_blood_type` int DEFAULT NULL,
  `allergies` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `selector_civil_status` int DEFAULT NULL,
  `selector_identification_contact` int DEFAULT NULL,
  `identification_number_contact` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_name_contact` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone_number_contact` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cell_phone_number_contact` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_contact` varchar(48) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `company_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `created_by` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_by` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`employee_id`),
  UNIQUE KEY `identification_number` (`identification_number`),
  KEY `fk_employee_company` (`company_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menus`
--

DROP TABLE IF EXISTS `menus`;
CREATE TABLE IF NOT EXISTS `menus` (
  `menu_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `menus`
--

INSERT INTO `menus` (`menu_id`, `code`, `name`, `route`, `position`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CONTROL_PANEL', 'Panel de Control', 'controlPanel', 10, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00'),
(2, 'COMPANY', 'Empresas', 'companies', 20, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00'),
(3, 'EMPLOYEE', 'Empleados', 'employees', 30, 1, '2025-02-08 17:00:51', '2025-02-08 18:22:58'),
(4, 'SGSST', 'Sistema de Gestión SST', 'employees', 40, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00'),
(5, 'IN_COMUNICATION', 'Comunicación Interna', 'internalCommunication', 50, 1, '2025-02-08 17:00:51', '2025-02-08 18:22:36'),
(6, 'USER_PROFILE', 'Perfiles de Usuarios', 'userProfiles', 60, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00'),
(7, 'SUPPORT_HELP', 'Soporte y Ayuda', 'supportAndHelp', 70, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00'),
(8, 'ACTIVITY_MONTH', 'Actividades del Mes', 'activitiesMonth', 80, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00'),
(9, 'LOG_OUT', 'Cerrar Sesión', 'logOut', 90, 1, '2025-02-08 17:00:51', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu_profiles`
--

DROP TABLE IF EXISTS `menu_profiles`;
CREATE TABLE IF NOT EXISTS `menu_profiles` (
  `menu_profile_id` int NOT NULL AUTO_INCREMENT,
  `profile_id` int NOT NULL,
  `menu_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_profile_id`),
  KEY `fk_menu_profiles` (`profile_id`),
  KEY `fk_menus_profile` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `menu_profiles`
--

INSERT INTO `menu_profiles` (`menu_profile_id`, `profile_id`, `menu_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-02-16 05:33:32', '0000-00-00 00:00:00'),
(2, 1, 2, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00'),
(3, 1, 3, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00'),
(4, 1, 4, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00'),
(5, 1, 5, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00'),
(6, 1, 6, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00'),
(7, 1, 7, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00'),
(8, 1, 8, 1, '2025-02-16 05:35:02', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `conversation_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `user_id` (`user_id`),
  KEY `attachment_id` (`attachment_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `profile_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `profiles`
--

INSERT INTO `profiles` (`profile_id`, `code`, `name`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'super', 'Super Admin', 'Administrador absoluto del sistema. ', 0, '2025-02-07 04:12:29', '2025-02-16 21:35:32'),
(2, 'admin', 'Administrador', 'Encargado de  la administración general del Sistema de Gestión', 1, '2025-02-07 04:12:29', '2025-02-16 21:35:37'),
(3, 'sgsst', 'Responsable del SGSST', 'Responsable del SGSST', 1, '2025-02-07 04:12:29', '2025-02-16 21:36:07'),
(4, 'geren', 'Gerencia', 'Solo puede ver y no manipular', 1, '2025-02-07 04:12:29', '2025-02-16 21:36:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `selectors`
--

DROP TABLE IF EXISTS `selectors`;
CREATE TABLE IF NOT EXISTS `selectors` (
  `selector_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dad_selector` int DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`selector_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `selectors`
--

INSERT INTO `selectors` (`selector_id`, `code`, `name`, `description`, `dad_selector`, `status`, `created_at`, `updated_at`) VALUES
(1, 'type', 'Type of identification', NULL, NULL, 1, '2025-02-27 12:19:08', '2025-03-01 12:13:15'),
(2, 'CC', 'Citizenship ID card', NULL, 1, 1, '2025-02-27 12:19:08', '2025-03-01 12:13:24'),
(3, 'CE', 'Foreigner ID card', NULL, 1, 1, '2025-02-27 12:19:08', '2025-03-01 12:13:33'),
(4, NULL, 'Person Type', NULL, NULL, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(5, NULL, 'Natural', NULL, 4, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(6, NULL, 'Legal', NULL, 4, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(7, NULL, 'Tax Regime', NULL, NULL, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(8, NULL, 'Common', NULL, 7, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(9, NULL, 'Simplified', NULL, 7, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(10, NULL, 'Special', NULL, 7, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(11, NULL, 'Academic background', NULL, NULL, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(12, NULL, 'Bachelor', NULL, 11, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(13, NULL, 'Technician', NULL, 11, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(14, NULL, 'Technologist', NULL, 11, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(15, NULL, 'Professional', NULL, 11, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(16, NULL, 'Specialization', NULL, 11, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(17, NULL, 'Master Degree', NULL, 11, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(18, NULL, 'Type Of Employment Contrac', NULL, NULL, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(19, NULL, 'Indefinite-term Contract', NULL, 18, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(20, NULL, 'Fixed-term Contract', NULL, 18, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(21, NULL, 'Service provision', NULL, 18, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(22, NULL, 'Other', NULL, 18, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(23, NULL, 'Marital Status', NULL, NULL, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(24, NULL, 'Single', NULL, 23, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(25, NULL, 'Married', NULL, 23, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(26, NULL, 'Common-law union', NULL, 23, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(27, NULL, 'Separated', NULL, 23, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(28, NULL, 'Widowed', NULL, 23, 1, '2025-03-01 12:11:01', '0000-00-00 00:00:00'),
(29, NULL, 'Passport', NULL, 1, 1, '2025-03-01 12:14:47', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sub_menus`
--

DROP TABLE IF EXISTS `sub_menus`;
CREATE TABLE IF NOT EXISTS `sub_menus` (
  `sub_menu_id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `route` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `menu_id` int NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sub_menu_id`),
  KEY `fk_sub_menus` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sub_menus`
--

INSERT INTO `sub_menus` (`sub_menu_id`, `code`, `name`, `route`, `position`, `menu_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'COMPANY_LIST', 'Listado de Empresas', 'companyList', 10, 2, 1, '2025-02-08 17:06:43', '0000-00-00 00:00:00'),
(2, 'COMPANY_MANAGEMENT', 'Crear Empresa', 'companyManagement', 20, 2, 1, '2025-02-08 17:06:43', '0000-00-00 00:00:00'),
(3, 'GENERATE_REPORTS', 'Generar Informes', 'generateReport', 30, 2, 1, '2025-02-08 17:16:19', '0000-00-00 00:00:00'),
(4, 'EMPLOYEE_LIST', 'Listado de Empleados', 'employeesList', 10, 3, 1, '2025-02-08 18:06:56', '0000-00-00 00:00:00'),
(5, 'CREATE_EMPLOYEE', 'Crear Empleado', 'createEmployee', 20, 3, 1, '2025-02-08 18:06:56', '0000-00-00 00:00:00'),
(6, 'MESSAGING', 'Gestión de Mensajes', 'messageManagement', 10, 5, 1, '2025-02-08 18:28:10', '2025-02-20 01:38:49'),
(7, 'USER_MANEGEMENT', 'Gestión de Usuarios', 'userManagement', 10, 6, 1, '2025-02-08 18:35:24', '0000-00-00 00:00:00'),
(8, 'TICKET_MANAGEMENT', 'Gestión de Tickets', 'ticketManagement', 10, 7, 1, '2025-02-08 19:00:00', '0000-00-00 00:00:00'),
(9, 'HELP_TUTORIAL', 'Tutoriales de Ayuda', 'helpTutorials', 20, 7, 1, '2025-02-08 19:00:00', '0000-00-00 00:00:00'),
(10, 'DOCUMENTATION', 'Documentación', 'documentation', 30, 7, 1, '2025-02-08 19:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_id` int NOT NULL,
  `created_by` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updated_by` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `unique_name` (`name`),
  KEY `fk_user_profile` (`profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`user_id`, `name`, `password`, `profile_id`, `created_by`, `updated_by`, `status`, `created_at`, `updated_at`) VALUES
(1, 'elazo', '$2y$12$TEwWc3XjV9fAUrdk/MHlF.5eP89qeo.1bWA9LVVKh02JCdbVg9ysO', 1, 'elazo', 'elazo', 1, '2025-02-10 08:38:53', '2025-03-10 07:17:48'),
(2, 'willinton', '$2y$12$DthNdXP5SV4VFsUDdPEkw.SR1O2P5ej0zoDAULmcrYu8hhaZ2bvBq', 1, '', 'elazo', 1, '2025-03-09 00:20:34', '2025-07-02 00:10:01');

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `action_profiles`
--
ALTER TABLE `action_profiles`
  ADD CONSTRAINT `fk_actions_profile` FOREIGN KEY (`action_id`) REFERENCES `actions` (`action_id`),
  ADD CONSTRAINT `fk_actions_profiles` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`);

--
-- Filtros para la tabla `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `fk_documents` FOREIGN KEY (`document_id`) REFERENCES `documents` (`document_id`),
  ADD CONSTRAINT `fk_documents_companies` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
  ADD CONSTRAINT `fk_documents_employees` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`employee_id`);

--
-- Filtros para la tabla `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `fk_legal_representative` FOREIGN KEY (`legal_representative_id`) REFERENCES `employees` (`employee_id`),
  ADD CONSTRAINT `fk_system_manager` FOREIGN KEY (`system_manager_id`) REFERENCES `employees` (`employee_id`);

--
-- Filtros para la tabla `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `fk_employee_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`);

--
-- Filtros para la tabla `menu_profiles`
--
ALTER TABLE `menu_profiles`
  ADD CONSTRAINT `fk_menu_profiles` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`),
  ADD CONSTRAINT `fk_menus_profile` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`);

--
-- Filtros para la tabla `sub_menus`
--
ALTER TABLE `sub_menus`
  ADD CONSTRAINT `fk_sub_menus` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`menu_id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`profile_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
