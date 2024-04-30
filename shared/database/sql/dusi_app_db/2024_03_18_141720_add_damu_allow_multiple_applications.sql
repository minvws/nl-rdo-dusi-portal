ALTER TABLE public.subsidies ADD COLUMN "allow_multiple_applications" boolean DEFAULT false NOT NULL;

UPDATE public.subsidies
SET allow_multiple_applications = true
WHERE id = '7b9f1318-4c38-4fe5-881b-074729d95abf';

