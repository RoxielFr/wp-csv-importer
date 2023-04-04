<?php

namespace Roxielfr\WpPodsImport\Config;

/**
 * Classe Config pour gérer la configuration et les traductions du plugin.
 */
class Config
{
    /**
     * Préfixe utilisé pour les traductions et d'autres éléments du plugin.
     *
     * @var string
     */
    public static string $prefix = 'wp-pods-import';

    /**
     * Chemin du dossier d'upload des excel et /image/.
     *
     * @var string
     */
    public static string $upload_dir ;

    /**
     * Charge les fichiers de traduction pour le plugin.
     *
     * Cette fonction utilise la fonction load_plugin_textdomain() de WordPress pour charger les fichiers de traduction
     * à partir du répertoire 'languages' situé dans le même dossier que ce fichier.
     *
     * @return void
     */
    public static function loadTranslations()
    {
        load_plugin_textdomain(Config::$prefix, false, basename(dirname(__DIR__)) . '/languages');
    }
}