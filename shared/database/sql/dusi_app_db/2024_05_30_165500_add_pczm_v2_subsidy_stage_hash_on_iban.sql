INSERT INTO public.subsidy_stage_hashes (id, subsidy_stage_id, description, created_at, updated_at, name)
VALUES ('ea1bcfad-3cc8-440b-bf65-617f224856a2', 'd7f38409-6805-408c-87e9-afd9b00a8de0',
        'Bank account duplicate reporting (version 2)', 'now()', 'now()', 'Bank account');

INSERT INTO public.subsidy_stage_hash_fields ("subsidy_stage_hash_id", "field_id")
VALUES ('ea1bcfad-3cc8-440b-bf65-617f224856a2', (select id
                                                 from public.fields
                                                 where "subsidy_stage_id" = 'd7f38409-6805-408c-87e9-afd9b00a8de0'
                                                   and "code" = 'bankAccountNumber'
                                                   and "title" = 'IBAN' limit 1)
);

