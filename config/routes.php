<?php

declare(strict_types=1);

/**
 * Déclaration des routes de l'application.
 *
 * Syntaxe : $router->methodeHttp('/url', 'NomDuController@methode', ['before' => middleware(s)]);
 *
 * Middlewares disponibles (src/Middlewares) :
 * - CsrfMiddleware  : vérifie le jeton CSRF (toutes les routes POST) ;
 * - AuthMiddleware  : réserve la route aux utilisateurs connectés ;
 * - AdminMiddleware : réserve la route aux administrateurs.
 *
 * @var \Buki\Router\Router $router Instancié dans public/index.php.
 */

// Accueil
$router->get('/', 'HomeController@index');

// Authentification
$router->get('/login', 'AuthController@loginForm');
$router->post('/login', 'AuthController@login', ['before' => 'CsrfMiddleware']);
$router->post('/logout', 'AuthController@logout', ['before' => ['CsrfMiddleware', 'AuthMiddleware']]);

// Trajets (utilisateurs connectés)
$router->get('/trajet/creer', 'TrajetController@create', ['before' => 'AuthMiddleware']);
$router->post('/trajet/creer', 'TrajetController@store', ['before' => ['CsrfMiddleware', 'AuthMiddleware']]);
$router->get('/trajet/:id/modifier', 'TrajetController@edit', ['before' => 'AuthMiddleware']);
$router->post('/trajet/:id/modifier', 'TrajetController@update', ['before' => ['CsrfMiddleware', 'AuthMiddleware']]);
$router->post('/trajet/:id/supprimer', 'TrajetController@delete', ['before' => ['CsrfMiddleware', 'AuthMiddleware']]);