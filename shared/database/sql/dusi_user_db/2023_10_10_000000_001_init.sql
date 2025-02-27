
\c dusi_user_db

CREATE TABLE deploy_releases
(
        version varchar(255),
        deployed_at timestamp default now()
);

CREATE ROLE dusi_dba;
CREATE ROLE "dpw-dusi" WITH LOGIN;
CREATE ROLE "backend_dusi" WITH LOGIN;
CREATE ROLE "user-admin-dusi" WITH LOGIN;
CREATE ROLE "assessment-web-dusi" WITH LOGIN;

ALTER TABLE public.deploy_releases OWNER TO dusi_dba;

GRANT SELECT ON public.deploy_releases TO  "dpw-dusi";
GRANT SELECT ON public.deploy_releases TO  "backend_dusi";
GRANT SELECT ON public.deploy_releases TO  "user-admin-dusi";

GRANT ALL PRIVILEGES ON DATABASE dusi_user_db to "assessment-web-dusi";

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public to "assessment-web-dusi";

GRANT ALL PRIVILEGES ON DATABASE dusi_user_db to "user-admin-dusi";

insert into public.deploy_releases(version) values ('v0.0.0');

-- GRANT ALL PRIVILEGES ON DATABASE dusi_user_db to "user-admin-dusi";
