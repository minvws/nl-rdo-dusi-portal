--
-- PostgreSQL database dump
--

-- Dumped from database version 12.16 (Debian 12.16-1.pgdg120+1)
-- Dumped by pg_dump version 12.16 (Debian 12.16-1.pgdg120+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: answers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.answers (
    id uuid NOT NULL,
    field_id uuid NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    encrypted_answer text NOT NULL,
    application_stage_id uuid NOT NULL
);


ALTER TABLE public.answers OWNER TO postgres;

--
-- Name: application_hashes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.application_hashes (
    subsidy_stage_hash_id uuid NOT NULL,
    application_id uuid NOT NULL,
    hash character varying(255) NOT NULL
);


ALTER TABLE public.application_hashes OWNER TO postgres;

--
-- Name: application_messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.application_messages (
    id uuid NOT NULL,
    application_id uuid NOT NULL,
    subject character varying(200) NOT NULL,
    is_new boolean NOT NULL,
    sent_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    seen_at timestamp(0) without time zone,
    html_path character varying(200) NOT NULL,
    pdf_path character varying(200) NOT NULL
);


ALTER TABLE public.application_messages OWNER TO postgres;

--
-- Name: application_stages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.application_stages (
    id uuid NOT NULL,
    application_id uuid NOT NULL,
    sequence_number integer NOT NULL,
    is_current boolean NOT NULL,
    subsidy_stage_id uuid NOT NULL,
    assessor_user_id uuid,
    assessor_decision character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    encrypted_key json NOT NULL,
    submitted_at timestamp(0) without time zone,
    is_submitted boolean DEFAULT false NOT NULL,
    CONSTRAINT application_stages_assessor_decision_check CHECK (((assessor_decision)::text = ANY ((ARRAY['approved'::character varying, 'rejected'::character varying, 'requestForChanges'::character varying])::text[])))
);


ALTER TABLE public.application_stages OWNER TO postgres;

--
-- Name: applications; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.applications (
    id uuid NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    subsidy_version_id uuid NOT NULL,
    locked_from timestamp(0) without time zone,
    judgement character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    updated_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    application_title character varying(255) NOT NULL,
    final_review_deadline timestamp(0) without time zone,
    reference character varying(15) NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    identity_id uuid NOT NULL,
    submitted_at timestamp(0) without time zone,
    CONSTRAINT applications_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'submitted'::character varying, 'approved'::character varying, 'rejected'::character varying, 'requestForChanges'::character varying])::text[])))
);


ALTER TABLE public.applications OWNER TO postgres;

--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO postgres;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: fields; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.fields (
    id uuid NOT NULL,
    title character varying(500) NOT NULL,
    description text,
    type character varying(255) NOT NULL,
    params text,
    is_required boolean NOT NULL,
    code character varying(100) NOT NULL,
    source character varying(255) DEFAULT 'user'::character varying NOT NULL,
    subsidy_stage_id uuid NOT NULL,
    CONSTRAINT fields_source_check CHECK (((source)::text = 'user'::text)),
    CONSTRAINT fields_type_check CHECK (((type)::text = ANY ((ARRAY['text'::character varying, 'text:numeric'::character varying, 'text:email'::character varying, 'text:tel'::character varying, 'text:url'::character varying, 'checkbox'::character varying, 'date'::character varying, 'multiselect'::character varying, 'select'::character varying, 'textarea'::character varying, 'upload'::character varying, 'custom:postalcode'::character varying, 'custom:country'::character varying, 'custom:bankaccount'::character varying])::text[])))
);


ALTER TABLE public.fields OWNER TO postgres;

--
-- Name: identities; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.identities (
    id uuid NOT NULL,
    type character varying(255) NOT NULL,
    hashed_identifier character varying(64) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    encrypted_identifier json NOT NULL,
    CONSTRAINT identities_type_check CHECK (((type)::text = 'citizenServiceNumber'::text))
);


ALTER TABLE public.identities OWNER TO postgres;

--
-- Name: migrations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO postgres;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: subsidies; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidies (
    id uuid NOT NULL,
    title character varying(100) NOT NULL,
    description text,
    valid_from date NOT NULL,
    valid_to date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    code character varying(50) NOT NULL,
    reference_prefix character varying(6) NOT NULL
);


ALTER TABLE public.subsidies OWNER TO postgres;

--
-- Name: subsidy_stage_hash_fields; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_stage_hash_fields (
    subsidy_stage_hash_id uuid NOT NULL,
    field_id uuid NOT NULL
);


ALTER TABLE public.subsidy_stage_hash_fields OWNER TO postgres;

--
-- Name: subsidy_stage_hashes; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_stage_hashes (
    id uuid NOT NULL,
    subsidy_stage_id uuid NOT NULL,
    description character varying(200) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    name character varying(255) NOT NULL
);


ALTER TABLE public.subsidy_stage_hashes OWNER TO postgres;

