# Upgrade notes

This file contains notes on how to upgrade the application to a new version.
The notes are added in reverse chronological order.

With the following git command you can see the changes between two tags. After creating the release you can include the
output of this command in the release request.

```shell
git diff v0.0.1..v1.0.0 UPGRADE.md
```

Make sure to make clear **what** needs to be done, **where** it needs to be done, **when** it needs to be done and
**what** the expected result is. If possible, also include a link to the relevant issues and/or pull requests.

See the following example for how to add a new note:

```markdown
### Brief description
Please run the artisan command `php artisan specific_command` on the backend application after upgrading to this
version. This command should exit cleanly without any errors.
See https://github.com/minvws/nl-rdo-dusi-portal/issues/397.
```

## Notes

Add the newest note to the top, below this line:

====================================================================================================

## Release 2.6.2

### DUSI-1588 Migrate audit syslogs to pgsql

First run the database migration '2024_04_15_140115_create_auditlog_table.sql'. Next setup audit db logging for the
following backend environments:  

- application-api
- application-backend
- assessment-api
- user-admin-api

```shell
AUDIT_DBLOG_ENABLED=true
AUDIT_DBLOG_THEIR_PUB_KEY=[public key]
AUDIT_DBLOG_OUR_PRIV_KEY=[private key]
```

Syslogging can be disabled by removing the env value AUDIT_SYSLOG_ENABLED or set it to false.

The logs in de assessment-api need to be migrated to the new audit_logs table. The audit logs are stored in
storage/logs/audit.log. This file is reset during deployments. Older audit logs are stored in the backup folders.
Before migration all older log files need to be merged to a single file. After merging the files the following
command should be run in the assessment-api:

```shell
php artisan app:migrate-audit-logging 
  --their-pub-nacl-key=[AUDIT_SYSLOG_THEIR_PUB_KEY] 
  --our-priv-nacl-key=[AUDIT_SYSLOG_OUR_PRIV_KEY] 
  storage/logs/merged_audit.log
````

## Release 2.6.1

### DUSI-252: Increased grant notification for PCZM v1

After running the the migration script '2024_04_02_152701_add_stage_pczm_v1.sql', run the following artisan command
from the public folder in the assessment-api app (assuming the artisan script is in the root folder of the
assessment-api):

php ../artisan pczm:increase-grant

## Release 2.6.0

### DUSI-252 New PCZM version

This adds a new subsidy version for PCZM, run `shared/database/sql/dusi_app_db/2024_04_09_132315_switch_pczm_subsidy_version.sql` for this. This will also archive the first version of PCZM.

PR: <https://github.com/minvws/nl-rdo-dusi-portal/pull/1589>

### DUSI-1570 AIGTfixes

Please run `shared/database/sql/dusi_app_db/2024_04_09_131744_aigt_fix.sql`.

PR: <https://github.com/minvws/nl-rdo-dusi-portal/pull/1586>

### DUSI-1569 DAMU fixes

Please run `shared/database/sql/dusi_app_db/2024_04_09_160001_damu_fix.sql`.

PR: <https://github.com/minvws/nl-rdo-dusi-portal/pull/1587>

### DUSI-638 Cleanup command for draft and old applications

This will add a command for cleaning up `draft` applications and applications that are older then 10 years. Please add `artisan app:cleanup-applications` to a cron job.

This will also introduce a new table for keeping track of the application references. Please run `shared/database/sql/dusi_app_db/2024_03_25_000000_fill_reference_table.sql`.

PR: <https://github.com/minvws/nl-rdo-dusi-portal/pull/1515>

## Release 2.4.3

### DUSI-1516 Remove btv stage transitions usages

For the test and acceptance environment, run the following sql queries before migrating:

```postgresql
delete from public.application_messages where application_stage_transition_id in (select id from public.application_stage_transitions where subsidy_stage_transition_id = '5b876216-ba37-4b13-aa99-e311db027d6b');
delete from public.application_stage_transitions where subsidy_stage_transition_id = '5b876216-ba37-4b13-aa99-e311db027d6b';
delete from public.subsidy_stage_transition_messages where subsidy_stage_transition_id = '5b876216-ba37-4b13-aa99-e311db027d6b';
```

### DUSI-1470: Add ClamAV file scanning to file uploading on assessment api

ClamAV needs to be added on the Assessment API servers.
We have the same ClamAV settings available as we already have for
the backend application.

PR <https://github.com/minvws/nl-rdo-dusi-portal/pull/1470>

## Release 2.3.0

### DUSI-1265: Add timout transition for BTV and AIGT subsidies

The `2024_02_19_000000_add_stage_timout_transitions_for_btv_aigt_subsidies.sql` SQL update needs to run to add the timeout transitions for BTV and AIGT subsidies.
PR <https://github.com/minvws/nl-rdo-dusi-portal/pull/1266>

### DUSI-632: Create AIGT subsidy

A signature needs to be added:  
assessment-api/storage/subsidy-files/cb91d7d4-6261-4cd6-96e8-d09c86a670b7/vws_dusi_signature.jpg

This feature contains a dusi_app_db migration.  See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1118>

### DUSI-1218: Update BTV firstAssessmentChecklist

The `2024_02_18_000000_update_btv_first_assessment_checklist.sql` SQL update needs to run to update the firstAssmentChecklist.
PR <https://github.com/minvws/nl-rdo-dusi-portal/pull/1223>

## Release 2.1.5

### DUSI-1119: Hardheidsclausule letter typo

This feature contains app-DB migrations. See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1201>

## Release 2.1.4

### DUSI-1169 BTV flow update

This feature contains a dusi_app_db migration.  See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1199>

## Release 2.1.3

### DUSI-1119: Hardheidsclausule

This feature contains app-DB migrations. See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1197>

### DUSI-1169: More BTV fixes

This feature contains app-DB migrations. See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1198>

## Release 2.1.1

### DUSI-1169: BTV fixes

This feature contains app-DB migrations. See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1170>

## Release 2.1.0

### DUSI-1041: BTV updates

This feature contains a DB migration. See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1066>

## Release 2.0.0

### DUSI-1034: Add legalSpecialist role and filter on application list

Run the user_user_db SQL migrations up to version v1.5.0. See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1036>

### DUSI-858: Don't clone truthfullyCompleted field

Run the SQL migration which is added to version v1.3.9.
See <https://github.com/minvws/nl-rdo-dusi-portal/pull/1032>

## Release 1.10.4

### DUSI-491: SurePay CloseMatch resultaat tonen in behandelportaal

Run the SQL migration which is added to version v1.3.8.
See <https://github.com/minvws/nl-rdo-dusi-portal/pull/893>

### DUSI-179: BTV updaten

For the BTV subsidy a signature needs to be added:
assessment-api/storage/subsidy-files/00f26400-7232-475f-922c-6b569b7e421a/vws_dusi_signature.jpg

To setup the BTV subsidy a SQL migration needs to be run, which is part of migration version v1.3.8.
More details: <https://github.com/minvws/nl-rdo-dusi-portal/pull/992>

## Release v1.10.3

### The calculated fieldHashes need to be updated

Run the artisan command to calculate the fieldHashes for all submitted applications. All hashes with fields with a
value of "0" should be updated to a new hash.
See <https://github.com/minvws/nl-rdo-dusi-portal/pull/911>
