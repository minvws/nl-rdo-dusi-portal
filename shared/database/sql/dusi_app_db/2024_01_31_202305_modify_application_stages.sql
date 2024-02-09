ALTER TABLE public.application_stages ADD COLUMN expires_at DATE NULL;
ALTER TABLE public.application_stages ADD COLUMN closed_at TIMESTAMP NULL;
UPDATE public.application_stages SET closed_at = submitted_at;