--
-- Name: subsidy_stage_transition_messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_stage_transition_messages (
    id uuid NOT NULL,
    subsidy_stage_transition_id uuid NOT NULL,
    version smallint NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    subject character varying(200) NOT NULL,
    content_html text NOT NULL,
    content_pdf text NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT subsidy_stage_transition_messages_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'published'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.subsidy_stage_transition_messages OWNER TO postgres;

--
-- Name: subsidy_stage_transitions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_stage_transitions (
    id uuid NOT NULL,
    current_subsidy_stage_id uuid NOT NULL,
    target_subsidy_stage_id uuid,
    target_application_status character varying(255),
    condition json,
    send_message boolean DEFAULT false NOT NULL,
    clone_data boolean DEFAULT false NOT NULL,
    CONSTRAINT subsidy_stage_transitions_target_application_status_check CHECK (((target_application_status)::text = ANY ((ARRAY['draft'::character varying, 'submitted'::character varying, 'approved'::character varying, 'rejected'::character varying, 'requestForChanges'::character varying])::text[])))
);


ALTER TABLE public.subsidy_stage_transitions OWNER TO postgres;

--
-- Name: subsidy_stage_uis; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_stage_uis (
    id uuid NOT NULL,
    subsidy_stage_id uuid NOT NULL,
    version smallint NOT NULL,
    status character varying(255) DEFAULT 'draft'::character varying NOT NULL,
    input_ui json NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    view_ui json NOT NULL,
    CONSTRAINT form_uis_status_check CHECK (((status)::text = ANY ((ARRAY['draft'::character varying, 'published'::character varying, 'archived'::character varying])::text[])))
);


ALTER TABLE public.subsidy_stage_uis OWNER TO postgres;

--
-- Name: subsidy_stages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_stages (
    id uuid NOT NULL,
    created_at timestamp(0) without time zone,
    subsidy_version_id uuid NOT NULL,
    title character varying(255) NOT NULL,
    subject_role character varying(255) NOT NULL,
    subject_organisation character varying(255),
    stage integer NOT NULL
);


ALTER TABLE public.subsidy_stages OWNER TO postgres;

--
-- Name: subsidy_versions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.subsidy_versions (
    id uuid NOT NULL,
    created_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    subsidy_id uuid NOT NULL,
    version smallint NOT NULL,
    status character varying(255) NOT NULL,
    subsidy_page_url character varying(255) NOT NULL,
    contact_mail_address character varying(255) NOT NULL,
    mail_to_name_field_identifier character varying(255) NOT NULL,
    mail_to_address_field_identifier character varying(255) NOT NULL,
    review_period integer,
    review_deadline timestamp(0) without time zone
);


ALTER TABLE public.subsidy_versions OWNER TO postgres;

--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Data for Name: answers; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: application_hashes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: application_messages; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: application_stages; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: applications; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: fields; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.fields VALUES ('45d77117-af48-4b80-bf54-e6fe1be74d3a', 'Voornaam', NULL, 'text', '{"maxLength":null}', true, 'firstName', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('3b417993-570d-4168-918b-8d9e927c8cec', 'Tussenvoegsel', NULL, 'text', '{"maxLength":null}', false, 'infix', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('188ec23e-f541-45f1-8c13-6f115965b405', 'Achternaam', NULL, 'text', '{"maxLength":null}', true, 'lastName', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('617695d3-4987-4c74-a8df-0cf6ad2bb361', 'Geboortedatum', NULL, 'date', 'null', true, 'dateOfBirth', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('e4274cbc-64c8-48d6-ae82-686156862b24', 'Straat', NULL, 'text', '{"maxLength":null}', true, 'street', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('8bdaea28-7edd-4b18-8e99-60faebf65db0', 'Huisnummer', NULL, 'text:numeric', '{"maxLength":null}', true, 'houseNumber', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('e6b1e1ae-b182-40a1-ae0d-ce56612f94a5', 'Huisnummer toevoeging', NULL, 'text', '{"maxLength":10}', false, 'houseNumberSuffix', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('28c0f8e3-8ef6-44f5-9ddc-3866c66e9417', 'Postcode', NULL, 'custom:postalcode', 'null', false, 'postalCode', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('d433bbea-63b6-48fb-a8a8-a14c8d1c6419', 'Plaats', NULL, 'text', '{"maxLength":100}', true, 'city', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('ab6c3701-0f41-4952-9e53-960ca8dd550f', 'Land', NULL, 'select', '{"options":["Afghanistan","\u00c5land","Albani\u00eb","Algerije","Amerikaanse Maagdeneilanden","Amerikaans-Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua en Barbuda","Argentini\u00eb","Armeni\u00eb","Aruba","Australi\u00eb","Azerbeidzjan","Bahama\u2019s","Bahrein","Bangladesh","Barbados","Belgi\u00eb","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosni\u00eb en Herzegovina","Botswana","Bouveteiland","Brazili\u00eb","Britse Maagdeneilanden","Brits Indische Oceaanterritorium","Brunei","Bulgarije","Burkina Faso","Burundi","Cambodja","Canada","Centraal-Afrikaanse Republiek","Chili","China","Christmaseiland","Cocoseilanden","Colombia","Comoren","Congo-Brazzaville","Congo-Kinshasa","Cookeilanden","Costa Rica","Cuba","Cura\u00e7ao","Cyprus","Denemarken","Djibouti","Dominica","Dominicaanse Republiek","Duitsland","Ecuador","Egypte","El Salvador","Equatoriaal-Guinea","Eritrea","Estland","Ethiopi\u00eb","Faer\u00f6er","Falklandeilanden","Fiji","Filipijnen","Finland","Frankrijk","Franse Zuidelijke en Antarctische Gebieden","Frans-Guyana","Frans-Polynesi\u00eb","Gabon","Gambia","Georgi\u00eb","Ghana","Gibraltar","Grenada","Griekenland","Groenland","Guadeloupe","Guam","Guatemala","Guernsey","Guinee","Guinee-Bissau","Guyana","Ha\u00efti","Heard en McDonaldeilanden","Honduras","Hongarije","Hongkong","Ierland","IJsland","India","Indonesi\u00eb","Irak","Iran","Isra\u00ebl","Itali\u00eb","Ivoorkust","Jamaica","Japan","Jemen","Jersey","Jordani\u00eb","Kaaimaneilanden","Kaapverdi\u00eb","Kameroen","Kazachstan","Kenia","Kirgizi\u00eb","Kiribati","Kleine Pacifische eilanden van de Verenigde Staten","Koeweit","Kroati\u00eb","Laos","Lesotho","Letland","Libanon","Liberia","Libi\u00eb","Liechtenstein","Litouwen","Luxemburg","Macau","Madagaskar","Malawi","Maldiven","Maleisi\u00eb","Mali","Malta","Marokko","Marshalleilanden","Martinique","Mauritani\u00eb","Mauritius","Mayotte","Mexico","Micronesia","Moldavi\u00eb","Monaco","Mongoli\u00eb","Montenegro","Montserrat","Mozambique","Myanmar","Namibi\u00eb","Nauru","Nederland","Nepal","Nicaragua","Nieuw-Caledoni\u00eb","Nieuw-Zeeland","Niger","Nigeria","Niue","Noordelijke Marianen","Noord-Korea","Noord-Macedoni\u00eb","code Land","Noorwegen","Norfolk","Oeganda","Oekra\u00efne","Oezbekistan","Oman","Oostenrijk","Oost-Timor","Pakistan","Palau","Palestina","Panama","Papoea-Nieuw-Guinea","Paraguay","Peru","Pitcairneilanden","Polen","Portugal","Puerto Rico","Qatar","R\u00e9union","Roemeni\u00eb","Rusland","Rwanda","Saint-Barth\u00e9lemy","Saint Kitts en Nevis","Saint Lucia","Saint-Pierre en Miquelon","Saint Vincent en de Grenadines","Salomonseilanden","Samoa","San Marino","Saoedi-Arabi\u00eb","Sao Tom\u00e9 en Principe","Senegal","Servi\u00eb","Seychellen","Sierra Leone","Singapore","Sint-Helena, Ascension en Tristan da Cunha","Sint-Maarten","Sint Maarten","Sloveni\u00eb","Slowakije","Soedan","Somali\u00eb","Spanje","Spitsbergen en Jan Mayen","Sri Lanka","Suriname","Swaziland","Syri\u00eb","Tadzjikistan","Taiwan","Tanzania","Thailand","Togo","Tokelau","Tonga","Trinidad en Tobago","Tsjaad","Tsjechi\u00eb","Tunesi\u00eb","Turkije","Turkmenistan","Turks- en Caicoseilanden","Tuvalu","Uruguay","Vanuatu","Vaticaanstad","Venezuela","Verenigde Arabische Emiraten","Verenigde Staten","Verenigd Koninkrijk","Vietnam","Wallis en Futuna","Westelijke Sahara","Wit-Rusland","Zambia","Zimbabwe","Zuid-Afrika","Zuid-Georgia en de Zuidelijke Sandwicheilanden","Zuid-Korea","Zuid-Soedan","Zweden","Zwitserland"],"default":"Nederland"}', true, 'country', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('c451d50c-9e94-4308-88ed-c9806062be52', 'Telefoonnummer', NULL, 'text:tel', '{"maxLength":20}', false, 'phoneNumber', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('3abd237f-21db-4ee0-8f67-17a37afc7941', 'E-mailadres', NULL, 'text:email', '{"maxLength":300}', false, 'email', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('25f4680e-29e6-4525-a95e-0d6a482e40f4', 'IBAN', NULL, 'custom:bankaccount', 'null', true, 'bankAccountNumber', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('53fe06ad-a4d3-4548-ad00-cab295ddd4e9', 'Naam rekeninghouder', NULL, 'text', '{"maxLength":50}', true, 'bankAccountHolder', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('2d0039dc-a3c2-41bb-8ce8-fabe6eccfd73', 'Kopie bankafschrift', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":5242880}', true, 'bankStatement', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('0a9440d6-5993-41c0-8db4-33ccaeb0de5d', 'Uittreksel bevolkingsregister niet ouder dan 3 maanden', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":5242880}', true, 'extractPersonalRecordsDatabase', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('0b4411a3-ae00-4aa3-b87c-76619c3258db', 'Verklaring behandeltraject', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":5242880}', true, 'proofOfMedicalTreatment', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('91aa0836-3b51-4d47-93bb-e46ee2cb9366', 'Verklaring type behandeling', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":5242880}', true, 'proofOfTypeOfMedicalTreatment', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('90bd7c86-4ce8-4207-9fd2-f4ddc026c7b6', 'Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze subsidieaanvraag. Ik verklaar het formulier naar waarheid te hebben ingevuld.', NULL, 'checkbox', 'null', true, 'permissionToProcessPersonalData', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('3bfc5805-b24d-4b0c-9c6b-7c9f707cc92e', '', NULL, 'checkbox', 'null', true, 'truthfullyCompleted', 'user', '721c1c28-e674-415f-b1c3-872a631ed045');
INSERT INTO public.fields VALUES ('8d0ba2d7-2a62-45f6-a975-1120c7295057', 'Gecontroleerd', NULL, 'multiselect', '{"options":["Uittreksel van het BRP is opgestuurd","De aanvrager is een ingezetene (> 4 maanden) in Nederland","de aanvrager is ouder dan 18 jaar","De verklaring van de arts over het behandeltraject is opgestuurd","De verklaring van de arts over het behandeltraject is minder dan 2 maanden oud","De verklaring van de arts over het behandeltraject is ondertekend en voorzien van een naamstempel","Het opgegeven BIG-nummer komt overeen met het BIG-register","De operatie heeft nog niet plaatsgevonden","De aanvrager heeft genderdysforie","De aanvrager heeft minimaal een jaar voor de aanvraag hormoonbehandeling ondergaan, of is hiermee vanwege medische redenen gestopt of kon deze om medische redenen niet ondergaan","De verklaring van de arts met de vermelding van de type behandeling is opgestuurd","De verklaring van de arts met de vermelding van de type behandeling is ondertekend en voorzien van een naamstempel","De type behandeling voldoet aan de voorwaarden conform de subsidieregeling","Het IBAN-nummer klopt met het opgegeven IBAN-nummer van de aanvraag","De tenaamstelling op het bankafschrift of bankpas klopt"]}', true, 'checklist', 'user', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c');
INSERT INTO public.fields VALUES ('77791547-06bf-4a25-ace3-8fbf5be05999', 'Bedrag', NULL, 'select', '{"options":["\u20ac 3.830","\u20ac 13.720"],"default":null}', true, 'amount', 'user', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c');
INSERT INTO public.fields VALUES ('0037bbe7-a008-4b6f-8a07-635c89874dc5', 'Beoordeling', NULL, 'select', '{"options":["Onbeoordeeld","Aanvulling nodig","Afgekeurd","Goedgekeurd"],"default":null}', true, 'review', 'user', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c');
INSERT INTO public.fields VALUES ('a2b376ed-84ab-4870-b232-799a2b375a77', 'Voornaam', NULL, 'text', '{"maxLength":null}', true, 'firstName', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('868e59cc-9873-444d-88a4-364520c28b28', 'Tussenvoegsel', NULL, 'text', '{"maxLength":null}', false, 'infix', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('b7d63b9b-76fe-4131-bc52-8b2f4220d18d', 'Achternaam', NULL, 'text', '{"maxLength":null}', true, 'lastName', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('e0a008ed-4001-42df-bbbe-ed722ab9b5bb', 'Geboortedatum', NULL, 'date', 'null', true, 'dateOfBirth', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('48765ed4-2702-48ef-b3ce-e473421c969c', 'Straat', NULL, 'text', '{"maxLength":null}', true, 'street', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('b21afde0-7b73-4f20-87bd-3ed36718f2ae', 'Huisnummer', NULL, 'text:numeric', '{"maxLength":null}', true, 'houseNumber', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('14e7d7ec-5ddb-4e8e-ad17-913ce7b75ced', 'Huisnummer toevoeging', NULL, 'text', '{"maxLength":10}', false, 'houseNumberSuffix', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('51b2e940-4830-4d52-9155-45b3fcd57ba6', 'Postcode', NULL, 'custom:postalcode', 'null', false, 'postalCode', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('7dbe8c51-3047-4a2e-96a0-59b8ecbc9c8a', 'Plaats', NULL, 'text', '{"maxLength":100}', true, 'city', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('2a4358e7-0324-498d-a0e8-dd220d775712', 'Land', NULL, 'select', '{"options":["Afghanistan","\u00c5land","Albani\u00eb","Algerije","Amerikaanse Maagdeneilanden","Amerikaans-Samoa","Andorra","Angola","Anguilla","Antarctica","Antigua en Barbuda","Argentini\u00eb","Armeni\u00eb","Aruba","Australi\u00eb","Azerbeidzjan","Bahama\u2019s","Bahrein","Bangladesh","Barbados","Belgi\u00eb","Belize","Benin","Bermuda","Bhutan","Bolivia","Bosni\u00eb en Herzegovina","Botswana","Bouveteiland","Brazili\u00eb","Britse Maagdeneilanden","Brits Indische Oceaanterritorium","Brunei","Bulgarije","Burkina Faso","Burundi","Cambodja","Canada","Centraal-Afrikaanse Republiek","Chili","China","Christmaseiland","Cocoseilanden","Colombia","Comoren","Congo-Brazzaville","Congo-Kinshasa","Cookeilanden","Costa Rica","Cuba","Cura\u00e7ao","Cyprus","Denemarken","Djibouti","Dominica","Dominicaanse Republiek","Duitsland","Ecuador","Egypte","El Salvador","Equatoriaal-Guinea","Eritrea","Estland","Ethiopi\u00eb","Faer\u00f6er","Falklandeilanden","Fiji","Filipijnen","Finland","Frankrijk","Franse Zuidelijke en Antarctische Gebieden","Frans-Guyana","Frans-Polynesi\u00eb","Gabon","Gambia","Georgi\u00eb","Ghana","Gibraltar","Grenada","Griekenland","Groenland","Guadeloupe","Guam","Guatemala","Guernsey","Guinee","Guinee-Bissau","Guyana","Ha\u00efti","Heard en McDonaldeilanden","Honduras","Hongarije","Hongkong","Ierland","IJsland","India","Indonesi\u00eb","Irak","Iran","Isra\u00ebl","Itali\u00eb","Ivoorkust","Jamaica","Japan","Jemen","Jersey","Jordani\u00eb","Kaaimaneilanden","Kaapverdi\u00eb","Kameroen","Kazachstan","Kenia","Kirgizi\u00eb","Kiribati","Kleine Pacifische eilanden van de Verenigde Staten","Koeweit","Kroati\u00eb","Laos","Lesotho","Letland","Libanon","Liberia","Libi\u00eb","Liechtenstein","Litouwen","Luxemburg","Macau","Madagaskar","Malawi","Maldiven","Maleisi\u00eb","Mali","Malta","Marokko","Marshalleilanden","Martinique","Mauritani\u00eb","Mauritius","Mayotte","Mexico","Micronesia","Moldavi\u00eb","Monaco","Mongoli\u00eb","Montenegro","Montserrat","Mozambique","Myanmar","Namibi\u00eb","Nauru","Nederland","Nepal","Nicaragua","Nieuw-Caledoni\u00eb","Nieuw-Zeeland","Niger","Nigeria","Niue","Noordelijke Marianen","Noord-Korea","Noord-Macedoni\u00eb","code Land","Noorwegen","Norfolk","Oeganda","Oekra\u00efne","Oezbekistan","Oman","Oostenrijk","Oost-Timor","Pakistan","Palau","Palestina","Panama","Papoea-Nieuw-Guinea","Paraguay","Peru","Pitcairneilanden","Polen","Portugal","Puerto Rico","Qatar","R\u00e9union","Roemeni\u00eb","Rusland","Rwanda","Saint-Barth\u00e9lemy","Saint Kitts en Nevis","Saint Lucia","Saint-Pierre en Miquelon","Saint Vincent en de Grenadines","Salomonseilanden","Samoa","San Marino","Saoedi-Arabi\u00eb","Sao Tom\u00e9 en Principe","Senegal","Servi\u00eb","Seychellen","Sierra Leone","Singapore","Sint-Helena, Ascension en Tristan da Cunha","Sint-Maarten","Sint Maarten","Sloveni\u00eb","Slowakije","Soedan","Somali\u00eb","Spanje","Spitsbergen en Jan Mayen","Sri Lanka","Suriname","Swaziland","Syri\u00eb","Tadzjikistan","Taiwan","Tanzania","Thailand","Togo","Tokelau","Tonga","Trinidad en Tobago","Tsjaad","Tsjechi\u00eb","Tunesi\u00eb","Turkije","Turkmenistan","Turks- en Caicoseilanden","Tuvalu","Uruguay","Vanuatu","Vaticaanstad","Venezuela","Verenigde Arabische Emiraten","Verenigde Staten","Verenigd Koninkrijk","Vietnam","Wallis en Futuna","Westelijke Sahara","Wit-Rusland","Zambia","Zimbabwe","Zuid-Afrika","Zuid-Georgia en de Zuidelijke Sandwicheilanden","Zuid-Korea","Zuid-Soedan","Zweden","Zwitserland"],"default":"Nederland"}', true, 'country', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('e731fcf0-6c7c-4f34-82ac-1f49705c69d1', 'Telefoonnummer', NULL, 'text:tel', '{"maxLength":20}', false, 'phoneNumber', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('f9d36799-7b02-4fd1-b432-06dec6997af0', 'E-mailadres', NULL, 'text:email', '{"maxLength":300}', false, 'email', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('6b9db799-9355-4329-809e-2f70933f6ba4', 'IBAN', NULL, 'custom:bankaccount', 'null', true, 'bankAccountNumber', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('0fbb725a-7939-4eb2-a944-77b255f8ced9', 'Naam rekeninghouder', NULL, 'text', '{"maxLength":50}', true, 'bankAccountHolder', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('2bebaac5-7122-4dca-b6f2-4dbe1d0d7b38', 'Gewaarmerkt verzekeringsbericht', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', true, 'certifiedEmploymentDocument', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('ce59c753-a54a-4735-a868-195928559d25', 'WIA-Beslissing', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', true, 'wiaDecisionDocument', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('4995e381-2793-4773-93ce-b4e61ddc3b4b', 'Is WIA beslissing uitgesteld?', NULL, 'select', '{"options":["Ja","Nee"],"default":null}', true, 'isWiaDecisionPostponed', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('9eba743a-93ed-47f0-bd7e-8f02f3b9a3a9', 'Toekenningsbrief', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', false, 'wiaDecisionPostponedLetter', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('f3d8e428-4d77-449f-9551-1672b18d933e', 'Bewijs dienstverband', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', true, 'employmentContract', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('ed15f5ae-13ee-4c9b-8eab-0eba3a44cf4a', 'Functie', NULL, 'select', '{"options":["Ambulancechauffeur","Anesthesiemedewerker en\/of operatieassistent","Arts","Bachelor medisch hulpverlener","Doktersassistent","Helpende","Physician assistant","Praktijkondersteuner huisarts","Verpleegkundig specialist","(gespecialiseerd) Verpleegkundige","Verzorgende in de individuele gezondheidszorg (VIG\u2019er) of verzorgende","Zorgondersteuner en\/of voedingsassistent","Anders"],"default":null}', true, 'employmentFunction', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('f6432fef-ec52-46bb-9549-a1931b3dcf4d', 'Andere functie', NULL, 'text', '{"maxLength":300}', false, 'otherEmploymentFunction', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('fcbfddcb-392c-4dc4-8b4a-877e87e49d9b', 'Werkgever', NULL, 'select', '{"options":["Zorgaanbieder","Andere organisatie"],"default":null}', true, 'employerKind', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('497f4a62-522f-44f6-a9e1-f7eb42860e25', 'Verklaring zorgaanbieder', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', false, 'otherEmployerDeclarationFile', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('1ef80ce4-c441-4ae2-a934-a4835c19f338', 'Bent u werkzaam geweest bij een justitiële inrichting?', NULL, 'select', '{"options":["Ja","Nee"],"default":null}', true, 'hasBeenWorkingAtJudicialInstitution', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('f31fbc35-7e43-41ff-9bb8-d834747665ca', 'BIG-nummer', NULL, 'text', '{"maxLength":11}', false, 'BIGNumberJudicialInstitution', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('76966ecc-68e3-440a-bbda-03784a8ca87c', 'Medisch onderzoeksverslag', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', true, 'socialMedicalAssessment', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('4f4e6617-6ca1-456d-a946-508557f3af77', 'Heeft langdurige post-COVID klachten', NULL, 'select', '{"options":["Ja","Nee"],"default":null}', true, 'hasPostCovidDiagnose', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('f8e0fc5b-6b20-4864-8b2c-fa149486f3c5', 'Verklaring arts', NULL, 'upload', '{"mimeTypes":["image\/*","application\/pdf"],"maxFileSize":20971520}', false, 'doctorsCertificate', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('c2063c67-e4ae-4cbd-8913-0f6d892c278a', '', NULL, 'checkbox', 'null', true, 'truthfullyCompleted', 'user', '7e5d64e9-35f0-4fee-b8d2-dca967b43183');
INSERT INTO public.fields VALUES ('c56bf31f-d7f7-43e1-9ffb-9a1c47105259', 'Controlevragen', NULL, 'select', '{"options":["Vraag 1","Vraag 2","Vraag 3","Vraag 4"],"default":null}', false, 'checklist', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb');
INSERT INTO public.fields VALUES ('48f53245-9978-4887-bbc9-c2154f156ba5', 'Bedrag', NULL, 'select', '{"options":["\u20ac 15.000"],"default":null}', false, 'amount', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb');
INSERT INTO public.fields VALUES ('d8962dfd-87cb-4863-8b3c-127a6f57202a', 'Beoordeling', NULL, 'select', '{"options":["Onbeoordeeld","Aanvulling nodig","Afgekeurd","Goedgekeurd"],"default":null}', true, 'firstAssessment', 'user', '8027c102-93ef-4735-ab66-97aa63b836eb');
INSERT INTO public.fields VALUES ('f4c6cd35-0e99-477b-b1c0-229adb9fbf3f', 'Beoordeling', NULL, 'select', '{"options":["Oneens met de eerste beoordeling","Eens met de eerste beoordeling"],"default":null}', true, 'secondAssessment', 'user', '61436439-e337-4986-bc18-57138e2fab65');
INSERT INTO public.fields VALUES ('5d5ae8ca-ec10-4023-b183-5b92d3e6f48e', 'Beoordeling', NULL, 'select', '{"options":["Onbeoordeeld","Afgekeurd","Goedgekeurd"],"default":null}', true, 'internalAssessment', 'user', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68');
INSERT INTO public.fields VALUES ('85a9fa36-3a22-4b15-80d8-165aaf8b2960', 'Beoordeling', NULL, 'select', '{"options":["Afgekeurd","Goedgekeurd"],"default":null}', true, 'implementationCoordinatorAssessment', 'user', '85ed726e-cdbe-444e-8d12-c56f9bed2621');


--
-- Data for Name: identities; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.migrations VALUES (1, '2023_05_25_083155_initial_application_model', 1);
INSERT INTO public.migrations VALUES (2, '2023_06_01_093009_add_judgements', 1);
INSERT INTO public.migrations VALUES (3, '2023_06_01_222709_add_judgement_to_application', 1);
INSERT INTO public.migrations VALUES (4, '2023_06_12_113904_initial_application_model', 1);
INSERT INTO public.migrations VALUES (5, '2023_06_27_110403_use_field_instead_of_question', 1);
INSERT INTO public.migrations VALUES (6, '2023_06_28_082713_create_failed_jobs_table', 1);
INSERT INTO public.migrations VALUES (7, '2023_07_04_140304_add_form_uis', 1);
INSERT INTO public.migrations VALUES (8, '2023_07_13_123904_modify_application_and_answer_tables', 1);
INSERT INTO public.migrations VALUES (9, '2023_07_18_084103_multistage_applications', 1);
INSERT INTO public.migrations VALUES (10, '2023_07_19_074740_multistage_forms', 1);
INSERT INTO public.migrations VALUES (11, '2023_08_04_111315_subsidy_url', 1);
INSERT INTO public.migrations VALUES (12, '2023_08_09_084758_review_ui', 1);
INSERT INTO public.migrations VALUES (13, '2023_08_10_130252_letter_texts', 1);
INSERT INTO public.migrations VALUES (14, '2023_08_11_134208_add_stage_and_decision_fields', 1);
INSERT INTO public.migrations VALUES (15, '2023_08_14_125410_revision_field_table', 1);
INSERT INTO public.migrations VALUES (16, '2023_08_15_180930_add_pdf_and_view_letter_path_to_stage_version', 1);
INSERT INTO public.migrations VALUES (17, '2023_08_22_233945_add_code_to_subsidy', 1);
INSERT INTO public.migrations VALUES (18, '2023_08_23_123045_add_multiselect_to_field_types', 1);
INSERT INTO public.migrations VALUES (19, '2023_08_23_140512_modify_fields_type', 1);
INSERT INTO public.migrations VALUES (20, '2023_08_28_074345_fk_subsidy', 1);
INSERT INTO public.migrations VALUES (21, '2023_08_31_124140_add_application_reference_fields', 1);
INSERT INTO public.migrations VALUES (22, '2023_08_31_124140_add_subsidy_reference_prefix_field', 1);
INSERT INTO public.migrations VALUES (23, '2023_09_04_150812_create_application_messages_table', 1);
INSERT INTO public.migrations VALUES (24, '2023_09_06_171920_add_application_status', 1);
INSERT INTO public.migrations VALUES (25, '2023_09_06_171922_merge_application_stage_and_version', 1);
INSERT INTO public.migrations VALUES (26, '2023_09_07_102659_add_identity_table', 1);
INSERT INTO public.migrations VALUES (27, '2023_09_07_154920_add_subsidy_versions_review_period', 1);
INSERT INTO public.migrations VALUES (28, '2023_09_11_165512_make_application_messages_seen_at_nullable', 1);
INSERT INTO public.migrations VALUES (29, '2023_09_13_092400_add_encrypted_key_to_application_stages', 1);
INSERT INTO public.migrations VALUES (30, '2023_09_13_215500_update_encrypted_identifier_in_identity_table', 1);
INSERT INTO public.migrations VALUES (31, '2023_09_19_092751_add_subsidy_stage_transitions_table', 1);
INSERT INTO public.migrations VALUES (32, '2023_09_19_100710_misc_cleanup', 1);
INSERT INTO public.migrations VALUES (33, '2023_09_20_133700_update_field_params', 1);
INSERT INTO public.migrations VALUES (34, '2023_09_21_102450_modify_subsidy_stage_transitions_table', 1);
INSERT INTO public.migrations VALUES (35, '2023_09_21_150923_modify_subsidy_version_table', 1);


--
-- Data for Name: subsidies; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.subsidies VALUES ('00f26400-7232-475f-922c-6b569b7e421a', 'Borstprothesen transvrouwen', 'Transvrouwen zijn man-vrouw transgenders die negatieve gevoelens (''genderdysforie'') ervaren omdat ze als man geboren zijn en in transitie zijn om als vrouw te leven. De meerderheid van de transvrouwen vindt, ook na behandeling (de zogeheten genderbevestigende hormonale therapie), dat zij te weinig borstweefsel heeft voor een vrouwelijk profiel. Dit kan een grote hindernis zijn bij de transitie. Een borstvergroting kan deze hinder verminderen.', '2019-02-01', NULL, NULL, NULL, 'BTV', 'BTV23');
INSERT INTO public.subsidies VALUES ('06a6b91c-d59b-401e-a5bf-4bf9262d85f8', 'Aanvraagformulier financiële ondersteuning Zorgmedewerkers met langdurige post-COVID klachten', 'De regeling Zorgmedewerkers met langdurige post-COVID klachten richt zich op zorgmedewerkers die tijdens de eerste golf van de COVID-19 pandemie besmet zijn geraakt met COVID-19 en sindsdien langdurige post-COVID klachten hebben. Deze klachten hebben grote invloed op het werk en het privéleven van deze zorgmedewerkers. Zij kunnen soms hun eigen werk als zorgmedewerker niet meer (volledig) doen. Voor deze specifieke groep zorgmedewerkers is een eenmalige financiële ondersteuning van €15.000 beschikbaar.', '2023-09-01', NULL, NULL, NULL, 'PCZM', 'PCZM23');
INSERT INTO public.subsidies VALUES ('a320abc3-6913-4da8-a803-6cf49b2b25e5', 'quaerat libero in', 'Quasi voluptatem quasi in beatae dolorem qui cum. Consequuntur impedit quidem non natus accusantium vel totam voluptatem. Dolorem magnam cum porro.', '2023-09-29', '2023-04-16', '2023-09-28 20:13:05', '2023-09-28 20:13:05', 'UKW', 'OSTX17');
INSERT INTO public.subsidies VALUES ('3f0b3cdc-937f-4de3-bb89-3f84ee31221a', 'est sint tempora', 'Et odio aliquam a officiis. Dolorem in dolorum earum voluptatem id. Consequatur sint quasi qui sapiente. Fugit dicta soluta cumque odio fugit qui aliquid.', '2024-06-22', '2022-10-30', '2023-09-28 20:13:05', '2023-09-28 20:13:05', 'XMZ', 'NEKC29');


--
-- Data for Name: subsidy_stage_hash_fields; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: subsidy_stage_hashes; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: subsidy_stage_transition_messages; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.subsidy_stage_transition_messages VALUES ('85bf054e-c6e3-42d2-880d-07c29d0fe6bf', '870bc38a-0d50-40a9-b49e-d56db5ead6b7', 1, 'published', 'Aanvulling nodig', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}
', '{layout ''letter_layout.latte''}
{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
', '2023-09-28 20:13:05', NULL);
INSERT INTO public.subsidy_stage_transition_messages VALUES ('64a636d8-ed0c-4bb6-982e-f948c68755b6', 'c2080b04-1389-42d1-9aca-33141f01a3bc', 1, 'published', 'Aanvraag afgekeurd', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}
', '{layout ''letter_layout.latte''}
{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
', '2023-09-28 20:13:05', NULL);
INSERT INTO public.subsidy_stage_transition_messages VALUES ('7da32b2f-4f0d-44ab-bc87-07718db4bfd5', '963a5afa-6990-4ea9-b097-91999c863d6c', 1, 'published', 'Aanvraag afgekeurd', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}
', '{layout ''letter_layout.latte''}
{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
', '2023-09-28 20:13:05', NULL);
INSERT INTO public.subsidy_stage_transition_messages VALUES ('9c2ad81e-cf52-41a3-966f-fc9757de15c9', 'a27195df-9825-4d18-acce-9b3492221d8a', 1, 'published', 'Aanvraag goedgekeurd', '{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}
', '{layout ''letter_layout.latte''}
{block content}
    <p>Beste lezer,</p>
    <p>
        Op {$content->createdAt|date:"d-m-Y"} heeft u een aanvraag ingediend voor de regeling {$content->subsidyTitle} met referentienummer {$content->reference}.

        {if $content->decision === ''approved''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''rejected''}
            Met deze brief beslis ik op uw aanvraag.
        {elseif $content->decision === ''discontinued''}
            Op (*DatumIncompleetbrief* of *DatumVraagbrief*) heb ik u geïnformeerd dat ik uw aanvraag niet in behandeling kan nemen.
        {/if}
    </p>

    {if $content->decision === ''requestForChanges''}
        <p>Uw aanvraag bevat helaas nog onvoldoende gegevens om een beslissing te kunnen nemen over afhandeling.</p>
    {elseif $content->decision === ''question''}
        <p>Om de aanvraag goed te kunnen beoordelen ontvang ik graag van u de volgende aanvullende informatie:</p>
        <p>*Motivatie*</p>
    {elseif $content->decision === ''discontinued''}
        <p>Ik heb u daarom verzocht uw aanvraag vóór (*DatumIncompleetbrief+14* dagen of *DatumVraagbrief+14*) dagen aan te vullen.</p>
    {/if}
    <p>&nbsp;</p>

    {if $content->decision === ''approved'' || $content->decision === ''rejected'' || $content->decision === ''discontinued''}
        <h2>Besluit</h2>
        {if $content->decision === ''approved''}
            <p>Hierbij verleen ik u de financiële tegemoetkoming/subsidie van &euro;{$content->appointedSubsidy}.</p>
        {elseif $content->decision === ''rejected''}
            <p>Uw aanvraag moet ik helaas afwijzen. De reden(en) licht ik hieronder toe.</p>
        {elseif $content->decision === ''discontinued''}
            <p>Aangezien ik de ontbrekende gegevens niet op tijd van u heb ontvangen, laat ik uw aanvraag buiten behandeling.</p>
        {/if}
        <p>&nbsp;</p>
    {/if}

    {if ($content->decision === ''approved'' || $content->decision === ''rejected'') && $content->motivation}
        <h2>Motivering bij het besluit</h2>
        {if $content->decision === ''approved''}
            <p>Uw aanvraag wordt verleend vanwege de volgende reden(en):</p>
        {/if}
        {if $content->decision === ''rejected''}
            <p>Uw aanvraag wordt afgewezen vanwege de volgende reden(en):</p>
        {/if}
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' && $content->motivation}
        <h2>Wat moet u aanvullen?</h2>
        <p>{$content->motivation}</p>
        <p>&nbsp;</p>
    {/if}

    {if $content->decision === ''requestForChanges'' || $content->decision === ''question''}
        <h2>Termijn</h2>
        <p>Ik ontvang de informatie graag binnen 2 weken na dagtekening van deze brief, dus uiterlijk *DocumentDatum+14dagen*. U kunt deze toevoegen aan uw aanvraag door het door u ingevulde aanvraagformulier te heropenen in Mijn DUS-I.</p>
        <p>Als de gevraagde gegevens niet binnen de gestelde termijn van 2 weken door mij zijn ontvangne, of onvoldoende zijn voor de beoordeling, kan ik niet op tijd een besluit nemen op uw aanvraag.</p>
        <p>&nbsp;</p>
    {/if}
{/block}

{block sidebar}
    {include parent}
{/block}
', '2023-09-28 20:13:05', NULL);


--
-- Data for Name: subsidy_stage_transitions; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.subsidy_stage_transitions VALUES ('7ac879d1-63cb-478d-8745-737313f1643e', '7e5d64e9-35f0-4fee-b8d2-dca967b43183', '8027c102-93ef-4735-ab66-97aa63b836eb', 'submitted', NULL, false, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('dd630ec0-50d1-45f5-b014-415e6359389e', '8027c102-93ef-4735-ab66-97aa63b836eb', '61436439-e337-4986-bc18-57138e2fab65', NULL, NULL, false, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('c33b8459-3a98-4906-9ce0-c6f9c0ae7a49', '61436439-e337-4986-bc18-57138e2fab65', '8027c102-93ef-4735-ab66-97aa63b836eb', NULL, '{"type":"comparison","stage":3,"fieldCode":"secondAssessment","operator":"===","value":"Oneens met de eerste beoordeling"}', false, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('870bc38a-0d50-40a9-b49e-d56db5ead6b7', '61436439-e337-4986-bc18-57138e2fab65', '7e5d64e9-35f0-4fee-b8d2-dca967b43183', 'requestForChanges', '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Aanvulling nodig"},{"type":"comparison","stage":3,"fieldCode":"secondAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}', true, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('d73eacca-7605-4915-9efa-bba7c92c3a46', '61436439-e337-4986-bc18-57138e2fab65', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', NULL, '{"type":"and","conditions":[{"type":"in","stage":2,"fieldCode":"firstAssessment","values":["Goedgekeurd","Afgekeurd"]},{"type":"comparison","stage":3,"fieldCode":"secondAssessment","operator":"===","value":"Eens met de eerste beoordeling"}]}', false, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('c2080b04-1389-42d1-9aca-33141f01a3bc', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', NULL, 'rejected', '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Afgekeurd"},{"type":"comparison","stage":3,"fieldCode":"internalAssessment","operator":"===","value":"Afgekeurd"}]}', true, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('005a5acb-a908-44d2-8b69-a50d5ef43870', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', '8027c102-93ef-4735-ab66-97aa63b836eb', NULL, '{"type":"or","conditions":[{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Afgekeurd"},{"type":"comparison","stage":3,"fieldCode":"internalAssessment","operator":"===","value":"Goedgekeurd"}]},{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Goedgekeurd"},{"type":"comparison","stage":3,"fieldCode":"internalAssessment","operator":"===","value":"Afgekeurd"}]}]}', false, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('3286f4cf-87ae-4cfc-9c1d-523b2ec6745a', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', '85ed726e-cdbe-444e-8d12-c56f9bed2621', NULL, '{"type":"and","conditions":[{"type":"comparison","stage":2,"fieldCode":"firstAssessment","operator":"===","value":"Goedgekeurd"},{"type":"comparison","stage":4,"fieldCode":"internalAssessment","operator":"===","value":"Goedgekeurd"}]}', false, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('963a5afa-6990-4ea9-b097-91999c863d6c', '85ed726e-cdbe-444e-8d12-c56f9bed2621', NULL, 'rejected', '{"type":"comparison","stage":5,"fieldCode":"implementationCoordinatorAssessment","operator":"===","value":"Afgekeurd"}', true, false);
INSERT INTO public.subsidy_stage_transitions VALUES ('a27195df-9825-4d18-acce-9b3492221d8a', '85ed726e-cdbe-444e-8d12-c56f9bed2621', NULL, 'approved', '{"type":"comparison","stage":5,"fieldCode":"implementationCoordinatorAssessment","operator":"===","value":"Goedgekeurd"}', true, false);


--
-- Data for Name: subsidy_stage_uis; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.subsidy_stage_uis VALUES ('72475863-7987-4375-94d7-21e04ff6552b', '721c1c28-e674-415f-b1c3-872a631ed045', 1, 'published', '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"Start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormHtml","options":{"html":"<p class=\"warning\">\n    <span>Waarschuwing:<\/span>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. Als u meer tijd nodig heeft, dan heeft dit\n    geen consequenties (gegevens blijven behouden). Zorg ervoor dat u alle gevraagde documenten\n    digitaal bij de hand heeft. Dit kan bijvoorbeeld een scan, schermafdruk of foto vanaf uw mobiele\n    telefoon zijn. Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.\n<\/p>\n\n<h2>Introductie<\/h2>\n<p>\n    Bent u een transvrouw met genderdysforie? En bevindt u zich in een medisch transitietraject en\n    overweegt u een borstvergroting? U kunt dan in aanmerking komen voor een subsidie via de\n    subsidieregeling Borstprothesen transvrouwen. Met dit formulier vraagt u een subsidie aan voor een\n    plastisch-chirurgische borstconstructie. Deze behandeling moet bewezen effectief zijn.\n<\/p>\n<h2>Benodigde bestanden<\/h2>\n<p>Om in aanmerking te komen voor de subsidie, worden een aantal documenten aan u gevraagd:<\/p>\n<p><\/p>\n<ol>\n    <li>Uittreksel Basisregistratie Personen;<\/li>\n    <li>Kopie van een recent bankafschrift of bankpas;<\/li>\n    <li>\n        Medische verklaring van uw BIG-geregistreerde arts over de behandeling die u tot nu toe heeft\n        gevolgd. Download het\n        <a\n            title=\"Medische verklaring behandeltraject format\"\n            href=\"https:\/\/www.dus-i.nl\/subsidies\/borstprothesen-transvrouwen\/documenten\/publicaties\/2019\/01\/14\/verklaring-behandelend-arts-borstprothesen-transvrouwen\"\n            target=\"_blank\"\n            rel=\"nofollow noopener external\"\n        >Medische verklaring behandeltraject format<\/a\n        >.\n    <\/li>\n    <li>\n        Medische verklaring van een BIG-geregistreerde arts over de behandeling (operatie) die zal\n        worden uitgevoerd. Download het\n        <a\n            title=\"Medische verklaring van het type behandeling format\"\n            href=\"https:\/\/www.dus-i.nl\/subsidies\/borstprothesen-transvrouwen\/documenten\/publicaties\/2021\/08\/05\/medische-verklaring-van-het-type-behandeling-borstprothesen-transvrouwen\"\n            target=\"_blank\"\n            rel=\"nofollow noopener external\"\n        >Medische verklaring van het type behandeling format<\/a\n        >.\n    <\/li>\n<\/ol>\n<p>In de laatste stap van het aanvraagformulier kunt u deze uploaden.<\/p>\n\n<h2>Aanvraag invullen<\/h2>\n<p>\n    Het invullen van een aanvraag kost ongeveer 10 minuten. Als u meer tijd nodig heeft, dan heeft dit\n    geen consequenties. Uw reeds ingevulde gegevens blijven behouden.\n<\/p>\n<p>\n    Zorg ervoor dat u alle gevraagde documenten digitaal bij de hand heeft. Dit kan bijvoorbeeld een\n    scan, schermafdruk of foto vanaf uw mobiele telefoon zijn.\n<\/p>\n<p>Laat uw aanvraag niet langer dan noodzakelijk open staan op uw computer.<\/p>\n<h2>Aanvraag starten<\/h2>\n"}},{"type":"FormGroupControl","label":"Toestemming","options":{"section":true,"group":true},"elements":[{"type":"CustomControl","scope":"#\/properties\/permissionToProcessPersonalData","label":"Verwerking","options":{"description":"Ik geef toestemming voor het verwerken van mijn persoonsgegevens voor deze aanvraag."}}]}]}],"options":{"required":["permissionToProcessPersonalData"]}},{"type":"CustomPageControl","label":"Start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","elements":[{"type":"FormNotification","options":{"displayAs":"explanation","message":"U moet ouder zijn dan 18 jaar (artikel 4 van de regeling)."}}]},{"type":"Group","label":"Persoonlijke informatie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName","label":"Voornaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/infix","label":"Tussenvoegsel","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/lastName","label":"Achternaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/dateOfBirth","label":"Geboortedatum","options":{"placeholder":""}}]}]},{"type":"Group","label":"Adres","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/street","label":"Straatnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumber","label":"Huisnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumberSuffix","label":"Huisnummer toevoeging","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/postalCode","label":"Postcode","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/city","label":"Plaatsnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/country","label":"Land","options":{"format":"select","placeholder":"Selecteer een land"}}]}]},{"type":"Group","label":"Contact","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/phoneNumber","label":"Telefoonnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/email","label":"E-mailadres","options":{"placeholder":"","tip":"U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door."}}]}]},{"type":"Group","label":"Bank","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/bankAccountHolder","label":"Naam rekeninghouder","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/bankAccountNumber","label":"IBAN","options":{"placeholder":"","tip":"Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening."}}]}]}]}],"options":{"required":["firstName","lastName","street","dateOfBirth","houseNumber","postalCode","city","country","phoneNumber","email","bankAccountHolder","bankAccountNumber"]}},{"type":"CustomPageControl","label":"Start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Bankafschrift of bankpas","elements":[{"type":"VerticalLayout","elements":[{"type":"FormResultsTable","label":"Uw bankgegegvens","options":{"fields":{"Naam rekeninghouder":"{bankAccountHolder}","IBAN":"{bankAccountNumber}"}}},{"type":"CustomControl","scope":"#\/properties\/bankStatement","label":"Kopie bankafschrift","options":{"accept":"image\/*,.pdf","maxFileSize":5242880,"tip":"Op de kopie van een recent bankafschrift moeten het rekeningnummer en uw naam zichtbaar zijn. Adres en datum mogen ook, maar zijn niet verplicht. Maak de andere gegevens onleesbaar. U mag ook een afdruk van internet bankieren gebruiken of een kopie van uw bankpas. Zie ook dit <a title=\"voorbeeld\" href=\"#\" target=\"_blank\" rel=\"noopener\" class=\"external\">voorbeeld<\/a>. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]},{"type":"Group","label":"Uittreksel bevolkingsregister","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/extractPersonalRecordsDatabase","label":"Uittreksel bevolkingsregister","options":{"accept":"image\/*,.pdf","maxFileSize":5242880,"tip":"U kunt een uittreksel uit het bevolkingsregister (de Basisregistratie personen) opvragen bij de gemeente waar u staat ingeschreven. Dit document bevat uw naam, geboortedatum en adres. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]},{"type":"Group","label":"Medische verklaring behandeltraject","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfMedicalTreatment","label":"Verklaring behandeltraject","options":{"accept":"image\/*,.pdf","maxFileSize":5242880,"tip":"De <a title=\"medische verklaring over het behandeltraject\" href=\"#\" target=\"_blank\" rel=\"noopener\" class=\"external\">medische verklaring over het behandeltraject<\/a> dat u tot nu toe heeft gevolgd moet zijn ingevuld door de BIG-geregistreerde arts waar u in behandeling bent. Dit kan een huisarts of medisch specialist zijn die de hormonen voorschrijft en de behandeling begeleidt. De verklaring mag niet ouder zijn dan twee maanden. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]},{"type":"Group","label":"Medische verklaring van het type behandeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/proofOfTypeOfMedicalTreatment","label":"Verklaring type behandeling","options":{"accept":"image\/*,.pdf","maxFileSize":5242880,"tip":"De <a title=\"medische verklaring van het type behandeling\" href=\"#\" target=\"_blank\" rel=\"noopener\" class=\"external\">medische verklaring van het type behandeling<\/a> (operatie) dat zal worden uitgevoerd moet zijn ingevuld en ondertekend door een BIG-geregistreerde arts. Toegestane bestandstypen: pdf, jpg, jpeg, png, Maximale bestandsgrootte: 5 MB."}}]}]}]}],"options":{"required":["bankStatement","extractPersonalRecordsDatabase","proofOfMedicalTreatment","proofOfTypeOfMedicalTreatment"]}},{"type":"CustomPageControl","label":"Start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Controleren","elements":[{"type":"FormResultsTable","label":"Uw bankgegevens","options":{"fields":{"Indiener":"{firstName} {infix} {lastName}","Adres":"{streetName} {houseNumber}{houseNumberSuffix} {postalCode} {city}","Telefoon":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"Group","label":"Ondertekenen","elements":[{"type":"CustomControl","scope":"#\/properties\/truthfullyCompleted","label":"Inhoud","options":{"description":"Ik verklaar het formulier naar waarheid te hebben ingevuld."}}]}]}],"options":{"required":["truthfullyCompleted"]}}]}', NULL, NULL, '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Metagegevens","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Dossiernummer":"{assessmentId}","Aangevraagd op":"{validFrom}","Uiterste behandeldatum":"{validTo}"}}}]},{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}"}}}]},{"type":"FormGroupControl","label":"Bestanden","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Kopie bankafschrift":"{bankStatement}","Uitreksel bevolkingsregister":"{extractPersonalRecordsDatabase}","Verklaring behandeltraject":"{proofOfMedicalTreatment}","Verklaring type behandeling":"{proofOfTypeOfMedicalTreatment}"}}}]}]}');
INSERT INTO public.subsidy_stage_uis VALUES ('c2365ef6-5ff9-469f-ab4a-5533c33b299d', '6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/checklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/review","options":{"format":"radio"}}]}]}]}', NULL, NULL, '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"Start","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Contactgegevens aanvrager","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/formOfAddress","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstName"},{"type":"CustomControl","scope":"#\/properties\/infix"},{"type":"CustomControl","scope":"#\/properties\/lastName"},{"type":"CustomControl","scope":"#\/properties\/street"},{"type":"CustomControl","scope":"#\/properties\/houseNumber"}]}]}],"options":{"required":["firstname"]}}]}');
INSERT INTO public.subsidy_stage_uis VALUES ('e6d5cd35-8c67-40c4-abc4-b1d6bf8afb97', '7e5d64e9-35f0-4fee-b8d2-dca967b43183', 1, 'published', '{"type":"CustomPageNavigationControl","elements":[{"type":"CustomPageControl","label":"Start","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormHtml","options":{"html":"<h2>Introductie<\/h2>\n<p>\n    Met dit formulier vraagt u een financi\u00eble ondersteuning aan via de regeling Zorgmedewerkers met langdurige post-COVID klachten. U kunt de financi\u00eble ondersteuning aanvragen als u voldoet aan de voorwaarden uit de regeling. De financi\u00eble ondersteuning bedraagt \u20ac15.000 per persoon.\n<\/p>\n<h2>Benodigde bestanden<\/h2>\n<p>Om in aanmerking te komen voor de financi\u00eble ondersteuning, vragen we u om een aantal documenten:<\/p>\n<p><\/p>\n<ol>\n    <li>Gewaarmerkt verzekeringsbericht UWV<\/li>\n    <li>Kopie WIA-beslissing<\/li>\n    <li>Bewijs van uw dienstverband<\/li>\n    <li>Medisch onderzoeksverslag \/ medische rapportage<\/li>\n<\/ol>\n\n<p>In de laatste stap van het aanvraagformulier kunt u de gevraagde documenten uploaden. In deze stap tonen we ook of mogelijk extra gegevens en\/of bewijsstukken noodzakelijk zijn voor de beoordeling van uw aanvraag. Let op: als we u om extra documenten vragen, gebruik dan altijd de <a\n    title=\"Documenten over Zorgmedewerkers met langdurige post-COVID klachten\"\n    href=\"https:\/\/www.dus-i.nl\/subsidies\/zorgmedewerkers-met-langdurige-post-covid-klachten\/documenten\"\n    target=\"_blank\"\n    rel=\"nofollow noopener external\"\n>verplichte formats<\/a\n>.<\/p>\n\n<h2>Meer informatie<\/h2>\n\n<p>Bekijk voor meer informatie over de regeling, de voorwaarden en de aanvraagprocedure onze <a href=\"https:\/\/www.dus-i.nl\/post-covid\">website<\/a>. Heeft u een vraag? Gebruik dan het <a\n    title=\"Contactformulier DUS-i post-COVID\"\n    href=\"https:\/\/formulierdus-i.nl\/contact\/?kenmerk=PostCov&dus%2Di%5Furl=https%3A%2F%2Fwww%2Edus%2Di%2Enl%2Fsubsidies%2Fzorgmedewerkers%2Dmet%2Dlangdurige%2Dpost%2Dcovid%2Dklachten\"\n    target=\"_blank\"\n    rel=\"nofollow noopener external\"\n>contactformulier<\/a\n> of bel met 070-3405566. Wij zijn telefonisch bereikbaar op werkdagen van 9.00 tot 16.00 uur.<\/p>\n\n<h2>Aanvraag starten<\/h2>\n"}}]}],"options":{"required":[],"allOf":[]}},{"type":"CustomPageControl","label":"Persoonsgegevens toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","elements":[{"type":"FormNotification","options":{"displayAs":"explanation","message":"U moet ouder zijn dan 18 jaar (artikel 4 van de regeling)."}}]},{"type":"Group","label":"Persoonlijke informatie","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName","label":"Voornaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/infix","label":"Tussenvoegsel","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/lastName","label":"Achternaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/dateOfBirth","label":"Geboortedatum","options":{"placeholder":""}}]}]},{"type":"Group","label":"Adres","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/street","label":"Straatnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumber","label":"Huisnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/houseNumberSuffix","label":"Huisnummer toevoeging","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/postalCode","label":"Postcode","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/city","label":"Plaatsnaam","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/country","label":"Land","options":{"format":"select","placeholder":"Selecteer een land","default":"Nederland"}}]}]},{"type":"Group","label":"Contact","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/phoneNumber","label":"Telefoonnummer","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/email","label":"E-mailadres","options":{"placeholder":"","tip":"U wordt via dit e-mailadres ge\u00efnformeerd over de status van uw aanvraag. Geef daarom alleen uw eigen e-mailadres door."}}]}]},{"type":"Group","label":"Bank","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/bankAccountHolder","label":"Naam rekeninghouder","options":{"placeholder":""}},{"type":"CustomControl","scope":"#\/properties\/bankAccountNumber","label":"IBAN","options":{"placeholder":"","tip":"Staat u onder bewind? Vermeld dan het IBAN van uw beheerrekening."}}]}]}]}],"options":{"required":["firstName","lastName","street","dateOfBirth","houseNumber","postalCode","city","country","phoneNumber","email","bankAccountHolder","bankAccountNumber"],"allOf":[]}},{"type":"CustomPageControl","label":"Documenten toevoegen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"UWV","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/certifiedEmploymentDocument","label":"Gewaarmerkt verzekeringsbericht","options":{"accept":"image\/*,.pdf","maxFileSize":20971520}}]}]},{"type":"Group","label":"WIA","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/wiaDecisionDocument","label":"WIA-Beslissing","options":{"accept":"image\/*,.pdf","maxFileSize":20971520}},{"type":"CustomControl","scope":"#\/properties\/isWiaDecisionPostponed","label":"Is uw WIA-beslissing uitgesteld vanwege een (vrijwillige of verplichte) loondoorbetaling?","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/wiaDecisionPostponedLetter","label":"Toekenningsbrief","options":{"accept":"image\/*,.pdf","maxFileSize":20971520},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/isWiaDecisionPostponed","schema":{"const":"Ja"}}}}]}]},{"type":"Group","label":"Werkgever","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employmentContract","label":"Bewijs van uw dienstverband","options":{"accept":"image\/*,.pdf","maxFileSize":20971520}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employmentFunction","label":"Functie","options":{"format":"select","placeholder":"-- Selecteer een functie --"}}]},{"type":"CustomControl","scope":"#\/properties\/otherEmploymentFunction","label":"Andere functie","options":{"placeholder":""},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/employmentFunction","schema":{"const":"Anders"}}}},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/employerKind","label":"Werkgever","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/otherEmployerDeclarationFile","label":"Verklaring zorgaanbieder","options":{"accept":"image\/*,.pdf","maxFileSize":20971520},"rule":{"effect":"SHOW","condition":{"scope":"#","schema":{"anyOf":[{"required":["employmentFunction"],"properties":{"employmentFunction":{"const":"Anders"}}},{"required":["employerKind"],"properties":{"employerKind":{"const":"Andere organisatie"}}}]}}}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/hasBeenWorkingAtJudicialInstitution","label":"Heeft u gewerkt in een justiti\u00eble inrichting?","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/BIGNumberJudicialInstitution","label":"BIG-nummer","options":{},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/hasBeenWorkingAtJudicialInstitution","schema":{"const":"Ja"}}}}]}]},{"type":"Group","label":"Medisch","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/socialMedicalAssessment","label":"Medisch onderzoeksverslag (medische rapportage)","options":{"accept":"image\/*,.pdf","maxFileSize":20971520}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/hasPostCovidDiagnose","label":"Bevat uw medisch onderzoeksverslag de diagnose langdurige post-COVID?","options":{"format":"radio"}}]},{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/doctorsCertificate","label":"Verklaring arts","options":{"accept":"image\/*,.pdf","maxFileSize":20971520},"rule":{"effect":"SHOW","condition":{"scope":"#\/properties\/hasPostCovidDiagnose","schema":{"const":"Nee"}}}}]}]}]}],"options":{"required":["certifiedEmploymentDocument","wiaDecisionDocument","isWiaDecisionPostponed","employmentContract","employmentFunction","employerKind","hasBeenWorkingAtJudicialInstitution","socialMedicalAssessment","hasPostCovidDiagnose"],"allOf":[{"if":{"properties":{"isWiaDecisionPostponed":{"const":"Ja"}}},"then":{"required":["wiaDecisionPostponedLetter"]}},{"if":{"anyOf":[{"required":["employmentFunction"],"properties":{"employmentFunction":{"const":"Anders"}}},{"required":["employerKind"],"properties":{"employerKind":{"const":"Andere organisatie"}}}]},"then":{"required":["otherEmployerDeclarationFile"]}},{"if":{"properties":{"hasBeenWorkingAtJudicialInstitution":{"const":"Ja"}}},"then":{"required":["BIGNumberJudicialInstitution"]}},{"if":{"properties":{"employmentFunction":{"const":"Anders"}}},"then":{"required":["otherEmploymentFunction"]}},{"if":{"properties":{"hasPostCovidDiagnose":{"const":"Nee"}}},"then":{"required":["doctorsCertificate"]}}]}},{"type":"CustomPageControl","label":"Controleren en ondertekenen","elements":[{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"Group","label":"Controleren","elements":[{"type":"FormResultsTable","label":"Uw gegevens","options":{"fields":{"Naam":"{firstName} {infix} {lastName}","Adres":"{street} {houseNumber}{houseNumberSuffix} {postalCode} {city}","Telefoon":"{phoneNumber}","E-mailadres":"{email}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"Group","label":"Ondertekenen","elements":[{"type":"CustomControl","scope":"#\/properties\/truthfullyCompleted","label":"Waarheidsverklaring","options":{"description":"Ik verklaar het formulier naar waarheid te hebben ingevuld."}}]}]}],"options":{"required":["truthfullyCompleted"],"allOf":[]}}]}', NULL, NULL, '{"type":"FormGroupControl","options":{"section":true},"elements":[{"type":"FormGroupControl","label":"Persoonlijke informatie","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Voornaam":"{firstName}","Tussenvoegsel":"{infix}","Achternaam":"{lastName}","Geboortedatum":"{dateOfBirth}"}}}]},{"type":"FormGroupControl","label":"Adres","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Land":"{country}","Straatnaam":"{street}","Huisnummer":"{houseNumber}","Huisnummer toevoeging":"{houseNumberSuffix}","Postcode":"{postalCode}","Plaatsnaam":"{city}"}}}]},{"type":"FormGroupControl","label":"Contact","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Telefoonnummer":"{phoneNumber}","E-mailadres":"{email}"}}}]},{"type":"FormGroupControl","label":"Bank","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"IBAN":"{bankAccountNumber}","Naam rekeninghouder":"{bankAccountHolder}"}}}]},{"type":"FormGroupControl","label":"UWV","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Gewaarmerkt verzekeringsbericht":"{certifiedEmploymentDocument}"}}}]},{"type":"FormGroupControl","label":"WIA","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"WIA-Beslissing":"{wiaDecisionDocument}","WIA-Beslissing uitgesteld":"{isWiaDecisionPostponed}","Toekenningsbrief (indien uitgesteld)":"{Toekenningsbrief}"}}}]},{"type":"FormGroupControl","label":"Werkgever","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Bewijs van dienstverband":"{employmentContract}","Functie":"{employmentFunction}","Werkgever":"{employerKind}","Verklaring zorgaanbieder (indien anders)":"{otherEmployerDeclarationFile}","Heeft u gewerkt in een justiti\u00eble inrichting?":"{hasBeenWorkingAtJudicialInstitution}","BIG-nummer justiti\u00eble inrichting":"{BIGNumberJudicialInstitution}"}}}]},{"type":"FormGroupControl","label":"Medisch","options":{"section":true,"headingLevel":"2"},"elements":[{"type":"FormResultsTable","options":{"fields":{"Medisch onderzoeksverslag (medische rapportage)":"{socialMedicalAssessment}","Bevat uw medisch onderzoeksverslag de diagnose langdurige post-COVID?":"{hasPostCovidDiagnose}","Verklaring arts (indien geen diagnose)":"{doctorsCertificate}"}}}]}]}');
INSERT INTO public.subsidy_stage_uis VALUES ('71f71916-c0ed-45bc-8186-1b4f5dfb69e8', '8027c102-93ef-4735-ab66-97aa63b836eb', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Checklist","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/checklist","options":{"format":"checkbox-group"}}]}]},{"type":"Group","label":"Uitkering","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}}]}]},{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]}]}', NULL, NULL, '{"type":"FormGroupControl","elements":[{"type":"FormGroupControl","label":"Aanvraag","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Aanvraag","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName"},{"type":"CustomControl","scope":"#\/properties\/infix"},{"type":"CustomControl","scope":"#\/properties\/lastName"},{"type":"CustomControl","scope":"#\/properties\/street"},{"type":"CustomControl","scope":"#\/properties\/houseNumber"}]}]}],"options":[]},{"type":"FormGroupControl","label":"Eerste beoordeling","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Eerste beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/checklist","options":{"format":"checkbox"}},{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]}],"options":{"required":["firstAssessment"]}}]}');
INSERT INTO public.subsidy_stage_uis VALUES ('44914bc7-9e4f-4b79-9498-01adbe5c4cfe', '61436439-e337-4986-bc18-57138e2fab65', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]}]}]}', NULL, NULL, '{"type":"FormGroupControl","elements":[{"type":"FormGroupControl","label":"Aanvraag","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Aanvraag","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName"},{"type":"CustomControl","scope":"#\/properties\/infix"},{"type":"CustomControl","scope":"#\/properties\/lastName"},{"type":"CustomControl","scope":"#\/properties\/street"},{"type":"CustomControl","scope":"#\/properties\/houseNumber"}]}]}],"options":[]},{"type":"FormGroupControl","label":"Eerste aanvraag","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Eerste beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/checklist","options":{"format":"checkbox"}},{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]}],"options":[]},{"type":"FormGroupControl","label":"Tweede aanvraag","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Tweede beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]}]}],"options":{"required":["secondAssessment"]}}]}');
INSERT INTO public.subsidy_stage_uis VALUES ('e819df05-03b7-4f37-b315-7f62339fd067', '7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]}]}', NULL, NULL, '{"type":"FormGroupControl","elements":[{"type":"FormGroupControl","label":"Aanvraag","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Aanvraag","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName"},{"type":"CustomControl","scope":"#\/properties\/infix"},{"type":"CustomControl","scope":"#\/properties\/lastName"},{"type":"CustomControl","scope":"#\/properties\/street"},{"type":"CustomControl","scope":"#\/properties\/houseNumber"}]}]}]},{"type":"FormGroupControl","label":"Eerste beoordeling","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Eerste beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/checklist","options":{"format":"checkbox"}},{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]}]},{"type":"FormGroupControl","label":"Tweede beoordeling","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Tweede beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]}]}]},{"type":"FormGroupControl","label":"Interne controle","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Interne controle","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]}]}]}');
INSERT INTO public.subsidy_stage_uis VALUES ('c51302f6-e131-45ff-8d4b-f4ff4a39b52f', '85ed726e-cdbe-444e-8d12-c56f9bed2621', 1, 'published', '{"type":"FormGroupControl","options":{"section":true,"group":true},"elements":[{"type":"Group","label":"Status","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]}]}', NULL, NULL, '{"type":"FormGroupControl","elements":[{"type":"FormGroupControl","label":"Aanvraag","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Aanvraag","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/firstName"},{"type":"CustomControl","scope":"#\/properties\/infix"},{"type":"CustomControl","scope":"#\/properties\/lastName"},{"type":"CustomControl","scope":"#\/properties\/street"},{"type":"CustomControl","scope":"#\/properties\/houseNumber"}]}]}]},{"type":"FormGroupControl","label":"Eerste beoordeling","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Eerste beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/checklist","options":{"format":"checkbox"}},{"type":"CustomControl","scope":"#\/properties\/amount","options":{"format":"radio"}},{"type":"CustomControl","scope":"#\/properties\/firstAssessment","options":{"format":"radio"}}]}]}]},{"type":"FormGroupControl","label":"Tweede beoordeling","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Tweede beoordeling","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/secondAssessment","options":{"format":"radio"}}]}]}]},{"type":"FormGroupControl","label":"Interne controle","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Interne controle","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/internalAssessment","options":{"format":"radio"}}]}]}]},{"type":"FormGroupControl","label":"Uitvoeringsco\u00f6rdinator controle","elements":[{"type":"CustomGroupControl","options":{"section":true},"label":"Uitvoeringsco\u00f6rdinator controle","elements":[{"type":"VerticalLayout","elements":[{"type":"CustomControl","scope":"#\/properties\/implementationCoordinatorAssessment","options":{"format":"radio"}}]}]}]}]}');


--
-- Data for Name: subsidy_stages; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.subsidy_stages VALUES ('09718b39-800d-366c-b91f-e6d49e2ce2eb', '2023-09-28 20:13:05', 'f5eeeca5-1f23-4a19-a31c-55e6b958f3ec', 'Id repudiandae delectus voluptatem modi sint.', 'applicant', NULL, 1);
INSERT INTO public.subsidy_stages VALUES ('14da837e-49bd-35bd-ad0b-395f9994cd08', '2023-09-28 20:13:05', '9b9c3f26-8309-4a7e-bd43-998166d2fd97', 'Ut consequatur iste nemo sapiente nobis.', 'applicant', NULL, 1);
INSERT INTO public.subsidy_stages VALUES ('c4bffbaf-8c47-34c1-823b-1afc7ecc717b', '2023-09-28 20:13:05', 'f5eeeca5-1f23-4a19-a31c-55e6b958f3ec', 'Unde aut accusantium qui non.', 'applicant', NULL, 1);
INSERT INTO public.subsidy_stages VALUES ('721c1c28-e674-415f-b1c3-872a631ed045', NULL, '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Aanvraag', 'applicant', NULL, 1);
INSERT INTO public.subsidy_stages VALUES ('6bcd59ab-2ba6-43bb-a1a1-9fb995f0e59c', NULL, '907bb399-0d19-4e1a-ac75-25a864df27c6', 'Beoordeling', 'assessor', NULL, 2);
INSERT INTO public.subsidy_stages VALUES ('7e5d64e9-35f0-4fee-b8d2-dca967b43183', NULL, '513011cd-789b-4628-ba5c-2fee231f8959', 'Aanvraag', 'applicant', NULL, 1);
INSERT INTO public.subsidy_stages VALUES ('8027c102-93ef-4735-ab66-97aa63b836eb', NULL, '513011cd-789b-4628-ba5c-2fee231f8959', 'Eerste beoordeling', 'assessor', NULL, 2);
INSERT INTO public.subsidy_stages VALUES ('61436439-e337-4986-bc18-57138e2fab65', NULL, '513011cd-789b-4628-ba5c-2fee231f8959', 'Tweede beoordeling', 'assessor', NULL, 3);
INSERT INTO public.subsidy_stages VALUES ('7ceb3c91-5c3b-4627-b9ef-a46d5fe2ed68', NULL, '513011cd-789b-4628-ba5c-2fee231f8959', 'Interne controle', 'assessor', NULL, 4);
INSERT INTO public.subsidy_stages VALUES ('85ed726e-cdbe-444e-8d12-c56f9bed2621', NULL, '513011cd-789b-4628-ba5c-2fee231f8959', 'Uitvoeringscoördinator controle', 'assessor', NULL, 5);


--
-- Data for Name: subsidy_versions; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.subsidy_versions VALUES ('f5eeeca5-1f23-4a19-a31c-55e6b958f3ec', '2023-09-28 20:13:05', 'a320abc3-6913-4da8-a803-6cf49b2b25e5', 1, 'published', 'https://www.dus-i.nl/subsidies', 'dienstpostbus@minvws.nl', 'firstName;infix;lastName', 'email', 14, NULL);
INSERT INTO public.subsidy_versions VALUES ('9b9c3f26-8309-4a7e-bd43-998166d2fd97', '2023-09-28 20:13:05', '3f0b3cdc-937f-4de3-bb89-3f84ee31221a', 2, 'published', 'https://www.dus-i.nl/subsidies', 'dienstpostbus@minvws.nl', 'firstName;infix;lastName', 'email', 56, NULL);
INSERT INTO public.subsidy_versions VALUES ('907bb399-0d19-4e1a-ac75-25a864df27c6', '2019-02-01 00:00:00', '00f26400-7232-475f-922c-6b569b7e421a', 1, 'published', 'https://www.dus-i.nl/subsidies', 'dienstpostbus@minvws.nl', 'firstName;infix;lastName', 'email', 91, NULL);
INSERT INTO public.subsidy_versions VALUES ('513011cd-789b-4628-ba5c-2fee231f8959', '2023-08-31 00:00:00', '06a6b91c-d59b-401e-a5bf-4bf9262d85f8', 1, 'published', 'https://www.dus-i.nl/subsidies/zorgmedewerkers-met-langdurige-post-covid-klachten', 'dienstpostbus@minvws.nl', 'firstName;infix;lastName', 'email', NULL, '2024-01-22 23:59:59');


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 35, true);


--
-- Name: answers answers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_pkey PRIMARY KEY (id);


--
-- Name: application_hashes application_hashes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_hashes
    ADD CONSTRAINT application_hashes_pkey PRIMARY KEY (subsidy_stage_hash_id, application_id);


--
-- Name: application_messages application_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_messages
    ADD CONSTRAINT application_messages_pkey PRIMARY KEY (id);


--
-- Name: application_stages application_stages_application_id_sequence_number_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_stages
    ADD CONSTRAINT application_stages_application_id_sequence_number_unique UNIQUE (application_id, sequence_number);


--
-- Name: application_stages application_stages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_stages
    ADD CONSTRAINT application_stages_pkey PRIMARY KEY (id);


--
-- Name: applications applications_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applications
    ADD CONSTRAINT applications_pkey PRIMARY KEY (id);


--
-- Name: applications applications_reference_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applications
    ADD CONSTRAINT applications_reference_unique UNIQUE (reference);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: fields fields_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fields
    ADD CONSTRAINT fields_pkey PRIMARY KEY (id);


--
-- Name: subsidy_stage_hash_fields form_hash_fields_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_hash_fields
    ADD CONSTRAINT form_hash_fields_pkey PRIMARY KEY (subsidy_stage_hash_id, field_id);


--
-- Name: subsidy_stage_hashes form_hashes_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_hashes
    ADD CONSTRAINT form_hashes_pkey PRIMARY KEY (id);


--
-- Name: subsidy_stage_uis form_uis_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_uis
    ADD CONSTRAINT form_uis_pkey PRIMARY KEY (id);


--
-- Name: subsidy_stages forms_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stages
    ADD CONSTRAINT forms_pkey PRIMARY KEY (id);


--
-- Name: identities identities_hashed_identifier_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identities
    ADD CONSTRAINT identities_hashed_identifier_unique UNIQUE (hashed_identifier);


--
-- Name: identities identities_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.identities
    ADD CONSTRAINT identities_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: subsidies subsidies_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidies
    ADD CONSTRAINT subsidies_pkey PRIMARY KEY (id);


--
-- Name: subsidies subsidies_reference_prefix_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidies
    ADD CONSTRAINT subsidies_reference_prefix_unique UNIQUE (reference_prefix);


--
-- Name: subsidy_stage_transition_messages subsidy_stage_transition_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_transition_messages
    ADD CONSTRAINT subsidy_stage_transition_messages_pkey PRIMARY KEY (id);


--
-- Name: subsidy_stage_transitions subsidy_stage_transitions_current_subsidy_stage_id_target_subsi; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_transitions
    ADD CONSTRAINT subsidy_stage_transitions_current_subsidy_stage_id_target_subsi UNIQUE (current_subsidy_stage_id, target_subsidy_stage_id);


--
-- Name: subsidy_stage_transitions subsidy_stage_transitions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_transitions
    ADD CONSTRAINT subsidy_stage_transitions_pkey PRIMARY KEY (id);


--
-- Name: subsidy_versions subsidy_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_versions
    ADD CONSTRAINT subsidy_versions_pkey PRIMARY KEY (id);


--
-- Name: application_stages_application_id_is_current_index; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX application_stages_application_id_is_current_index ON public.application_stages USING btree (application_id, is_current);


--
-- Name: answers answers_application_stage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_application_stage_id_foreign FOREIGN KEY (application_stage_id) REFERENCES public.application_stages(id);


--
-- Name: answers answers_field_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_field_id_foreign FOREIGN KEY (field_id) REFERENCES public.fields(id);


--
-- Name: application_hashes application_hashes_application_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_hashes
    ADD CONSTRAINT application_hashes_application_id_foreign FOREIGN KEY (application_id) REFERENCES public.applications(id);


--
-- Name: application_hashes application_hashes_subsidy_stage_hash_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_hashes
    ADD CONSTRAINT application_hashes_subsidy_stage_hash_id_foreign FOREIGN KEY (subsidy_stage_hash_id) REFERENCES public.subsidy_stage_hashes(id);


--
-- Name: application_messages application_messages_application_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_messages
    ADD CONSTRAINT application_messages_application_id_foreign FOREIGN KEY (application_id) REFERENCES public.applications(id) ON DELETE CASCADE;


--
-- Name: application_stages application_stages_application_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_stages
    ADD CONSTRAINT application_stages_application_id_foreign FOREIGN KEY (application_id) REFERENCES public.applications(id);


--
-- Name: application_stages application_stages_subsidy_stage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.application_stages
    ADD CONSTRAINT application_stages_subsidy_stage_id_foreign FOREIGN KEY (subsidy_stage_id) REFERENCES public.subsidy_stages(id);


--
-- Name: applications applications_identity_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applications
    ADD CONSTRAINT applications_identity_id_foreign FOREIGN KEY (identity_id) REFERENCES public.identities(id);


--
-- Name: applications applications_subsidy_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.applications
    ADD CONSTRAINT applications_subsidy_version_id_foreign FOREIGN KEY (subsidy_version_id) REFERENCES public.subsidy_versions(id);


--
-- Name: fields fields_subsidy_stage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.fields
    ADD CONSTRAINT fields_subsidy_stage_id_foreign FOREIGN KEY (subsidy_stage_id) REFERENCES public.subsidy_stages(id) ON DELETE CASCADE;


--
-- Name: subsidy_stage_hash_fields form_hash_fields_field_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_hash_fields
    ADD CONSTRAINT form_hash_fields_field_id_foreign FOREIGN KEY (field_id) REFERENCES public.fields(id);


--
-- Name: subsidy_stage_hash_fields form_hash_fields_form_hash_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_hash_fields
    ADD CONSTRAINT form_hash_fields_form_hash_id_foreign FOREIGN KEY (subsidy_stage_hash_id) REFERENCES public.subsidy_stage_hashes(id);


--
-- Name: subsidy_stage_hashes form_hashes_form_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_hashes
    ADD CONSTRAINT form_hashes_form_id_foreign FOREIGN KEY (subsidy_stage_id) REFERENCES public.subsidy_stages(id);


--
-- Name: subsidy_stage_uis form_uis_form_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_uis
    ADD CONSTRAINT form_uis_form_id_foreign FOREIGN KEY (subsidy_stage_id) REFERENCES public.subsidy_stages(id) ON DELETE CASCADE;


--
-- Name: subsidy_stage_transition_messages subsidy_stage_transition_messages_subsidy_stage_transition_id_f; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_transition_messages
    ADD CONSTRAINT subsidy_stage_transition_messages_subsidy_stage_transition_id_f FOREIGN KEY (subsidy_stage_transition_id) REFERENCES public.subsidy_stage_transitions(id);


--
-- Name: subsidy_stage_transitions subsidy_stage_transitions_current_subsidy_stage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_transitions
    ADD CONSTRAINT subsidy_stage_transitions_current_subsidy_stage_id_foreign FOREIGN KEY (current_subsidy_stage_id) REFERENCES public.subsidy_stages(id) ON DELETE CASCADE;


--
-- Name: subsidy_stage_transitions subsidy_stage_transitions_target_subsidy_stage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stage_transitions
    ADD CONSTRAINT subsidy_stage_transitions_target_subsidy_stage_id_foreign FOREIGN KEY (target_subsidy_stage_id) REFERENCES public.subsidy_stages(id) ON DELETE CASCADE;


--
-- Name: subsidy_stages subsidy_stages_subsidy_version_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_stages
    ADD CONSTRAINT subsidy_stages_subsidy_version_id_foreign FOREIGN KEY (subsidy_version_id) REFERENCES public.subsidy_versions(id);


--
-- Name: subsidy_versions subsidy_versions_subsidy_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.subsidy_versions
    ADD CONSTRAINT subsidy_versions_subsidy_id_foreign FOREIGN KEY (subsidy_id) REFERENCES public.subsidies(id);


--
-- PostgreSQL database dump complete
--
