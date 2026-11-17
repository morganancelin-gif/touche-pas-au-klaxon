<?php

/**
 * Initialisation des tests PHPUnit.
 *
 * Charge l'autoload de Composer et la configuration (.env) pour que
 * les tests des modèles puissent se connecter à la base de données.
 */

declare(strict_types=1);

use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);

require $rootPath . '/vendor/autoload.php';

Dotenv::createImmutable($rootPath)->safeLoad();
date_default_timezone_set('Europe/Paris');
