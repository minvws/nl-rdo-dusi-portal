# Subsidy model
Subsidy model for the models and repositories of form-admin-web.

## Tests
The tests need a postgres database to run. The easiest way to get this is to run the following command:
```docker run --name subsidy-model-test-postgres -e POSTGRES_PASSWORD=postgres -p 5432:5432 -d postgres```