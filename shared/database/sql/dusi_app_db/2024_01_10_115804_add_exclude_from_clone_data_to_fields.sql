ALTER TABLE public.fields ADD COLUMN "exclude_from_clone_data" boolean DEFAULT false NOT NULL;

UPDATE public.fields
SET exclude_from_clone_data = true
WHERE code = 'truthfullyCompleted';
