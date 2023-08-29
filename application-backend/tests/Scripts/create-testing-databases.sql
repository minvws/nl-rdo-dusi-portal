SELECT 'CREATE DATABASE portal_backend_testing'
    WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'portal_backend_testing')\gexec
