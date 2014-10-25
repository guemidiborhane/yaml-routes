Yaml Routing for __CakePHP 3__
====

implements Yaml Routing which allows you to write routes using yaml language instead of php

## Requirements

The 3.0 branch has the following requirements:

* CakePHP 3.0.0 or greater.

## Installation

* Install the plugin with composer from your CakePHP Project's ROOT directory (where composer.json file is located)
```sh
php composer.phar require chobo1210/yaml-route "dev-master"
```

OR

add this lines to your `composer.json`

```javascript
"require": {
  "chobo1210/yaml-route": "dev-master"
}
```

And run `php composer.phar update`


then add this lines to your `config/bootstrap.php`

```php
Plugin::load('YamlRoute', ['routes' => true]);
```

then create your routes file `config/routes.yml`

##Example 

```yaml
Routes:
    scope: /
    index:
        path: /
        controller: Pages
        action: display
        arg: home
    pages:
        path: /pages/*
        controller: Pages
        action: display
    voyage:
        path: /blog/:slug-:id
        controller: Posts
        action: view
        args:
            _name: view_single_post
            pass:
                - id
                - slug
            id: '[0-9]+'
```

gives this 

```php
Router::scope('/', function($routes) {

	$routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

	$routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

	$routes->connect('/blog/:slug-:id', 
	    ['controller' => 'Posts', 'action' => 'view'],
	    ['_name' => 'view_single_post', 'pass' => ['id', 'slug'], 'id' => '[0-9]+']);

	$routes->fallbacks();
});
```
