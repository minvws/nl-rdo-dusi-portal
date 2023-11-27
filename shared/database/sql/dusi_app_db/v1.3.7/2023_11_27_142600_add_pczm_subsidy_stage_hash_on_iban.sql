INSERT INTO public.subsidy_stage_hashes (id, subsidy_stage_id, description, created_at, updated_at, name)
VALUES ('bd26ae6f-05ac-4690-81da-87b534f7758d', '7e5d64e9-35f0-4fee-b8d2-dca967b43183',
        'Bank account duplicate reporting', '2023-11-27 13:24:43', '2023-11-27 13:24:43', 'Bank account');

INSERT INTO subsidy_stage_hash_fields (subsidy_stage_hash_id, field_id)
SELECT
    'bd26ae6f-05ac-4690-81da-87b534f7758d',
    fields.id
FROM
    fields
WHERE
      fields.subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183'
  AND fields.code = 'bankAccountNumber'
  AND fields.title = 'IBAN'
LIMIT 1;
