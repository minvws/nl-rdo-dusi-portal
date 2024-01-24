
-- Handle with care, execute manually:
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM user_admin_dusi;
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM assessment_web_dusi;
-- REVOKE ALL ON ALL TABLES IN SCHEMA public FROM backend_dusi;

-- Cleanup alternate from with dashes
-- DROP ROLE IF EXISTS "dpw-dusi";
-- DROP ROLE IF EXISTS "backend-dusi";
-- DROP ROLE IF EXISTS "user-admin-dusi";
-- DROP ROLE IF EXISTS "assessment-web-dusi";

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

ALTER TABLE public.migrations OWNER TO dusi_dba;
ALTER TABLE public.organisations OWNER TO dusi_dba;
ALTER TABLE public.role_user OWNER TO dusi_dba;
ALTER TABLE public.roles OWNER TO dusi_dba;
ALTER TABLE public.users OWNER TO dusi_dba;
ALTER TABLE public.deploy_releases OWNER TO dusi_dba;

GRANT SELECT ON TABLE public.deploy_releases TO "user-admin-dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.deploy_releases TO "backend_dusi";

GRANT SELECT ON TABLE public.migrations TO "user_admin_dusi";
GRANT SELECT ON TABLE public.migrations TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.migrations TO "backend_dusi";

GRANT SELECT,INSERT,UPDATE ON TABLE public.organisations TO "user_admin_dusi";
GRANT SELECT ON TABLE public.organisations TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.organisations TO "backend_dusi";

GRANT SELECT,UPDATE,INSERT,DELETE ON TABLE public.role_user TO "user_admin_dusi";
GRANT SELECT ON TABLE public.role_user TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.role_user TO "backend_dusi";

GRANT SELECT ON TABLE public.roles TO "user_admin_dusi";
GRANT SELECT ON TABLE public.roles TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.roles TO "backend_dusi";

GRANT SELECT,INSERT,UPDATE ON TABLE public.users TO "user_admin_dusi";
GRANT SELECT ON TABLE public.users TO "assessment_web_dusi";
GRANT UPDATE (password, password_updated_at,password_reset_token,password_reset_token_valid_until) ON TABLE public.users TO "assessment_web_dusi";
GRANT SELECT ON TABLE public.users TO "backend_dusi";


