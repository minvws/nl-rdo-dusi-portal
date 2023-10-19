
-- van 1.0.0 naar 1.1.0
ALTER TABLE roles  ADD view_all_stages boolean DEFAULT false NOT NULL;

insert into "roles" ("name") values ('userAdmin');
update "role_user" set "role_name" = 'userAdmin' where "role_name" = 'admin';
delete from "roles" where "name" = 'admin';

insert into "roles" ("name") values ('assessor');
update "role_user" set "role_name" = 'assessor' where "role_name" = 'practitioner';
delete from "roles" where "name" = 'practitioner';
