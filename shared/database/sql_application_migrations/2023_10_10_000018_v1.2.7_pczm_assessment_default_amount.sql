BEGIN;

UPDATE public.fields
SET title              = 'Bedrag',
    description        = null,
    type               = 'select',
    params             = '{"options":["\u20ac 15.000"],"default":"\u20ac 15.000"}',
    is_required        = false,
    code               = 'amount',
    source             = 'user',
    subsidy_stage_id   = '8027c102-93ef-4735-ab66-97aa63b836eb',
    required_condition = null
WHERE "subsidy_stage_id" = '8027c102-93ef-4735-ab66-97aa63b836eb'
  AND "code" = 'amount';

COMMIT;
