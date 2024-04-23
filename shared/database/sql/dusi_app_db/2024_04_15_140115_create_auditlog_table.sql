CREATE TABLE public.audit_logs
(
    email              character varying(320),
    context            json,
    pii_context        text,
    created_at         timestamp(0) without time zone,
    event_code         character varying(255),
    action_code        character varying(255),
    source             character varying(255),
    allowed_admin_view boolean,
    failed             boolean,
    failed_reason      text
);

ALTER TABLE public.audit_logs OWNER TO postgres;

GRANT SELECT, INSERT ON TABLE public.audit_logs TO "backend_dusi";
