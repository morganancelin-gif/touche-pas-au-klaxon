<?php

declare(strict_types=1);

/**
 * Déclaration des routes de l'application.
 *
 * Syntaxe : $router->methodeHttp('/url', 'NomDuController@methode');
 *
 * @var \Buki\Router\Router $router Instancié dans public/index.php.
 */

$router->get('/', 'HomeController@index');