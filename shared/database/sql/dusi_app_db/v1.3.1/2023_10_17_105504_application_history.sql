CREATE TABLE "application_stage_transitions"
(
    "id" uuid NOT NULL,
    "application_id" uuid NOT NULL,
    "subsidy_stage_transition_id" uuid NOT NULL,
    "previous_application_stage_id" uuid NOT NULL,
    "new_application_stage_id" uuid NULL,
    "previous_application_status" varchar(255) check ("previous_application_status" in ('draft', 'submitted', 'approved', 'rejected', 'requestForChanges')) NOT NULL,
    "new_application_status" varchar(255) check ("new_application_status" in ('draft', 'submitted', 'approved', 'rejected','requestForChanges')) NOT NULL,
    "created_at" timestamp(0) without time zone NOT NULL
);

ALTER TABLE "application_stage_transitions" ADD CONSTRAINT "application_stage_transitions_application_id_foreign" FOREIGN KEY ("application_id") REFERENCES "applications" ("id");
ALTER TABLE "application_stage_transitions" ADD CONSTRAINT "application_stage_transitions_subsidy_stage_transition_id_foreign" FOREIGN KEY ("subsidy_stage_transition_id") REFERENCES "subsidy_stage_transitions" ("id");
ALTER TABLE "application_stage_transitions" ADD CONSTRAINT "application_stage_transitions_previous_application_stage_id_foreign" FOREIGN KEY ("previous_application_stage_id") REFERENCES "application_stages" ("id");
ALTER TABLE "application_stage_transitions" ADD CONSTRAINT "application_stage_transitions_new_application_stage_id_foreign" FOREIGN KEY ("new_application_stage_id") REFERENCES "application_stages" ("id");
ALTER TABLE "application_stage_transitions" ADD CONSTRAINT "application_stage_transitions_application_id_previous_application_stage_id_unique" unique ("application_id", "previous_application_stage_id");
ALTER TABLE "application_stage_transitions" ADD PRIMARY KEY ("id");

ALTER TABLE "subsidy_stage_transitions" ADD COLUMN "description" varchar(200) NULL;

ALTER TABLE "application_messages" ADD COLUMN "application_stage_transition_id" uuid NULL;
ALTER TABLE "application_messages" ADD CONSTRAINT "application_messages_application_stage_transition_id_foreign" FOREIGN KEY ("application_stage_transition_id") REFERENCES "application_stage_transitions" ("id");
ALTER TABLE "application_messages" ADD CONSTRAINT "application_messages_application_stage_transition_id_unique" unique ("application_stage_transition_id");

UPDATE public.subsidy_stage_transitions SET description = 'Aanvraag ingediend' WHERE id = '7ac879d1-63cb-478d-8745-737313f1643e';
UPDATE public.subsidy_stage_transitions SET description = 'Aanvulling gevraagd' WHERE id = '870bc38a-0d50-40a9-b49e-d56db5ead6b7';
UPDATE public.subsidy_stage_transitions SET description = 'Eerste beoordeling voltooid' WHERE id = 'dd630ec0-50d1-45f5-b014-415e6359389e';
UPDATE public.subsidy_stage_transitions SET description = 'Tweede beoordeling oneens met eerste beoordeling' WHERE id = 'c33b8459-3a98-4906-9ce0-c6f9c0ae7a49';
UPDATE public.subsidy_stage_transitions SET description = 'Tweede beoordeling eens met afkeuring eerste beoordeling' WHERE id = 'c2080b04-1389-42d1-9aca-33141f01a3bc';
UPDATE public.subsidy_stage_transitions SET description = 'Tweede beoordeling eens met goedkeuring eerste beoordeling' WHERE id = 'd73eacca-7605-4915-9efa-bba7c92c3a46';
UPDATE public.subsidy_stage_transitions SET description = 'Interne controle oneens met beoordeling' WHERE id = '005a5acb-a908-44d2-8b69-a50d5ef43870';
UPDATE public.subsidy_stage_transitions SET description = 'Interne controle eens met beoordeling' WHERE id = '3286f4cf-87ae-4cfc-9c1d-523b2ec6745a';
UPDATE public.subsidy_stage_transitions SET description = 'Aanvraag afgekeurd' WHERE id = '963a5afa-6990-4ea9-b097-91999c863d6c';
UPDATE public.subsidy_stage_transitions SET description = 'Aanvraag goedgekeurd' WHERE id = 'a27195df-9825-4d18-acce-9b3492221d8a';

ALTER TABLE "subsidy_stage_transitions" ALTER COLUMN "description" SET NOT NULL ;

INSERT INTO application_stage_transitions (
    id, application_id, subsidy_stage_transition_id, previous_application_stage_id,
    new_application_stage_id, previous_application_status, new_application_status, created_at
)
SELECT
    uuid_in(overlay(overlay(md5(random()::text || ':' || random()::text) placing '4' from 13) placing to_hex(floor(random()*(11-8+1) + 8)::int)::text from 17)::cstring),
    s.application_id,
    t.id,
    ps.id,
    s.id,
    CASE
        WHEN ps.sequence_number = 1 THEN 'draft'
        WHEN ps.subsidy_stage_id = '7e5d64e9-35f0-4fee-b8d2-dca967b43183' THEN 'requestForChanges'
        ELSE 'submitted'
        END,
    CASE
        WHEN t.target_application_status IS NOT NULL THEN t.target_application_status
        ELSE 'submitted'
        END,
    s.created_at
FROM application_stages s, application_stages ps, subsidy_stage_transitions t
WHERE ps.application_id = s.application_id
  AND ps.sequence_number = s.sequence_number - 1
  AND t.target_subsidy_stage_id = s.subsidy_stage_id
  AND t.current_subsidy_stage_id = ps.subsidy_stage_id;


UPDATE application_messages m
SET application_stage_transition_id = (
    SELECT t.id
    FROM application_stage_transitions t
    WHERE t.created_at <= m.sent_at
      AND t.application_id = m.application_id
    ORDER BY t.created_at DESC
    LIMIT 1
)
WHERE m.application_stage_transition_id IS NULL;

ALTER TABLE "application_messages" ALTER COLUMN "application_stage_transition_id" SET NOT NULL;

ALTER TABLE "subsidy_stages" ADD COLUMN "internal_note_field_code" varchar(255) NULL;

UPDATE subsidy_stages SET internal_note_field_code = 'firstAssessmentInternalNote' WHERE id = '8027c102-93ef-4735-ab66-97aa63b836eb';
UPDATE subsidy_stages SET internal_note_field_code = 'secondAssessmentInternalNote' WHERE id = '61436439-e337-4986-bc18-57138e2fab65';
UPDATE subsidy_stages SET internal_note_field_code = 'internalAssessmentInternalNote' WHERE id = '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68';
UPDATE subsidy_stages SET internal_note_field_code = 'coordinatorImplementationInternalNote' WHERE id = '85ed726e-cdbe-444e-8d12-c56f9bed2621';
