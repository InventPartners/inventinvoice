# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.52-0ubuntu0.14.04.1)
# Database: inventinvoice
# Generation Time: 2016-10-24 14:04:10 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table account
# ------------------------------------------------------------

CREATE TABLE `account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_firstname` varchar(255) NOT NULL,
  `account_lastname` varchar(255) NOT NULL,
  `account_company` varchar(255) NOT NULL,
  `account_address1` varchar(255) NOT NULL,
  `account_address2` varchar(255) NOT NULL,
  `account_address3` varchar(255) NOT NULL,
  `account_address4` varchar(255) NOT NULL,
  `account_address5` varchar(128) NOT NULL,
  `account_postcode` varchar(12) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `account_vatnumber` varchar(45) NOT NULL,
  `account_email` varchar(255) NOT NULL,
  `taxcode_id` int(10) unsigned NOT NULL DEFAULT '1',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `taxcode_id` (`taxcode_id`),
  KEY `country_code` (`country_code`),
  CONSTRAINT `account_ibfk_1` FOREIGN KEY (`taxcode_id`) REFERENCES `taxcode` (`taxcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
INSERT INTO `account` (`id`, `account_firstname`, `account_lastname`, `account_company`, `account_address1`, `account_address2`, `account_address3`, `account_address4`, `account_address5`, `account_postcode`, `country_code`, `account_vatnumber`, `account_email`, `taxcode_id`, `updated`)
VALUES
	(1, '', '', '', '', '', '', '', '', '', 'GB', '', '', 1, NOW());



# Dump of table contact
# ------------------------------------------------------------

CREATE TABLE `contact` (
  `contact_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contact_firstname` varchar(255) NOT NULL,
  `contact_lastname` varchar(255) NOT NULL,
  `contact_company` varchar(255) NOT NULL,
  `contact_address1` varchar(255) NOT NULL,
  `contact_address2` varchar(255) NOT NULL,
  `contact_address3` varchar(255) NOT NULL,
  `contact_address4` varchar(255) NOT NULL,
  `contact_address5` varchar(255) NOT NULL,
  `contact_postcode` varchar(12) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `contact_vatnumber` varchar(45) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_tel` varchar(25) NOT NULL,
  `contact_status` enum('active','deleted') NOT NULL DEFAULT 'active',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`contact_id`),
  KEY `country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table country
# ------------------------------------------------------------

CREATE TABLE `country` (
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `country_name` varchar(90) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;

INSERT INTO `country` (`country_code`, `country_name`)
VALUES
	('AD','ANDORRA'),
	('AE','UNITED ARAB EMIRATES'),
	('AF','AFGHANISTAN'),
	('AG','ANTIGUA AND BARBUDA'),
	('AI','ANGUILLA'),
	('AL','ALBANIA'),
	('AM','ARMENIA'),
	('AN','NETHERLANDS ANTILLES'),
	('AO','ANGOLA'),
	('AQ','ANTARCTICA'),
	('AR','ARGENTINA'),
	('AS','AMERICAN SAMOA'),
	('AT','AUSTRIA'),
	('AU','AUSTRALIA'),
	('AW','ARUBA'),
	('AZ','AZERBAIJAN'),
	('BA','BOSNIA AND HERZEGOVINA'),
	('BB','BARBADOS'),
	('BD','BANGLADESH'),
	('BE','BELGIUM'),
	('BF','BURKINA FASO'),
	('BG','BULGARIA'),
	('BH','BAHRAIN'),
	('BI','BURUNDI'),
	('BJ','BENIN'),
	('BM','BERMUDA'),
	('BN','BRUNEI DARUSSALAM'),
	('BO','BOLIVIA'),
	('BR','BRAZIL'),
	('BS','BAHAMAS'),
	('BT','BHUTAN'),
	('BV','BOUVET ISLAND'),
	('BW','BOTSWANA'),
	('BY','BELARUS'),
	('BZ','BELIZE'),
	('CA','CANADA'),
	('CC','COCOS (KEELING) ISLANDS'),
	('CD','CONGO (DR)'),
	('CF','CENTRAL AFRICAN REPUBLIC'),
	('CG','CONGO'),
	('CH','SWITZERLAND'),
	('CI','COTE D\'IVOIRE'),
	('CK','COOK ISLANDS'),
	('CL','CHILE'),
	('CM','CAMEROON'),
	('CN','CHINA'),
	('CO','COLOMBIA'),
	('CR','COSTA RICA'),
	('CS','SERBIA AND MONTENEGRO'),
	('CU','CUBA'),
	('CV','CAPE VERDE'),
	('CX','CHRISTMAS ISLAND'),
	('CY','CYPRUS'),
	('CZ','CZECH REPUBLIC'),
	('DE','GERMANY'),
	('DJ','DJIBOUTI'),
	('DK','DENMARK'),
	('DM','DOMINICA'),
	('DO','DOMINICAN REPUBLIC'),
	('DZ','ALGERIA'),
	('EC','ECUADOR'),
	('EE','ESTONIA'),
	('EG','EGYPT'),
	('EH','WESTERN SAHARA'),
	('ER','ERITREA'),
	('ES','SPAIN'),
	('ET','ETHIOPIA'),
	('FI','FINLAND'),
	('FJ','FIJI'),
	('FK','FALKLAND ISLANDS'),
	('FM','MICRONESIA'),
	('FO','FAROE ISLANDS'),
	('FR','FRANCE'),
	('GA','GABON'),
	('GB','UNITED KINGDOM'),
	('GD','GRENADA'),
	('GE','GEORGIA'),
	('GF','FRENCH GUIANA'),
	('GH','GHANA'),
	('GI','GIBRALTAR'),
	('GL','GREENLAND'),
	('GM','GAMBIA'),
	('GN','GUINEA'),
	('GP','GUADELOUPE'),
	('GQ','EQUATORIAL GUINEA'),
	('GR','GREECE'),
	('GS','SOUTH GEORGIA'),
	('GT','GUATEMALA'),
	('GU','GUAM'),
	('GW','GUINEA-BISSAU'),
	('GY','GUYANA'),
	('HK','HONG KONG'),
	('HN','HONDURAS'),
	('HR','CROATIA'),
	('HT','HAITI'),
	('HU','HUNGARY'),
	('ID','INDONESIA'),
	('IE','IRELAND'),
	('IL','ISRAEL'),
	('IN','INDIA'),
	('IQ','IRAQ'),
	('IR','IRAN, ISLAMIC REPUBLIC OF'),
	('IS','ICELAND'),
	('IT','ITALY'),
	('JM','JAMAICA'),
	('JO','JORDAN'),
	('JP','JAPAN'),
	('KE','KENYA'),
	('KG','KYRGYZSTAN'),
	('KH','CAMBODIA'),
	('KI','KIRIBATI'),
	('KM','COMOROS'),
	('KN','SAINT KITTS AND NEVIS'),
	('KW','KUWAIT'),
	('KY','CAYMAN ISLANDS'),
	('KZ','KAZAKHSTAN'),
	('LB','LEBANON'),
	('LC','SAINT LUCIA'),
	('LI','LIECHTENSTEIN'),
	('LK','SRI LANKA'),
	('LR','LIBERIA'),
	('LS','LESOTHO'),
	('LT','LITHUANIA'),
	('LU','LUXEMBOURG'),
	('LV','LATVIA'),
	('LY','LIBYAN ARAB JAMAHIRIYA'),
	('MA','MOROCCO'),
	('MC','MONACO'),
	('MD','MOLDOVA, REPUBLIC OF'),
	('MG','MADAGASCAR'),
	('MH','MARSHALL ISLANDS'),
	('MK','MACEDONIA'),
	('ML','MALI'),
	('MM','MYANMAR'),
	('MN','MONGOLIA'),
	('MO','MACAO'),
	('MQ','MARTINIQUE'),
	('MR','MAURITANIA'),
	('MS','MONTSERRAT'),
	('MT','MALTA'),
	('MU','MAURITIUS'),
	('MV','MALDIVES'),
	('MW','MALAWI'),
	('MX','MEXICO'),
	('MY','MALAYSIA'),
	('MZ','MOZAMBIQUE'),
	('NA','NAMIBIA'),
	('NC','NEW CALEDONIA'),
	('NE','NIGER'),
	('NF','NORFOLK ISLAND'),
	('NG','NIGERIA'),
	('NI','NICARAGUA'),
	('NL','NETHERLANDS'),
	('NO','NORWAY'),
	('NP','NEPAL'),
	('NR','NAURU'),
	('NU','NIUE'),
	('NZ','NEW ZEALAND'),
	('OM','OMAN'),
	('PA','PANAMA'),
	('PE','PERU'),
	('PF','FRENCH POLYNESIA'),
	('PG','PAPUA NEW GUINEA'),
	('PH','PHILIPPINES'),
	('PK','PAKISTAN'),
	('PL','POLAND'),
	('PM','SAINT PIERRE AND MIQUELON'),
	('PN','PITCAIRN'),
	('PR','PUERTO RICO'),
	('PS','PALESTINE'),
	('PT','PORTUGAL'),
	('PW','PALAU'),
	('PY','PARAGUAY'),
	('QA','QATAR'),
	('RE','REUNION'),
	('RO','ROMANIA'),
	('RU','RUSSIAN FEDERATION'),
	('RW','RWANDA'),
	('SA','SAUDI ARABIA'),
	('SB','SOLOMON ISLANDS'),
	('SC','SEYCHELLES'),
	('SD','SUDAN'),
	('SE','SWEDEN'),
	('SG','SINGAPORE'),
	('SH','SAINT HELENA'),
	('SI','SLOVENIA'),
	('SJ','SVALBARD AND JAN MAYEN'),
	('SK','SLOVAKIA'),
	('SL','SIERRA LEONE'),
	('SM','SAN MARINO'),
	('SN','SENEGAL'),
	('SO','SOMALIA'),
	('SR','SURINAME'),
	('ST','SAO TOME AND PRINCIPE'),
	('SV','EL SALVADOR'),
	('SY','SYRIAN ARAB REPUBLIC'),
	('SZ','SWAZILAND'),
	('TC','TURKS AND CAICOS ISLANDS'),
	('TD','CHAD'),
	('TG','TOGO'),
	('TH','THAILAND'),
	('TJ','TAJIKISTAN'),
	('TK','TOKELAU'),
	('TL','TIMOR-LESTE'),
	('TM','TURKMENISTAN'),
	('TN','TUNISIA'),
	('TO','TONGA'),
	('TR','TURKEY'),
	('TT','TRINIDAD AND TOBAGO'),
	('TV','TUVALU'),
	('TW','TAIWAN'),
	('TZ','TANZANIA'),
	('UA','UKRAINE'),
	('UG','UGANDA'),
	('US','UNITED STATES'),
	('UY','URUGUAY'),
	('UZ','UZBEKISTAN'),
	('VA','HOLY SEE (VATICAN CITY STATE)'),
	('VC','SAINT VINCENT'),
	('VE','VENEZUELA'),
	('VG','VIRGIN ISLANDS, BRITISH'),
	('VI','VIRGIN ISLANDS, U.S.'),
	('VN','VIET NAM'),
	('VU','VANUATU'),
	('WF','WALLIS AND FUTUNA'),
	('WS','SAMOA'),
	('YE','YEMEN'),
	('YT','MAYOTTE'),
	('ZA','SOUTH AFRICA'),
	('ZM','ZAMBIA'),
	('ZW','ZIMBABWE');

/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table invoice
# ------------------------------------------------------------

CREATE TABLE `invoice` (
  `invoice_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_from_company` varchar(255) NOT NULL,
  `invoice_from_address1` varchar(255) NOT NULL,
  `invoice_from_address2` varchar(255) NOT NULL,
  `invoice_from_address3` varchar(255) NOT NULL,
  `invoice_from_address4` varchar(255) NOT NULL,
  `invoice_from_address5` varchar(255) NOT NULL,
  `invoice_from_postcode` varchar(12) NOT NULL,
  `invoice_from_country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `invoice_from_vatnumber` varchar(45) NOT NULL,
  `contact_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_to_company` varchar(255) NOT NULL,
  `invoice_to_address1` varchar(255) NOT NULL,
  `invoice_to_address2` varchar(255) NOT NULL,
  `invoice_to_address3` varchar(255) NOT NULL,
  `invoice_to_address4` varchar(255) NOT NULL,
  `invoice_to_address5` varchar(255) NOT NULL,
  `invoice_to_postcode` varchar(12) NOT NULL,
  `invoice_to_country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `invoice_to_vatnumber` varchar(45) NOT NULL,
  `invoice_date` date NOT NULL,
  `taxcode_id` int(10) unsigned NOT NULL DEFAULT '1',
  `invoice_total` double(11,4) NOT NULL,
  `invoice_tax` double(11,4) NOT NULL,
  `invoice_status` enum('pending','outstanding','paid','void') NOT NULL DEFAULT 'pending',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`invoice_id`),
  KEY `contact` (`contact_id`),
  KEY `taxcode_id` (`taxcode_id`),
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`taxcode_id`) REFERENCES `taxcode` (`taxcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table invoiceitem
# ------------------------------------------------------------

