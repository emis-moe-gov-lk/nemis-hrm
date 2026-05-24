ALTER TABLE `cadre_d_m_s_approveds`
    MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `cadre_d_m_s_approveds`
    ADD CONSTRAINT `cadre_d_m_s_approveds_circular_id_foreign`
        FOREIGN KEY (`circular_id`) REFERENCES `cadre_circulars` (`circular_id`)
        ON UPDATE CASCADE,
    ADD CONSTRAINT `cadre_d_m_s_approveds_confirmed_by_foreign`
        FOREIGN KEY (`confirmed_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `cadre_d_m_s_approveds_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `cadre_d_m_s_approveds_medium_id_foreign`
        FOREIGN KEY (`medium_id`) REFERENCES `medium_of_instructions` (`medium_id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    ADD CONSTRAINT `cadre_d_m_s_approveds_subject_id_foreign`
        FOREIGN KEY (`subject_id`) REFERENCES `subject_lists` (`subject_id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    ADD CONSTRAINT `cadre_d_m_s_approveds_updated_by_foreign`
        FOREIGN KEY (`updated_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `cadre_d_m_s_approveds_verified_by_foreign`
        FOREIGN KEY (`verified_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `cadre_d_m_s_approveds_workplace_id_foreign`
        FOREIGN KEY (`workplace_id`) REFERENCES `workplaces` (`workplace_id`)
        ON UPDATE CASCADE;
