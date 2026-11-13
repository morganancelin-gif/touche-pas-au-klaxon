<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;
use Throwable;

/**
 * Contrôleur de base : rendu des vues et redirections.
 *
 * Tous les contrôleurs de l'application héritent de cette classe.
 */
abstract class Controller
{
    /**
     * Dossier contenant les vues.
     */
    private const VIEWS_PATH = __DIR__ . '/../../views/';

    /**
     * Rend une vue à l'intérieur du layout commun (header, footer).
     *
     * @param string               $view Chemin de la vue, sans extension (ex. "home/index").
     * @param array<string, mixed> $data Variables transmises à la vue.
     *
     * @return string Le HTML de la page complète.
     */
    protected function render(string $view, array $data = []): string
    {
        $content = $this->renderFile($view, $data);

        return $this->renderFile('layout', array_merge($data, ['content' => $content]));
    }

    /**
     * Redirige vers une autre URL et arrête le script.
     *
     * @param string $url URL de destination (ex. "/").
     *
     * @return never
     */
    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

        /**
     * Lit un champ texte envoyé en POST, de façon défensive.
     *
     * Retourne une chaîne vide si le champ est absent ou n'est pas une chaîne
     * (un attaquant peut envoyer un tableau à la place d'un texte).
     *
     * @param string $key  Nom du champ.
     * @param bool   $trim Supprimer les espaces en début et fin (false pour un mot de passe).
     *
     * @return string
     */
    protected function input(string $key, bool $trim = true): string
    {
        $value = $_POST[$key] ?? '';

        if (!is_string($value)) {
            return '';
        }

        return $trim ? trim($value) : $value;
    }

    /**
     * Exécute un fichier de vue et retourne le HTML produit.
     *
     * @param string               $view Chemin de la vue, sans extension.
     * @param array<string, mixed> $data Variables transmises à la vue.
     *
     * @return string
     *
     * @throws RuntimeException Si le fichier de vue n'existe pas.
     */
    private function renderFile(string $view, array $data): string
    {
        $file = self::VIEWS_PATH . $view . '.php';

        if (!is_file($file)) {
            throw new RuntimeException("Vue introuvable : {$view}");
        }

        // EXTR_SKIP : une donnée ne peut pas écraser $file ou $view.
        extract($data, EXTR_SKIP);

        ob_start();
        try {
            require $file;
        } catch (Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        return (string) ob_get_clean();
    }
}