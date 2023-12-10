# SzolProg-Beadandó-REST

## Endpoints

| URL           | HTTP method | Auth | JSON Response    |
|---------------|-------------|------|------------------|
| /users/login  | POST        |      | user's token     |
| /users        | GET         |  Y   | all users        |
| /users        | POST        |  Y   | new user created |
| /products     | GET         |      | all drinks       |
| /products     | POST        |  Y   | new drink added  |
| /products     | PUT         |  Y   | edited drink     |
| /products     | DELETE      |  Y   | true / false     |
