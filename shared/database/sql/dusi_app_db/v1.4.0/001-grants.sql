
-- Handle with care, execute manually:
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "dpw-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "user-admin-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "assessment-web-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "backend-dusi";

-- Cleanup alternate from with dashes
-- DROP ROLE IF EXISTS "dpw-dusi";
-- DROP ROLE IF EXISTS "backend-dusi";
-- DROP ROLE IF EXISTS "user-admin-dusi";
-- DROP ROLE IF EXISTS "assessment-web-dusi";

GRANT ALL ON TABLE public.deploy_releases TO "user_admin_dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "backend_dusi";


GRANT SELECT ON TABLE public.answers TO "user_admin_dusi";
GRANT SELECT ON TABLE public.application_hashes TO "user_admin_dusi";
GRANT SELECT ON TABLE public.application_messages TO "user_admin_dusi";
GRANT SELECT ON TABLE public.application_stage_transitions TO "user_admin_dusi";
GRANT SELECT ON TABLE public.application_stages TO "user_admin_dusi";
GRANT SELECT ON TABLE public.application_surepay_results TO "user_admin_dusi";
GRANT SELECT ON TABLE public.applications TO "user_admin_dusi";
GRANT SELECT ON TABLE public.failed_jobs TO "user_admin_dusi";
GRANT SELECT ON TABLE public.fields TO "user_admin_dusi";
GRANT SELECT ON TABLE public.identities TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidies TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hash_fields TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hashes TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transition_messages TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transitions TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_uis TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_stages TO "user_admin_dusi";
GRANT SELECT ON TABLE public.subsidy_versions TO "user_admin_dusi";

GRANT SELECT ON TABLE public.answers TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.application_hashes TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.application_messages TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.application_stage_transitions TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.application_stages TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.application_surepay_results TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.applications TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.failed_jobs TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.fields TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.identities TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidies TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hash_fields TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hashes TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transition_messages TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transitions TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_uis TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stages TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_versions TO "assessment_web_dusi";

GRANT SELECT ON TABLE public.answers TO "backend_dusi";
GRANT SELECT ON TABLE public.application_hashes TO "backend_dusi";
GRANT SELECT ON TABLE public.application_messages TO "backend_dusi";
GRANT SELECT ON TABLE public.application_stage_transitions TO "backend_dusi";
GRANT SELECT ON TABLE public.application_stages TO "backend_dusi";
GRANT SELECT ON TABLE public.application_surepay_results TO "backend_dusi";
GRANT SELECT ON TABLE public.applications TO "backend_dusi";
GRANT SELECT ON TABLE public.failed_jobs TO "backend_dusi";
GRANT SELECT ON TABLE public.fields TO "backend_dusi";
GRANT SELECT ON TABLE public.identities TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidies TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hash_fields TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hashes TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transition_messages TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transitions TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_uis TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stages TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_versions TO "backend_dusi";

GRANT USAGE ON SEQUENCE public.failed_jobs_id_seq TO "assessment_web_dusi";
