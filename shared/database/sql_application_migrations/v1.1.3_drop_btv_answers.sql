--
-- Dropping data for incomplete disabled subsidy
--

BEGIN; 

DELETE FROM public.answers WHERE field_id in (SELECT id FROM public.fields WHERE subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045');
DELETE FROM public.application_stages WHERE subsidy_stage_id = '721c1c28-e674-415f-b1c3-872a631ed045';
DELETE FROM public.applications WHERE subsidy_version_id = '907bb399-0d19-4e1a-ac75-25a864df27c6';

COMMIT;
