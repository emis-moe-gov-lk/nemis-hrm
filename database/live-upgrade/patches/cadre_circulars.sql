ALTER TABLE `cadre_circulars`
    MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cadre_circulars`
    ADD CONSTRAINT `cadre_circulars_supersedes_id_foreign`
        FOREIGN KEY (`supersedes_id`) REFERENCES `cadre_circulars` (`circular_id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;
