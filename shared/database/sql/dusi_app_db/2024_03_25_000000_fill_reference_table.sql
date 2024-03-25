INSERT INTO public.application_references (reference, used, deleted, created_at, updated_at)
SELECT a.reference, true, false, a.created_at, a.created_at
FROM applications a;
