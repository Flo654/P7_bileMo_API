
# BileMo_API

project n°7 Create a web service exposing an API from "OpenClassrooms PHP/Symfony developper course"


## Authors

- [@florentascensio](https://www.github.com/Flo654)

  
## Badges


[![Maintainability](https://api.codeclimate.com/v1/badges/41317942e34622cb6a87/maintainability)](https://codeclimate.com/github/Flo654/P7_bileMo_API/maintainability)
## Run Locally

Clone the project

```bash
  git clone https://link-to-project
```

Go to the project directory

```bash
  cd my-project
```

Install dependencies

```bash
  composer install
```


  
## Generation  of SSL keys

Create folder

```bash
  mkdir -p config/jwt
```

generate private key and public key with passphrase

```bash
  openssl genrsa -out config/jwt/private.pem -aes256 4096
  openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```

report the passphrase in .env.test file
## Environment Variables

To run this project, you will need to add the following environment variables to your .env.test

###> doctrine/doctrine-bundle ###

`DATABASE_URL`

###> lexik/jwt-authentication-bundle ###

`JWT_PASSPHRASE`

  !!!! don't forget to remove .test extension from the .env.test file
## Run the project


Create database and data

```bash
  composer prepare
```

Start the server

```bash
  symfony server:start
```

  
## Documentation

[LexikJWTAuthenticationBundle](https://github.com/lexik/LexikJWTAuthenticationBundle)

  
## API Documentation

#### Html interactive documentation

```http
  GET /api/doc
```

#### Json documentation

```http
  GET /api/doc.json
```