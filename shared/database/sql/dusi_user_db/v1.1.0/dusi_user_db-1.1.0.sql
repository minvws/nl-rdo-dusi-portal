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
-- Name: organisations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.organisations (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.organisations OWNER TO postgres;

--
-- Name: role_user; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.role_user (
    user_id uuid NOT NULL,
    role_name character varying(255) NOT NULL,
    subsidy_id uuid
);


ALTER TABLE public.role_user OWNER TO postgres;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.roles (
    name character varying(255) NOT NULL,
    view_all_stages boolean DEFAULT false NOT NULL
);


ALTER TABLE public.roles OWNER TO postgres;

--
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id uuid NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    password character varying(255) NOT NULL,
    two_factor_secret text,
    two_factor_recovery_codes text,
    created_by uuid,
    active_until timestamp(0) without time zone,
    password_reset_token character varying(255),
    password_reset_token_valid_until timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organisation_id uuid NOT NULL,
    password_updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO postgres;

--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.migrations VALUES (1, '2023_05_16_000000_create_users_table', 1);
INSERT INTO public.migrations VALUES (2, '2023_05_16_200000_create_organisations_table', 1);
INSERT INTO public.migrations VALUES (3, '2023_05_16_300000_create_roles_table', 1);
INSERT INTO public.migrations VALUES (4, '2023_05_16_400000_create_organisation_role_table', 1);
INSERT INTO public.migrations VALUES (5, '2023_09_18_173517_drop_organisation_role_table', 1);
INSERT INTO public.migrations VALUES (6, '2023_09_18_173820_add_organisation_id_to_users_table', 1);
INSERT INTO public.migrations VALUES (7, '2023_09_18_174237_add_roles_user_table', 1);
INSERT INTO public.migrations VALUES (8, '2023_09_20_133700_add_password_updated_at_to_users_table', 1);
INSERT INTO public.migrations VALUES (9, '2023_09_25_133700_add_view_all_stages_to_roles_table', 1);
INSERT INTO public.migrations VALUES (10, '2023_09_26_133700_update_role_names_in_roles_table', 1);
INSERT INTO public.migrations VALUES (11, '2023_09_26_420000_update_role_names_in_roles_table', 1);


--
-- Data for Name: organisations; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.organisations VALUES ('9a39e57d-6051-4da2-9f0e-64720a567ce8', 'DUS-I', '2023-09-26 15:22:20', '2023-09-26 15:22:20');


--
-- Data for Name: role_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.role_user VALUES ('9a39e5a3-81b3-4afa-b966-1e7a8c2434e8', 'userAdmin', NULL);


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.roles VALUES ('userAdmin', false);
INSERT INTO public.roles VALUES ('assessor', false);
INSERT INTO public.roles VALUES ('implementationCoordinator', true);
INSERT INTO public.roles VALUES ('internalAuditor', false);


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO public.users VALUES ('9a39e5a3-81b3-4afa-b966-1e7a8c2434e8', 'user', 'user@example.com', '$2y$13$/dzfvf5izDitiQyJ7upgo.9LLKAWilWBoSve.DIJlO5BuNt8Inadq', 'eyJpdiI6ImZzYlpvSGc0ekhob0VSOGkiLCJ2YWx1ZSI6IkhhL2o2S1Noc2xmdnNLU3Y1Yk9IeXJQZGROeHlLejlmIiwibWFjIjoiIiwidGFnIjoiR2l5V2dXb3VLb2x6WWNMcTZnOXNCdz09In0=', NULL, NULL, NULL, NULL, NULL, '2023-09-26 15:22:45', '2023-09-26 15:22:45', '9a39e57d-6051-4da2-9f0e-64720a567ce8', NULL);
INSERT INTO public.users VALUES ('9a39e5a5-9c8d-44a9-9a63-863f52d9a430', 'password', 'assessor@example.com', '$2y$13$JwSGREueO8.s.94EazlmJ.q8vDInP5CgQiAZFUugTd.H288aTTnze', 'eyJpdiI6InBiVmJ6RjJuRC9UYUF6ZEciLCJ2YWx1ZSI6IkRSVjB3UjlWU1J4c044ODlQZGI5NEtHM3pDNm13VWNYIiwibWFjIjoiIiwidGFnIjoiZ0xVdGVKT05EZk5kZTNzd0NOdWROdz09In0=', NULL, NULL, NULL, NULL, NULL, '2023-09-26 15:22:46', '2023-09-26 15:22:46', '9a39e57d-6051-4da2-9f0e-64720a567ce8', NULL);
INSERT INTO public.users VALUES ('9a39e5a7-abcd-480f-b4ec-acafb26a54d7', 'password', 'implementationCoordinator@example.com', '$2y$13$NL6b7SPbB0kuVsSimx3OS.xnmzTqzRT1D7J7U6rzmUwTQfZ5FtWqi', 'eyJpdiI6IkJTRE5UQzJMNWxURVJDTGwiLCJ2YWx1ZSI6IllScVQzeFRYRzVTOE9xc1NISWlER0xxMms2ZzdRdkxQIiwibWFjIjoiIiwidGFnIjoiTzBDeXhNazc5NklCQlBBd0V6ZitlUT09In0=', NULL, NULL, NULL, NULL, NULL, '2023-09-26 15:22:47', '2023-09-26 15:22:47', '9a39e57d-6051-4da2-9f0e-64720a567ce8', NULL);
INSERT INTO public.users VALUES ('9a39e5a9-bbe0-4e63-a950-9288f5d68252', 'password', 'internalAuditor@example.com', '$2y$13$xHLAeRyEevsPfX0CqMQphOFq6rhX.dLjTENviCn3TU0eatBwdzaYK', 'eyJpdiI6IkhZM3lpc2dCdXBpcDZicGoiLCJ2YWx1ZSI6Ilh6czdxODc0TkFLcXdBbEpsV2hqMHZmTzJvR1A3Rm9EIiwibWFjIjoiIiwidGFnIjoieFAxaEcyTWd2UEc1YWlIVTBnNEZ0UT09In0=', NULL, NULL, NULL, NULL, NULL, '2023-09-26 15:22:49', '2023-09-26 15:22:49', '9a39e57d-6051-4da2-9f0e-64720a567ce8', NULL);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.migrations_id_seq', 11, true);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: organisations organisations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.organisations
    ADD CONSTRAINT organisations_pkey PRIMARY KEY (id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (name);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: role_user role_user_role_name_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_role_name_foreign FOREIGN KEY (role_name) REFERENCES public.roles(name);


--
-- Name: role_user role_user_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.role_user
    ADD CONSTRAINT role_user_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: users users_organisation_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_organisation_id_foreign FOREIGN KEY (organisation_id) REFERENCES public.organisations(id);


--
-- PostgreSQL database dump complete
--

