
-- van 1.0.0 naar 1.1.0
ALTER TABLE public.roles  ADD view_all_stages boolean DEFAULT false NOT NULL;

insert into public.roles ("name") values ('userAdmin');
update public.role_user set "role_name" = 'userAdmin' where "role_name" = 'admin';
delete from public.roles where "name" = 'admin';

insert into public.roles ("name") values ('assessor');
update public.role_user set "role_name" = 'assessor' where "role_name" = 'practitioner';
delete from public.roles where "name" = 'practitioner';
