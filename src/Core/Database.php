<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Fournit une connexion PDO unique à la base de données (pattern Singleton).
 *
 * Les paramètres de connexion sont lus dans les variables d'environnement,
 * chargées depuis le fichier .env par public/index.php.
 */
final class Database
{
    /**
     * Connexion partagée, créée lors du premier appel à getConnection().
     *
     * @var PDO|null
     */
    private static ?PDO $connection = null;

    /**
     * Constructeur privé : cette classe ne s'instancie pas.
     */
    private function __construct()
    {
    }

    /**
     * Retourne la connexion PDO, en la créant au premier appel.
     *
     * @return PDO
     *
     * @throws RuntimeException Si une variable d'environnement manque ou si la connexion échoue.
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                self::env('DB_HOST'),
                self::env('DB_PORT', '3306'),
                self::env('DB_NAME')
            );

            try {
                self::$connection = new PDO($dsn, self::env('DB_USER'), self::env('DB_PASSWORD', ''), [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                // Le message d'origine peut contenir des informations sensibles
                // (hôte, utilisateur) : on le garde en "previous" sans l'afficher.
                throw new RuntimeException('Connexion à la base de données impossible.', 0, $e);
            }
        }

        return self::$connection;
    }

    /**
     * Remplace la connexion partagée (utilisé par les tests unitaires).
     *
     * @param PDO|null $pdo Nouvelle connexion, ou null pour la réinitialiser.
     *
     * @return void
     */
    public static function setConnection(?PDO $pdo): void
    {
        self::$connection = $pdo;
    }

    /**
     * Lit une variable d'environnement.
     *
     * @param string      $key     Nom de la variable.
     * @param string|null $default Valeur par défaut si la variable est absente.
     *
     * @return string
     *
     * @throws RuntimeException Si la variable est absente et sans valeur par défaut.
     */
    private static function env(string $key, ?string $default = null): string
    {
        $value = $_ENV[$key] ?? $default;

        if ($value === null) {
            throw new RuntimeException("Variable d'environnement manquante : {$key}");
        }

        return (string) $value;
    }
}