<?php

use App\Support\DatabaseUpgrade\MySqlCreateTableNormalizer;

test('mysql create table normalizer strips volatile auto increment values', function () {
    $left = "CREATE TABLE `example` (`id` bigint unsigned NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    $right = "CREATE TABLE `example` (`id` bigint unsigned NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=314 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

    expect(MySqlCreateTableNormalizer::normalize($left))
        ->toBe(MySqlCreateTableNormalizer::normalize($right));
});

test('mysql create table normalizer collapses noisy whitespace', function () {
    $sql = "CREATE   TABLE `example`\n(\n `id` bigint unsigned NOT NULL AUTO_INCREMENT,\n PRIMARY KEY (`id`)\n)\nENGINE=InnoDB";

    expect(MySqlCreateTableNormalizer::normalize($sql))
        ->toBe('CREATE TABLE `example` ( `id` bigint unsigned NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`) ) ENGINE=InnoDB');
});
