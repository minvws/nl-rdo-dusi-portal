BEGIN;

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

COMMIT;
