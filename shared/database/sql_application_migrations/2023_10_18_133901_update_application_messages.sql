BEGIN;

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

ALTER TABLE "application_messages" ALTER COLUMN "application_stage_transition_id" SET NOT NULL ;

COMMIT;
