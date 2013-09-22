DaApiClientBundle
=================

DaApiClientBundle is a Symfony2's bundle allowing to discuss in a simple and secure way with an API.


Installation
------------

Installation is a quick 2 steps process.


### Step 1: Add in composer

Add the bundle in the composer.json file:

```json
// composer.json

"require": {
    // ...
    "da/api-client-bundle": "dev-master"
},
```

Update your vendors with composer:

```sh
composer update      # WIN
composer.phar update # LINUX
```


### Step 2: Declare in the kernel

Declare the bundle in your kernel:

```php
// app/AppKernel.php

$bundles = array(
    // ...
    new Da\ApiClientBundle\DaApiClientBundle(),
);
```


Configuration to use your API
-----------------------------

```yaml
# app/config/config.yml

da_api_client:
    api:
        my_api_name:
            base_url:      'https://my-domain/api'
            api_token:     3e90o0xrzy4gsw4k0440sw4k4g8oog0ckoo4okgogs0wowo4sg
            cache_enabled: true
```


How to use
----------

```php
try {
    $api = $container->get('da_api_client.api.my_api_name')
    $parameters = array('offset' => 0, 'limit' => 20);
    $friends = $api->get('/friends', $parameters);
} catch (ApiCallException $e) {
    switch ($e->getHttpCode()) {
        // Handle specific http error code here.
        case '404':
            // ...
            break;
    }
}
```

You can use all basic REST methods in the same way:

```php
$friends = $api->get('/friends', array(...));
$friend = $api->post('/friends/add', array(...));
$friend = $api->put('/friends/update', array(...));
$status = $api->delete('/friends/remove', array(...));
```


Documentation
-------------

You can do a lot more than that! Check the full [documentation](https://github.com/Gnuckorg/DaApiClientBundle/blob/master/Resources/doc/index.md)!


What about the API server side?
-------------------------------

Take a look at the [DaApiServerBundle](https://github.com/Gnuckorg/DaApiServerBundle)!

