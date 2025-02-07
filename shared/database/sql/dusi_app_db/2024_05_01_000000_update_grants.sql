
-- Handle with care, execute manually:
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "dpw-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "user-admin-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "assessment-web-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "backend-dusi";

-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "dpw_dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "user_admin_dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "assessment_web_dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "backend_dusi";

-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "dpw-acc-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "user-admin-acc-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "assessment-web-acc-dusi";
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM "backend-acc-dusi";


-- Cleanup alternate from with dashes
-- DROP ROLE IF EXISTS "dpw-dusi";
-- DROP ROLE IF EXISTS "backend-dusi";
-- DROP ROLE IF EXISTS "user-admin-dusi";
-- DROP ROLE IF EXISTS "assessment-web-dusi";

-- DROP ROLE IF EXISTS "dpw-acc-dusi";
-- DROP ROLE IF EXISTS "backend-acc-dusi";
-- DROP ROLE IF EXISTS "user-admin-acc-dusi";
-- DROP ROLE IF EXISTS "assessment-web-acc-dusi";


do $$
<<first_block>>
declare
  ln_count integer := 0;
begin
   -- Check if rol exists before creating.
   select count(*)
   into ln_count
   from pg_roles
   where rolname = 'dpw_dusi';

   if ln_count = 0 then
     CREATE role dpw_dusi;
   end if;
   select count(*)
   into ln_count
   from pg_roles
   where rolname = 'backend_dusi';

   if ln_count = 0 then
     CREATE role backend_dusi;
   end if;
   select count(*)
   into ln_count
   from pg_roles
   where rolname = 'user_admin_dusi';

   if ln_count = 0 then
     CREATE role user_admin_dusi;
   end if;
   select count(*)
   into ln_count
   from pg_roles
   where rolname = 'assessment_web_dusi';

   if ln_count = 0 then
     CREATE role assessment_web_dusi;
   end if;
end first_block $$;

ALTER ROLE user_admin_dusi WITH LOGIN;
ALTER ROLE dpw_dusi WITH LOGIN;
ALTER ROLE assessment_web_dusi WITH LOGIN;
ALTER ROLE backend_dusi WITH LOGIN;

-- Deploy releases should be readable for every user
GRANT SELECT ON TABLE public.deploy_releases TO "user_admin_dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "dpw_dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "backend_dusi";

-- Audit logs should be insertable for every user
GRANT INSERT ON TABLE public.audit_logs TO "user_admin_dusi";
GRANT INSERT ON TABLE public.audit_logs TO "dpw_dusi";
GRANT INSERT ON TABLE public.audit_logs TO "assessment_web_dusi";
GRANT INSERT ON TABLE public.audit_logs TO "backend_dusi";

-- Permissions for user-admin
GRANT SELECT ON TABLE public.subsidies TO "user_admin_dusi";

-- Permissions for application-api
GRANT SELECT ON TABLE public.fields TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidies TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hash_fields TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hashes TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transition_messages TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transitions TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_uis TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_stages TO "dpw_dusi";
GRANT SELECT ON TABLE public.subsidy_versions TO "dpw_dusi";

-- permissions for assessment-api
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.answers TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.application_hashes TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.application_messages TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.application_stage_transitions TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.application_stages TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.application_surepay_results TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.applications TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.failed_jobs TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.fields TO "assessment_web_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.identities TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidies TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hash_fields TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hashes TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transition_messages TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transitions TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_uis TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_stages TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.subsidy_versions TO "assessment_web_dusi";

GRANT USAGE ON SEQUENCE public.failed_jobs_id_seq TO "assessment_web_dusi";

-- permissions for application-backend
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.answers TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.application_hashes TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.application_messages TO "backend_dusi";
GRANT SELECT,INSERT ON TABLE public.application_references TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.application_stage_transitions TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.application_stages TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.application_surepay_results TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.applications TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE ON TABLE public.failed_jobs TO "backend_dusi";
GRANT SELECT ON TABLE public.fields TO "backend_dusi";
GRANT SELECT,INSERT,UPDATE,DELETE ON TABLE public.identities TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidies TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hash_fields TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_hashes TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transition_messages TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_transitions TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stage_uis TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_stages TO "backend_dusi";
GRANT SELECT ON TABLE public.subsidy_versions TO "backend_dusi";

GRANT USAGE ON SEQUENCE public.failed_jobs_id_seq TO "backend_dusi";
