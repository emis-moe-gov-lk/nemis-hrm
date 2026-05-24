-- Remove orphaned legacy rows that cannot satisfy the new foreign keys.
-- On the April 27, 2026 rehearsal against the imported live DB, this removed 7 rows.
DELETE ecs
FROM `employer_cadre_subjects` AS ecs
LEFT JOIN `employer_appointments` AS ea
    ON ea.`appointment_id` = ecs.`appointment_id`
LEFT JOIN `people` AS employee
    ON employee.`people_id` = ecs.`employee_id`
LEFT JOIN `medium_of_instructions` AS moi
    ON moi.`medium_id` = ecs.`appointment_medium`
LEFT JOIN `subject_lists` AS sl
    ON sl.`subject_id` = ecs.`main_subject`
WHERE ea.`appointment_id` IS NULL
   OR employee.`people_id` IS NULL
   OR moi.`medium_id` IS NULL
   OR sl.`subject_id` IS NULL;

ALTER TABLE `employer_cadre_subjects`
    MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `employer_cadre_subjects`
    ADD CONSTRAINT `employer_cadre_subjects_appointment_id_foreign`
        FOREIGN KEY (`appointment_id`) REFERENCES `employer_appointments` (`appointment_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_cadre_subjects_appointment_medium_foreign`
        FOREIGN KEY (`appointment_medium`) REFERENCES `medium_of_instructions` (`medium_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_cadre_subjects_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `employer_cadre_subjects_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `people` (`people_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_cadre_subjects_main_subject_foreign`
        FOREIGN KEY (`main_subject`) REFERENCES `subject_lists` (`subject_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_cadre_subjects_updated_by_foreign`
        FOREIGN KEY (`updated_by`) REFERENCES `people` (`people_id`);
