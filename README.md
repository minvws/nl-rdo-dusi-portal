# Application model
Application model for the models and repositories of portal-backend.

## Tests
To run the tests use the following steps:

```sh
# 1. Start the postgres container
docker run -d --name postgres-container -e POSTGRES_PASSWORD=postgres -v ./tests/Scripts/init.sql:/docker-entrypoint-initdb.d/init.sql -p 5432:5432 postgres

# 2. Run the tests
composer test

# 3. Stop and remove the postgres container
docker stop postgres-container && docker rm -v postgres-container
```