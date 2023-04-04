<?php

namespace Roxielfr\WpPodsImport\Helper;

use Roxielfr\WpPodsImport\Config\Config;

class FileHelper
{


    public static function isImageFile(string $filename): bool
    {
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($file_extension, $image_extensions);
    }

    public static function saveImageToMediaLibrary(string $image_filename): int
    {
        $image_file_path = Config::$upload_dir . "/images/{$image_filename}";

        if (!file_exists($image_file_path)) {
            throw new \Exception(sprintf(
                __("Le fichier image n'existe pas : %s", 'wp-csv-importer'),
                $image_filename
            ));
        }

        $file_content = file_get_contents($image_file_path);
        $upload = wp_upload_bits($image_filename, null, $file_content);

        if (!$upload['error']) {
            $file_path = $upload['file'];
            $file_name = basename($file_path);
            $file_type = wp_check_filetype($file_name, null);

            $attachment = [
                'post_mime_type' => $file_type['type'],
                'post_title' => sanitize_file_name($file_name),
                'post_content' => '',
                'post_status' => 'inherit',
            ];

            $attach_id = wp_insert_attachment($attachment, $file_path);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
            wp_update_attachment_metadata($attach_id, $attach_data);

            return $attach_id;

            if (!$attachment_id) {
                throw new \Exception(sprintf(
                    __("Impossible d'enregistrer l'image %s dans la bibliothèque de médias", 'wp-csv-importer'),
                    $image_filename
                ));
            }
        }

        return 0;
    }

    /**
     * Déplace un fichier dans un nouveau dossier.
     *
     * @param string $file_path Chemin du fichier à déplacer.
     * @param string $destination Chemin de destination.
     *
     * @return bool Vrai si le fichier a été déplacé avec succès, sinon faux.
     */
    public static function moveFile(string $file_path, string $destination): bool
    {
        if (!file_exists($file_path)) {
            return false;
        }

        return rename($file_path, $destination);
    }

    /**
     * Supprime un dossier et son contenu.
     *
     * @param string $dir_path Chemin du dossier à supprimer.
     *
     * @return bool Vrai si le dossier a été supprimé avec succès, sinon faux.
     */
    public static function deleteDirectory(string $dir_path): bool
    {
        if (!is_dir($dir_path)) {
            return false;
        }

        $files = array_diff(scandir($dir_path), ['.', '..']);

        foreach ($files as $file) {
            $file_path = $dir_path . DIRECTORY_SEPARATOR . $file;
            is_dir($file_path) ? self::deleteDirectory($file_path) : unlink($file_path);
        }

        return rmdir($dir_path);
    }
}
