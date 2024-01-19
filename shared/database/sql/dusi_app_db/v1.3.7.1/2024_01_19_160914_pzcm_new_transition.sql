INSERT INTO subsidy_stage_transitions (
    id, description, current_subsidy_stage_id, target_subsidy_stage_id, target_application_status,
    condition, send_message, assign_to_previous_assessor, clone_data
)
VALUES (
    '5b8b3dcb-717d-4ff0-9698-800120285298',
    'Geen aanvulling ingediend binnen gestelde termijn',
    '7e5d64e9-35f0-4fee-b8d2-dca967b43183',
    '8027c102-93ef-4735-ab66-97aa63b836eb',
    'submitted',
    '{"type":"comparison","stage":1,"fieldCode":"someFieldThatDoesNotExist","operator":"===","value":"someValueThatCanNeverMatch"}',
    false,
    true,
    true
);
