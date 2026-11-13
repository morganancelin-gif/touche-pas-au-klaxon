<?php

/**
 * Front controller : point d'entrée unique de l'application.
 *
 * Toutes les requêtes HTTP passent par ce fichier, qui charge la
 * configuration puis confie la requête au routeur.
 */

declare(strict_types=1);

use App\Core\Session;
use Buki\Router\Router;
use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);

require $rootPath . '/vendor/autoload.php';

// 1. Configuration : variables d'environnement lues dans le fichier .env
$dotenv = Dotenv::createImmutable($rootPath);
$dotenv->load();
$dotenv->required(['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD']);

$debug = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';

error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
date_default_timezone_set('Europe/Paris');

// 2. Session (utilisateur connecté, messages flash, jeton CSRF)
Session::start();

// 3. Routeur
$router = new Router([
    'base_folder' => __DIR__,
    'paths'       => [
        'controllers' => $rootPath . '/src/Controllers',
        'middlewares' => $rootPath . '/src/Middlewares',
    ],
    'namespaces'  => [
        'controllers' => 'App\Controllers',
        'middlewares' => 'App\Middlewares',
    ],
    'debug'       => $debug,
]);

require $rootPath . '/config/routes.php';

// 4. Pages d'erreur (le détail technique n'est jamais montré à l'utilisateur)
$router->notFound(function (): string {
    return '<h1>Page introuvable</h1><p><a href="/">Retour à l\'accueil</a></p>';
});

$router->error(function (): string {
    return '<h1>Une erreur est survenue</h1><p>Merci de réessayer plus tard.</p>';
});

$router->run();