<?php

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\EventManager;
use Cake\Routing\DispatcherFactory;
use Cake\Routing\Router;
use Yaml\Configure\Engine\YamlConfig;

$items = [];

$yaml = new YamlConfig();
if (!file_exists(CONFIG . 'routes.yml')) {
    return false;
}
$items[] = ['file' => 'routes'];
foreach (Configure::read('plugins') as $key => $value) {
    if (!in_array($key, ['Bake', 'YamlRoute', 'Yaml'])) {
        $items[] = ['file' => $key . '.' . 'routes', 'plugin' => $key];
    }
}
foreach ($items as $item) {
    if (isset($item['plugin'])) {
        $res = Cake\Core\Plugin::routes($item['plugin']);
        if (!$res) {
            continue;
        }
    }
    $routes_file = $yaml->read($item['file']);

    if (isset($routes_file['Routes']['scope'])) {
        $scope = $routes_file['Routes']['scope'];
    } else {
        $scope = '/';
    }

    // If isset plugin, use plugin method, set different scope and set path
    if (isset($routes_file['Routes']['plugin'])) {
        $method = 'plugin';
        $scope = $routes_file['Routes']['plugin'];
        $options = [
            'path' => $routes_file['Routes']['scope'],
        ];
    } else {
        $method = 'scope';
        $options = [];
    }

    Router::$method($scope, $options, function ($routes) use ($item) {

        $yaml = new YamlConfig();
        $r = $yaml->read($item['file']);
        unset($r['Routes']['scope']);

        if (isset($r['Routes']['extensions'])) {
            $routes->extensions($r['Routes']['extensions']);
        }

        foreach ($r['Routes'] as $k => $rt) {
            if (in_array($k, ['plugin', 'extensions'])) {
                continue;
            }

            if (!isset($rt['action'])) {
                $rt['action'] = 'index';
            }

            if (!isset($rt['args'])) {
                $rt['args'] = [];
            }

            $url = ['controller' => $rt['controller'], 'action' => $rt['action']];

            // Additional values
            foreach ($rt as $key => $value) {
                if (!array_key_exists($key, ['controller', 'action', 'plugin', 'path', 'args'])) {
                    $url[$key] = $value;
                }
            }

            if (isset($rt['method'])) {
                $url['[method]'] = $rt['method'];
            }

            if (isset($rt['arg'])) {
                array_push($url, $rt['arg']);
            }

            $routes->connect($rt['path'], $url, $rt['args']);
        }

        $routes->fallbacks('DashedRoute');
    });

}