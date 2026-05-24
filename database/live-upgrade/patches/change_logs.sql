ALTER TABLE `change_logs`
    MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `change_logs`
    ADD CONSTRAINT `change_logs_version_id_foreign`
        FOREIGN KEY (`version_id`) REFERENCES `versions` (`version_id`)
        ON UPDATE CASCADE;
