SELECT 'CREATE DATABASE portal_backend_testing'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'portal_backend_testing')\gexec
SELECT 'CREATE DATABASE form_admin_web_testing'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'form_admin_web_testing')\gexec