CREATE TABLE `invoiceitem` (
  `invoiceitem_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `invoice_id` bigint(20) unsigned NOT NULL,
  `sku` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `unit_price` double(10,2) NOT NULL,
  `taxcode_id` int(10) unsigned NOT NULL DEFAULT '1',
  `line_total` double(10,2) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`invoiceitem_id`),
  KEY `taxcode_id` (`taxcode_id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoiceitem_ibfk_1` FOREIGN KEY (`taxcode_id`) REFERENCES `taxcode` (`taxcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table invoicelog
# ------------------------------------------------------------

CREATE TABLE `invoicelog` (
  `invoice_id` bigint(20) unsigned NOT NULL,
  `invoicelog_status` enum('','pending','outstanding','paid','void') NOT NULL DEFAULT '',
  `invoicelog_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoicelog_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table invoicepayment
# ------------------------------------------------------------

CREATE TABLE `invoicepayment` (
  `invoice_id` bigint(20) unsigned NOT NULL,
  `invoicepayment_amount` double(11,4) NOT NULL,
  `invoicepayment_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `invoicepayment_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table payment
# ------------------------------------------------------------

CREATE TABLE `payment` (
  `payment_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` bigint(20) unsigned DEFAULT NULL,
  `payment_amount` double(10,2) DEFAULT NULL,
  `payment_log` text,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `contact_id` (`contact_id`),
  CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table paymentreconcile
# ------------------------------------------------------------

CREATE TABLE `paymentreconcile` (
  `paymentreconcile_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `payment_id` bigint(20) unsigned DEFAULT NULL,
  `invoice_id` bigint(20) unsigned DEFAULT NULL,
  `paymentreconcile_amount` double(11,2) DEFAULT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`paymentreconcile_id`),
  KEY `invoice_id` (`invoice_id`),
  KEY `payment_id` (`payment_id`),
  CONSTRAINT `paymentreconcile_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`invoice_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `paymentreconcile_ibfk_2` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table purchase
# ------------------------------------------------------------

CREATE TABLE `purchase` (
  `purchase_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_reference` varchar(255) NOT NULL,
  `purchase_from_company` varchar(255) NOT NULL,
  `purchase_from_address1` varchar(255) NOT NULL,
  `purchase_from_address2` varchar(255) NOT NULL,
  `purchase_from_address3` varchar(255) NOT NULL,
  `purchase_from_address4` varchar(255) NOT NULL,
  `purchase_from_address5` varchar(255) NOT NULL,
  `purchase_from_postcode` varchar(12) NOT NULL,
  `purchase_from_country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `purchase_from_vatnumber` varchar(45) NOT NULL,
  `contact_id` bigint(20) unsigned DEFAULT NULL,
  `purchase_to_company` varchar(255) NOT NULL,
  `purchase_to_address1` varchar(255) NOT NULL,
  `purchase_to_address2` varchar(255) NOT NULL,
  `purchase_to_address3` varchar(255) NOT NULL,
  `purchase_to_address4` varchar(255) NOT NULL,
  `purchase_to_address5` varchar(255) NOT NULL,
  `purchase_to_postcode` varchar(12) NOT NULL,
  `purchase_to_country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `purchase_to_vatnumber` varchar(45) NOT NULL,
  `purchase_date` date NOT NULL,
  `taxcode_id` int(10) unsigned NOT NULL DEFAULT '1',
  `purchase_total` double(11,4) NOT NULL,
  `purchase_tax` double(11,4) NOT NULL,
  `purchase_status` enum('pending','outstanding','paid','void') NOT NULL DEFAULT 'pending',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`purchase_id`),
  KEY `contact` (`contact_id`),
  KEY `taxcode_id` (`taxcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table purchaselog
# ------------------------------------------------------------

CREATE TABLE `purchaselog` (
  `purchase_id` bigint(20) unsigned NOT NULL,
  `purchaselog_status` enum('','pending','outstanding','paid','void') NOT NULL DEFAULT '',
  `purchaselog_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `purchase_id` (`purchase_id`),
  CONSTRAINT `purchaselog_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table purchasepayment
# ------------------------------------------------------------

CREATE TABLE `purchasepayment` (
  `purchase_id` bigint(20) unsigned NOT NULL,
  `purchasepayment_amount` double(11,4) NOT NULL,
  `purchasepayment_date` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  KEY `purchase_id` (`purchase_id`),
  CONSTRAINT `purchasepayment_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table repeatinvoice
# ------------------------------------------------------------

CREATE TABLE `repeatinvoice` (
  `repeatinvoice_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` bigint(20) unsigned DEFAULT NULL,
  `repeat_period` enum('weekly','monthly','quarterly','annually') NOT NULL DEFAULT 'annually',
  `next_date` date NOT NULL,
  `last_invoice_id` bigint(20) unsigned NOT NULL,
  `repeatinvoice_status` enum('pending','active','stopped') NOT NULL DEFAULT 'pending',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`repeatinvoice_id`),
  KEY `contact` (`contact_id`),
  KEY `last_invoice` (`last_invoice_id`),
  CONSTRAINT `repeatinvoice_ibfk_3` FOREIGN KEY (`contact_id`) REFERENCES `contact` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table repeatinvoiceitem
# ------------------------------------------------------------

CREATE TABLE `repeatinvoiceitem` (
  `repeatinvoiceitem_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `repeatinvoice_id` bigint(20) unsigned NOT NULL,
  `sku` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `unit_price` double(10,2) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`repeatinvoiceitem_id`),
  KEY `repeatinvoice_id` (`repeatinvoice_id`),
  CONSTRAINT `repeatinvoiceitem_ibfk_1` FOREIGN KEY (`repeatinvoice_id`) REFERENCES `repeatinvoice` (`repeatinvoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table taxcode
# ------------------------------------------------------------

CREATE TABLE `taxcode` (
  `taxcode_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `taxcode_name` varchar(128) NOT NULL,
  `taxcode_calc` varchar(12) NOT NULL,
  PRIMARY KEY (`taxcode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `taxcode` WRITE;
/*!40000 ALTER TABLE `taxcode` DISABLE KEYS */;

INSERT INTO `taxcode` (`taxcode_id`, `taxcode_name`, `taxcode_calc`)
VALUES
	(1,'None',''),
	(2,'VAT (Standard Rate)','*0.2');

/*!40000 ALTER TABLE `taxcode` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user
# ------------------------------------------------------------

CREATE TABLE `user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varbinary(255) NOT NULL,
  `password` varbinary(255) NOT NULL,
  `user_firstname` varchar(255) NOT NULL,
  `user_lastname` varchar(255) NOT NULL,
  `user_company` varchar(255) NOT NULL,
  `user_address1` varchar(255) NOT NULL,
  `user_address2` varchar(255) NOT NULL,
  `user_address3` varchar(255) NOT NULL,
  `user_address4` varchar(255) NOT NULL,
  `user_address5` varchar(128) NOT NULL,
  `user_postcode` varchar(12) NOT NULL,
  `country_code` varchar(2) NOT NULL DEFAULT 'GB',
  `user_email` varchar(255) NOT NULL,
  `user_activationcode` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `status` enum('active','pending','suspended','closed') NOT NULL DEFAULT 'active',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  KEY `country_code` (`country_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
