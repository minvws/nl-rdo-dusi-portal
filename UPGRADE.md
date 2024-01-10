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

## Next release

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
