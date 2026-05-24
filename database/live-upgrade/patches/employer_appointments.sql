ALTER TABLE `employer_appointments`
    MODIFY COLUMN `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `employer_appointments`
    ADD INDEX `idx_emp_service_active` (`employee_id`, `service_id`, `active_status`),
    ADD INDEX `idx_workplace_service_level` (`workplace_id`, `service_id`, `office_level_id`),
    ADD INDEX `employer_appointments_service_id_index` (`service_id`),
    ADD INDEX `employer_appointments_workplace_id_index` (`workplace_id`),
    ADD INDEX `employer_appointments_w_op_no_index` (`w_op_no`),
    ADD INDEX `employer_appointments_pay_sheet_no_index` (`pay_sheet_no`);

ALTER TABLE `employer_appointments`
    ADD CONSTRAINT `employer_appointments_confirmed_by_foreign`
        FOREIGN KEY (`confirmed_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `employer_appointments_created_by_foreign`
        FOREIGN KEY (`created_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `employer_appointments_employee_id_foreign`
        FOREIGN KEY (`employee_id`) REFERENCES `people` (`people_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_appointments_office_level_id_foreign`
        FOREIGN KEY (`office_level_id`) REFERENCES `office_levels` (`office_level_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_appointments_position_id_foreign`
        FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_appointments_rank_id_foreign`
        FOREIGN KEY (`rank_id`) REFERENCES `service_ranks` (`rank_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_appointments_service_id_foreign`
        FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `employer_appointments_updated_by_foreign`
        FOREIGN KEY (`updated_by`) REFERENCES `people` (`people_id`),
    ADD CONSTRAINT `employer_appointments_verified_by_foreign`
        FOREIGN KEY (`verified_by`) REFERENCES `people` (`people_id`);
