<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventManager;
use Cake\Routing\DispatcherFactory;
use Cake\Routing\Router;
use Yaml\Configure\Engine\YamlConfig;

$yaml = new YamlConfig();
if (!file_exists(CONFIG . 'routes.yml')) {
	return false;
}
$routes_file = $yaml->read('routes');

if(isset($routes_file['Routes']['scope'])){
	$scope = $routes_file['Routes']['scope'];
} else {
	$scope = '/';
}

Router::scope($scope, function($routes) {

/*	$routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

	$routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

	$routes->connect('/voyage-sur-mesure/:slug-:id', 
	    ['controller' => 'Offres', 'action' => 'view'],
	    ['_name' => 'view_single_offer', 'pass' => ['id', 'slug'], 'id' => '[0-9]+']);
*/

	$yaml = new YamlConfig();
	$r = $yaml->read('routes');
	unset($r['Routes']['scope']);

	foreach ($r['Routes'] as $k => $rt) {

		if(!isset($rt['action'])) {
			$rt['action'] = 'index';
		}

		if(!isset($rt['args'])) {
			$rt['args'] = [];
		}

		$url = ['controller' => $rt['controller'], 'action' => $rt['action']];

		if (isset($rt['method'])) {
			$url['[method]'] = $rt['method'];
		}

		if(isset($rt['arg'])) {
			array_push($url, $rt['arg']);
		}

		$routes->connect($rt['path'], $url, $rt['args']);

	}
		$routes->fallbacks();
});
