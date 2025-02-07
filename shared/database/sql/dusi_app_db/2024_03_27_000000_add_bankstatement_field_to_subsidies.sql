-- AIGT - bankstatement field
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id)
VALUES ('18f6cc1a-b80d-48cc-884b-d05bc646bf4e', 'Rekeningafschrift', NULL, 'upload', '{"mimeTypes":["image\/jpeg,image\/png,application\/pdf"],"maxFileSize":20971520,"minItems": 1,"maxItems": 20}', false, 'bankStatement', 'user', 'a0f9ed92-c553-42d9-aef6-707bdfadd2d1');

-- BTV - bankstatement field
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id)
VALUES ('7920399c-28be-44aa-a39c-9bacb1ebf2d7', 'Rekeningafschrift', NULL, 'upload', '{"mimeTypes":["image\/jpeg,image\/png,application\/pdf"],"maxFileSize":20971520,"minItems": 1,"maxItems": 20}', false, 'bankStatement', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');

-- DAMU - bankstatement field
INSERT INTO public.fields (id, title, description, type, params, is_required, code, source, subsidy_stage_id)
VALUES ('d3b820b7-8ae4-4678-b5cf-39c134d764cd', 'Rekeningafschrift', NULL, 'upload', '{"mimeTypes":["image\/jpeg,image\/png,application\/pdf"],"maxFileSize":20971520,"minItems": 1,"maxItems": 20}', false, 'bankStatement', 'user', '77996a9c-5c8d-47e1-9a88-e41bf594cfc8');
