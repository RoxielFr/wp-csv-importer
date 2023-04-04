<?php

namespace Roxielfr\WpPodsImport\FieldType;

/**
 * Interface FieldTypeInterface
 *
 * @package Roxielfr\WpPodsCsvImporter\FieldTypes
 */
interface FieldTypeInterface
{

     /**
     * Valide la donnée pour l'enregistrement pods.
     *
     * @param string $value La valeur du champ.
     *
     * @return void.
     */

    public function validate($value);

     /**
     * Construit la donnée.
     *
     * @param string $value La valeur du champ excel.
     *
     * @return string|array.
     */

    public function process($value) : string|array;

    /**
     * Construit la donnée pour l'enregistrement pods.
     *
     * @param array $data Les valeurs du champ construit.
     * @param array $field_info Les informations du champ.
     *
     * @return array Tableau des données.
     */

    public function processField($data, array $field_info): array;

    /**
     * Valide le mapping du champ pour le fichier CSV.
     *
     * @param array $mapping Le mapping du champ.
     *
     * @return void.
     */
    public function validateMapping(array $mapping): void;
}