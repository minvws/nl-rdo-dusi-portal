SELECT 'CREATE DATABASE user_admin'
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'user_admin')\gexec
