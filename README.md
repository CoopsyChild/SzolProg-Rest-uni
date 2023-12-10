# SzolProg-Beadandó-REST

## Endpoints

| URL              | HTTP method | Auth | Admin | JSON Response     |
|------------------|-------------|------|-------|-------------------|
| /users/login     | POST        |      |       | user's token      |
| /users           | GET         | Y    | Y     | all users         |
| /users           | POST        |      |       | new user created  |
| /products        | GET         | Y    | Y     | all drinks        |
| /products/userid | GET         | Y    | Y     | all user's drinks |
| /products        | POST        | Y    |       | new drink added   |
| /products        | PUT         | Y    |       | edited drink      |
| /products        | DELETE      | Y    |       | true / false      |
