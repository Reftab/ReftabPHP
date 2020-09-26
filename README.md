ReftabPHP
=============

This is a quick and dirty module to interact with the Reftab API via PHP.

# Instructions

### Download repository

Use the [Reftab API documentation](https://www.reftab.com/api-docs) to find endpoints to access.

### Methods
The ReftabClient has 4 methods available, get, post, put, delete, corresponding to the HTTP methods.

Parameters each take:
* get(endpoint, id)
  * endpoint (e.g. assets, optional parameters may be added such as ?limit=200 to get additional assets)
  * id (default null)
* post(endpoint, body)
  * endpoint (e.g. assets)
  * body (an object which will be converted to json)
* put(endpoint, id, body)
  * endpoint (e.g. assets)
  * id (required)
  * body (an object which will be converted to json)
* delete(endpoint, id)
  * endpoint (e.g. assets)
  * id (required)

### Prerequisites

* PHP 7.0 or later
* A valid API key pair from Reftab
  * Generate one in Reftab Settings
  
# Examples

### Get an Asset and Update It

```php
#This example shows how to get an asset and update it

<?php

require_once './src/ReftabClient.php';

$api = new Reftab\ReftabClient([
  'publicKey' => '[publicKey]',
  'secretKey' => '[secretKey]',
]);

$asset = $api->get('assets', 'NY00');

$asset->title = 'New Title';

$api->put('assets', 'NY00', $asset);
```