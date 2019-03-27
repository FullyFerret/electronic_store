# TalentNet Code Challenge (electronic_store API)

### Endpoints
```
POST /create-client

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

### Install dependencies/Setting up:
1) After pulling from this repo, go to root project directory and run:
```
composer install
```

2) Create database
```
bin/console doctrine:database:create
```

3) Create tables
```
bin/console doctrine:schema:create
```


### Create Users:

1) cd to project directory root

2) Run these commands to populate users:

```
bin/console fos:user:create BobbyFischer bobby@foo.com password1
bin/console fos:user:create BobbyFischer betty@foo.com password1
```


### /create-client to get access_token for OAuth authenticated requests:

Note. grant_type values below should have the literal value "password", everything else is uniquely generated from each preceding step and must be replaced.

1. Create client by POSTing to `/create-client` with the following body:
```
{
	"redirect-uri": "any-url-goes-here",
	"grant-type": "password"
}
```

2. Get `access_token` (`grant_type` should literally be "password", the rest you replace):

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

1) Make a POST request to `/api/products` for each product below (using Postman or another tool) to populate database with products and their associated categories:

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
### Start symfony server for testing
1)
```
php bin/console server:run
```

2)
✅ ✅ You're good to go for making REST API requests
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
