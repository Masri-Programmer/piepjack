-- Adminer 5.4.2 MariaDB 10.6.19-MariaDB dump

SET NAMES utf8;

SET time_zone = '+00:00';

SET foreign_key_checks = 0;

SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `launch_registrations`;

CREATE TABLE `launch_registrations` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `sent_online_notification_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `launch_registrations_email_unique` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

INSERT INTO
    `launch_registrations` (
        `id`,
        `name`,
        `email`,
        `sent_online_notification_at`,
        `created_at`,
        `updated_at`
    )
VALUES (
        2,
        'mohamad',
        'mohmasri9753@gmail.com',
        NULL,
        '2026-03-05 17:00:05',
        '2026-03-05 17:00:05'
    ),
    (
        3,
        'Son Goku',
        'xxxxxx@piepjacksoon.de',
        NULL,
        '2026-03-09 19:39:35',
        '2026-03-09 19:39:35'
    ),
    (
        4,
        'Jens Rat',
        'jens.rat@icloud.co',
        NULL,
        '2026-03-09 21:42:10',
        '2026-03-09 21:42:10'
    ),
    (
        5,
        'Phil Pieprzyk',
        'Phil.pieprzyk@web.de',
        NULL,
        '2026-03-10 06:55:26',
        '2026-03-10 06:55:26'
    ),
    (
        6,
        'Marvin Pieprzyk',
        'piepjack1@web.de',
        NULL,
        '2026-03-10 07:00:47',
        '2026-03-10 07:00:47'
    ),
    (
        7,
        'Niklas Kimmel',
        'niklas.kimmel96@gmail.com',
        NULL,
        '2026-03-10 10:17:58',
        '2026-03-10 10:17:58'
    ),
    (
        8,
        'Mavin piepjack',
        'piepjack@web.de',
        NULL,
        '2026-03-10 15:28:54',
        '2026-03-10 15:28:54'
    ),
    (
        9,
        'Jens',
        'jens.rat@icloud.com',
        NULL,
        '2026-03-10 16:55:31',
        '2026-03-10 16:55:31'
    ),
    (
        10,
        'Lord Jaro',
        'jaro.becker@gmx.de',
        NULL,
        '2026-03-10 17:59:35',
        '2026-03-10 17:59:35'
    ),
    (
        11,
        'Rezan Rashki',
        'rizanrashki50@gmail.com',
        NULL,
        '2026-03-13 10:33:39',
        '2026-03-13 10:33:39'
    ),
    (
        12,
        'Nilufar',
        'nilufar.asgari@gmx.de',
        NULL,
        '2026-03-14 17:25:35',
        '2026-03-14 17:25:35'
    ),
    (
        13,
        'Kevin Pieprzyk',
        'kevinpiep90@msn.com',
        NULL,
        '2026-03-14 22:36:52',
        '2026-03-14 22:36:52'
    ),
    (
        14,
        'Malte Niemeyer',
        'maltemiemeyer@web.de',
        NULL,
        '2026-03-16 17:37:13',
        '2026-03-16 17:37:13'
    ),
    (
        15,
        'Germain',
        'germain.ehlert@gmail.com',
        NULL,
        '2026-03-16 18:24:06',
        '2026-03-16 18:24:06'
    ),
    (
        16,
        'Amina',
        'aminazyy@outlook.de',
        NULL,
        '2026-03-17 19:22:17',
        '2026-03-17 19:22:17'
    ),
    (
        17,
        'Richard Panzertape Tappe mit D am Ende ;)',
        'richard.tappe@web.de',
        NULL,
        '2026-03-25 18:51:12',
        '2026-03-25 18:51:12'
    ),
    (
        18,
        'Mohamad',
        'masri_mohamad@protonmail.com',
        NULL,
        '2026-04-06 21:03:56',
        '2026-04-06 21:03:56'
    ),
    (
        19,
        'Peggy  Jordan',
        'peggy84jordan@gmail.com',
        NULL,
        '2026-04-10 15:02:32',
        '2026-04-10 15:02:32'
    ),
    (
        20,
        'Tine',
        'c.oechsner@gmx.de',
        NULL,
        '2026-04-10 15:07:12',
        '2026-04-10 15:07:12'
    );

-- 2026-04-13 12:19:01 UTC