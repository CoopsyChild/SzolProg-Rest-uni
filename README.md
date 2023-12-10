# SzolProg-Beadandó-REST

## Endpoints

| URL             | HTTP method | Auth | Admin | JSON Response           |
|-----------------|-------------|------|-------|-------------------------|
| /users/login    | POST        |      |       | user's session w/ token |
| /users/register | POST        |      |       | new user created        |
| /users          | GET         | Y    | Y     | all users               |
| /users          | PUT         | Y    |       | edited user             |
| /drinks         | GET         | Y    | Y     | all drinks (for admin)  |
| /drinks         | POST        | Y    |       | new drink created       |
| /drinks         | PUT         | Y    |       | edited drink            |
| /drinks         | DELETE      | Y    |       | true / false            |
