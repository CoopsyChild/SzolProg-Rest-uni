# SzolProg-Beadandó-REST

## Endpoints

| URL               | HTTP method | Auth | Admin | JSON Response                     |
|-------------------|-------------|------|-------|-----------------------------------|
| /users/login      | POST        |      |       | user's session w/ token           |
| /users/register   | POST        |      |       | new user created                  |
| /users            | GET         | Y    | Y     | all users                         |
| /users            | PUT         | Y    |       | edited user                       |
| /drinks           | GET         | Y    | Y     | get user's drinks (all for admin) |
| /drinks           | POST        | Y    |       | new drink created                 |
| /drinks           | PUT         | Y    |       | edited drink                      |
| /drinks           | DELETE      | Y    |       | true / false                      |
| /drink-category   | GET         | Y    |       | get all categories                |
| /drink-category   | DELETE      | Y    | Y     | true/ false                       |
| /drink-category   | PUT         | Y    | Y     | edited category                   |
| /drink-category   | POST        | Y    | Y     | created category                  |