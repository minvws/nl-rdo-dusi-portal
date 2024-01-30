CREATE TABLE public.deploy_releases
(
        version varchar(255),
        deployed_at timestamp default now()
);

INSERT INTO public.deploy_releases(version) values ('v1.0.0');

ALTER TABLE public.deploy_releases OWNER TO dusi_dba;

GRANT SELECT ON public.deploy_releases TO  "dpw-dusi";
GRANT SELECT ON public.deploy_releases TO  "backend-dusi";
GRANT SELECT ON public.deploy_releases TO  "user-admin-dusi";

GRANT ALL PRIVILEGES ON DATABASE dusi_app_db to "dpw-dusi";
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "dpw-dusi";

GRANT ALL PRIVILEGES ON DATABASE dusi_app_db to "backend-dusi";
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "backend-dusi";

GRANT ALL PRIVILEGES ON DATABASE dusi_app_db to "assessment-web-dusi";
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "assessment-web-dusi";

-- \c dusi_app_db

--- GRANT ALL ON ALL TABLES IN SCHEMA public TO "user-admin-acc-dusi";

--- Mogelijk enkel nodig op de subsidies table
---  GRANT ALL ON subsidies IN SCHEMA public TO "user-admin-acc-dusi"   -- FIXME syntax



-- GRANT ALL PRIVILEGES ON DATABASE migratie_test to "dpw-acc-dusi";
-- GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "dpw-acc-dusi";

-- GRANT ALL PRIVILEGES ON DATABASE migratie_test to "backend-acc-dusi";
-- GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "backend-acc-dusi";

-- GRANT ALL PRIVILEGES ON DATABASE migratie_test to "assessment-web-acc-dusi";
-- GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "assessment-web-acc-dusi";
