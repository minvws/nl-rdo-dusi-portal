BEGIN;

UPDATE subsidies
SET
    valid_from = '2023-09-25 09:00 CEST',
    valid_to = '2023-10-23 12:00 CEST'
WHERE id = '06a6b91c-d59b-401e-a5bf-4bf9262d85f8';

COMMIT;
