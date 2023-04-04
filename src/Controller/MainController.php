<?php

namespace Roxielfr\WpPodsImport\Controller;

use Roxielfr\WpPodsImport\Config\Config;

use roxielfr\WpPodsImport\FieldType\FieldTypeInterface;
use Roxielfr\WpPodsImport\FieldType\StringFieldType;
use Roxielfr\WpPodsImport\FieldType\TaxonomyFieldType;
use Roxielfr\WpPodsImport\FieldType\HtmlFieldType;
use Roxielfr\WpPodsImport\FieldType\ImageFieldType;

use Roxielfr\WpPodsImport\Trait\HasLoggerTrait;

use Roxielfr\WpPodsImport\Helper\FileHelper;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class MainController
{
    use HasLoggerTrait;
    private array $custom_field_types = [];

    public function import(string $post_type, string $upload_directory, string $excel_file_name, array $column_mappings): array
    {   

        Config::$upload_dir = $upload_directory;

        $this->setLogger(Config::$prefix);

        $import_results = [
            'success' => [],
            'errors' => [],
        ];

        $file_path = $upload_directory . $excel_file_name;

        $this->logger->info(sprintf(__('Début de l\'importation du fichier %s', Config::$prefix), $file_path));

       // Vérifier si le fichier existe
        if (!file_exists($file_path)) {
            $this->logger->error(sprintf(__('Le fichier %s n\'existe pas', Config::$prefix), $file_path));
            $import_results['errors'][] = sprintf(__('Le fichier %s n\'existe pas', Config::$prefix), $file_path);
            return $import_results;
        }
        $rows = $this->readExcelFile($file_path);

        

        if (empty($rows)) {
             $this->logger->error(sprintf(__('Aucune donnée dans le fichier Excel.', Config::$prefix)));
            $import_results['errors'][] = __('Aucune donnée dans le fichier Excel.', Config::$prefix);
            return $import_results;
        }

        foreach ($rows as $row_index => $row) {
            $post_data = [];

            foreach ($column_mappings as $column_name => $field_info) {
                $field_type = $field_info['field_type'];

                try {
                    $field_instance = $this->getFieldTypeInstance($field_type);
                    $field_instance->validateMapping($field_info);
                } catch (\Exception $e) {
                    $error_message = sprintf(
                        __('Error at row %d, column %s: %s', Config::$prefix),
                        $row_index + 1,
                        $column_name,
                        $e->getMessage()
                    );
                    $this->logger->error($error_message);
                    $import_results['errors'][] = $error_message;
                    continue;
                }

                try {
                    if ($field_instance->validate($row[$column_name])) {
                        $processed_data = $field_instance->process($row[$column_name]);
                        $field_data = $field_instance->processField($processed_data, $field_info);
                        $post_data = array_merge($post_data, $field_data);
                    } else {
                        $error_message = sprintf(
                            __('Donnée invalide ligne %d, colonne %d: %s', Config::$prefix),
                            $row_index + 1,
                            $column_name,
                            $row[$column_name]
                        );
                        $this->logger->error($error_message);
                        $import_results['errors'][] = $error_message;
                    }
                } catch (\Exception $e) {
                    $error_message = sprintf(
                        __('Donnée invalide ligne %d, colonne %d: %s - %s', Config::$prefix),
                        $row_index + 1,
                        $column_name,
                        $row[$column_name],
                        $e->getMessage()
                    );
                    $this->logger->error($error_message);
                    $import_results['errors'][] = $error_message;
                }
            }

            $post_data['post_status'] = 'publish';

            try {
                $pod = pods($post_type);
                $post_id = $pod->add($post_data);
            } catch (\Exception $e) {
                $error_message = sprintf(
                    __('Erreur ligne %d, colonne %s: %s', Config::$prefix),
                    $row_index + 1,
                    $column_name,
                    $e->getMessage()
                );
                $this->logger->error($error_message);
                $import_results['errors'][] = $error_message;
                continue;
            }

            

            if ($post_id) {
                $success_message = sprintf(
                    __('Importation de la ligne %d réussi (post ID %d).', Config::$prefix),
                    $row_index + 1,
                    $post_id
                );
                $import_results['success'][$row_index + 1] = $success_message;
            } else {
                $error_message = sprintf(
                    __('Imporssible d\'importer la ligne %d.', Config::$prefix),
                    $row_index + 1
                );
                $this->logger->error($error_message);
                $import_results['errors'][] = $error_message;
            }
        }
        

        // Déplace le fichier Excel traité dans le sous-dossier "success" et le renomme avec la date du jour
        $date = date('Y-m-d');
        $processed_dir = $upload_directory . DIRECTORY_SEPARATOR . 'success';
        if (!file_exists($processed_dir)) {
            mkdir($processed_dir);
        }
        $excel_file_extension = pathinfo($excel_file_name,PATHINFO_EXTENSION);
        $processed_file = $processed_dir . DIRECTORY_SEPARATOR . basename($excel_file_name, '.' . $excel_file_extension) . '-' . $date . '.' . $excel_file_extension;
        FileHelper::moveFile($upload_directory . $excel_file_name, $processed_file);

        // Supprime le dossier "images" après l'importation
        $images_dir = $upload_directory . DIRECTORY_SEPARATOR . 'images';
        FileHelper::deleteDirectory($images_dir);

        $this->logger->info(sprintf(__('Fin de l\'importation du fichier %s', Config::$prefix), $file_path));

        return $import_results;
    }

    private function readExcelFile(string $excel_file_path): array
    {
        try {
            $spreadsheet = IOFactory::load($excel_file_path);
        } catch (ReaderException $e) {
            $this->logger->error(sprintf(__('impossible de charger le fichier Excel : %s', Config::$prefix), $e->getMessage()));
            throw sprintf(
                __('Failed to load Excel file: %s', Config::$prefix),
                $e->getMessage()
            );
        }

        $worksheet = $spreadsheet->getActiveSheet();
        $rows = [];
        $header = [];

        foreach ($worksheet->toArray() as $rowIndex => $row) {
            if ($rowIndex === 0) {
                $header = $row;
                continue;
            }
            $rows[] = array_combine($header, $row);
        }

        return $rows;
    }

    private function getFieldTypeInstance(string $field_type): FieldTypeInterface
    {
        $field_types = [
            'string' => StringFieldType::class,
            'html' => HtmlFieldType::class,
            'image' => ImageFieldType::class,
            'taxonomy' => TaxonomyFieldType::class,
        ];

        $field_types = array_merge($field_types, $this->custom_field_types);

        if (!isset($field_types[$field_type])) {
            throw new \Exception(sprintf(__('Type de champ invalide : %s', Config::$prefix), $field_type));
        }

        return new $field_types[$field_type];
    }

    public function registerFieldType(string $field_type, string $class_name): void
    {
        if (!class_exists($class_name) || !in_array(FieldTypeInterface::class, class_implements($class_name))) {
            throw new \Exception(sprintf(__('Classe FieldType invalide : %s', Config::$prefix), $class_name));
        }

        $this->custom_field_types[$field_type] = $class_name;
    }
}