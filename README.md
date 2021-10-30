#Postman file generator

<p><img src="https://github.com/matijajanc/postman/-/raw/master/images/postman.png"></p>

### Installation

// todo Publish to packagist
```
composer require ...
```

Update config settings under
```
config/postman.php
```


### Run command to generate Postman files
```
php artisan postman:generate
```

This command generates 2 files:
- postman-environment.json (where variables are defined)
- postman.json (where all API routes are defined)

Currently it supports bearer token authorization, if you have other wishes please create new issue or pull request on github and I'll add/extend it.

<p><img src="https://github.com/matijajanc/postman/-/raw/master/images/postman_api.png"></p>

<p><img src="https://github.com/matijajanc/postman/-/raw/master/images/postman_authorization.png"></p>

## Requirements
- Laravel 8 (it works with older version of Laravel also but you need to provide "method" property in Postman attribute definition)
- PHP ^8.0

### PHP 8 attributes
I used new PHP 8 attributes feature to define new properties I use for postman file generator.
This is not needed to define for GET, DELETE routes, you provide this Postman attribute only where you want your custom request payload POST, PUT.


If no method is provided it calls default factory "definition" method

```
#[Postman(['factory' => ContractFactory::class])]
public function createContract(Request $request): JsonResponse
```

You can override which methods get called with additional "method" property

```
#[Postman(['factory' => ContractFactory::class, 'method' => 'postCreateContract'])]
public function createContract(Request $request): JsonResponse
```

<p><img src="https://github.com/matijajanc/postman/-/raw/master/images/factory.png"></p>

You can override body type with property "mode"

```
#[Postman(['factory' => ContractFactory::class, 'method' => 'postCreateContract', 'mode' => 'raw'])]
public function createContract(Request $request): JsonResponse
```

### Todo
- tests
