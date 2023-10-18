BEGIN;

ALTER TABLE "subsidy_stages" ADD COLUMN "internal_note_field_code" varchar(255) NULL;

UPDATE subsidy_stages SET internal_note_field_code = 'firstAssessmentInternalNote' WHERE id = '8027c102-93ef-4735-ab66-97aa63b836eb';
UPDATE subsidy_stages SET internal_note_field_code = 'secondAssessmentInternalNote' WHERE id = '61436439-e337-4986-bc18-57138e2fab65';
UPDATE subsidy_stages SET internal_note_field_code = 'internalAssessmentInternalNote' WHERE id = '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68';
UPDATE subsidy_stages SET internal_note_field_code = 'coordinatorImplementationInternalNote' WHERE id = '85ed726e-cdbe-444e-8d12-c56f9bed2621';

COMMIT;
