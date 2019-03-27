# TalentNet Code Challenge (electronic_store API)

### Endpoints
```
POST /create-client
POST /oauth/v2/token

GET /api/categories

GET /api/products
GET /api/products/{id}

/* OAuth Authentication required (see below for guide) */
POST /api/products
PUT /api/products/{id}
DELETE /api/products/{id}
```
## Assumptions
* Product creation/updates can yield new categories.

## Data Layer used
* MySQL 5.6

## Instructions

### .env file

Create a `.env` file at the root of the project directory and set the following key/value pair (adjusted with your full MySQL url).

For example:

`DATABASE_URL=mysql://root@127.0.0.1/electronics_store`

### Install dependencies/Setting up:
1) Make sure composer is installed:
https://getcomposer.org/download/

2) After pulling this repo, go to root project directory and run:
```
composer install
```

3) Create database (make sure MySQL is running)
```
bin/console doctrine:database:create
```

4) Create tables
```
bin/console doctrine:schema:create
```


### Create Users:

1) cd to project directory root

2) Run these commands to populate users:

```
bin/console fos:user:create BobbyFischer bobby@foo.com password1
bin/console fos:user:create BettyRubble betty@foo.com password1
```

### Start symfony server for testing

```
php bin/console server:run
```

### /create-client to get access_token for OAuth authenticated requests:

Note. grant_type values should have the literal value "password"; everything else should be replaced using the corresponding values generated from each preceding step.

1. Create client by POSTing to `/create-client` with the following body:
```
{
	"redirect-uri": "any-url-goes-here",
	"grant-type": "password"
}
```

2. Get `access_token` by POSTing to `http://localhost:8083/oauth/v2/token` (`grant_type` should literally be "password", the rest you replace):

input:

```
{
    "client_id": "2_60lvo1hsrq4gkcc80w8cs8swgkk4c4ks0ok84sok84s0kc8c00",
    "client_secret": "slfw1z8ytxc008gck8s4cwcko04gc48kgw88o8o08w440kgwc",
    "grant_type": "password",
    "username": "foo",
    "password": "bar"
}
```
output:
```
{
    "access_token": "NmY2ODUyZjE2YjJiYzBiYjdlMDdkNTIyMmI3MmI4NTE2Zjc3ZTY1YjhiYTg2ZTk4OTc4MGIxN2JmNTVjNjJiOA",
    "expires_in": 86400,
    "token_type": "bearer",
    "scope": null,
    "refresh_token": "NjVjYzliZmZkZWMzN2IxOTdkYTM5NmYxN2JkZWMwYTFhOTI0MjQ5NzRkMDgxMzVjMDIxZTk1NTRhOGNiZDA2MA"
}
```

3. You can now put `access_token` into `Authorization: Bearer` for authenticated requests.

## Seeding product data

1) Make POST requests to `/api/products` for each of the products below (using Postman or another tool). This will populate your database with products and their associated categories:

```
{
  "name": "Pong",
  "category": "Games",
  "sku": "A0001",
  "price": 69.99,
  "quantity": 20
},
{
  "name": "GameStation 5",
  "category": "Games",
  "sku": "A0002",
  "price": 269.99,
  "quantity": 15
},
{
  "name": "AP Oman PC - Aluminum",
  "category": "Computers",
  "sku": "A0003",
  "price": 1399.99,
  "quantity": 10
},
{
  "name": "Fony UHD HDR 55\" 4k TV",
  "category": "TVs and Accessories",
  "sku": "A0004",
  "price": 1399.99,
  "quantity": 5
}
```

## Unit tests
Run at project root: `php bin/phpunit src/`

#### ✅ You're good to go for making REST API requests.
___

## Senior PHP Developer Challenge

### Introduction
Your local electronics store has started to expand, but track their entire inventory by hand.  They have asked you to build a simple cataloging system as a REST API so that they can integrate with mobile and desktop applications in the future.

You are free to use any PHP libraries or modules in order to complete the challenge.  You may choose either MySQL/MariaDB or MongoDB as your data layer.

**The challenge is to be completed using Symfony4.**

#### Bonus Points
* Use Docker to build your solution
* Use Kahlan for your unit tests

### Requirements

The API should be able to:
* list all products
* list all categories
* retrieve a single product
* create a product
* update a product
* delete a product

#### Authentication
Only authenticated users can create, update, or delete a product.  No authentication is required to retrieve or list.

#### Data
> All entities should have timestamp fields (created_at, and modified_at)

Products have the following attributes: 
* name
* category
* SKU
* price
* quantity

Categories have the following attributes:
* name

##### Seed Data
Import the contents of [electronic-catalog.json](../data/seeds/electronic-catalog.json) into your database of choice.  It's up to you how you want to construct relations.

### Criteria
For full transparency, the test will be scored according to the following:
* REST Structure
* Unit Testing
* Logging
* Use of services, controllers, and models
* Use of Symfony4 as a framework
* Best practices
* Reusable code
* Decoupled code
* Ability to transform requirements into code
