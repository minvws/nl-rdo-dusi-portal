BEGIN;

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

COMMIT;
