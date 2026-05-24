# Live Upgrade Patch Files

Put optional table-specific rehearsal patch files in this directory when the local rehearsal command reports schema drift.

Naming convention:

- `employer_appointments.sql`
- `versions.sql`
- `change_logs.sql`
- `cadre_circulars.sql`
- `cadre_d_m_s_approveds.sql`
- `employer_cadre_subjects.sql`
- `subject_lists.sql`
- `grade_spans.sql`
- `personal_access_tokens.sql`

Rules:

- Write only safe schema-fix SQL for the rehearsal clone.
- Small bounded cleanup statements are allowed when required to remove invalid rows that would otherwise block new foreign keys.
- Do not include `DROP DATABASE`, `CREATE DATABASE`, or `USE`.
- The rehearsal command runs each patch against the target rehearsal database after cloning and before schema comparison.
- If the patch still does not align the target schema with `cemisnew`, the command will stop and write a failure report.
