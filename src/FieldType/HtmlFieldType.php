<?php

namespace Roxielfr\WpPodsImport\FieldType;

use Roxielfr\WpPodsImport\Config\Config;

class HtmlFieldType implements FieldTypeInterface
{
    public function validate($data): bool
    {
        return is_string($data);
    }

    public function process($data): string|array
    {
        return wp_kses_post($data);
    }

    public function processField($data, array $field_info): array
    {
        return [$field_info['field_name'] => $data];
    }

    /**
     * Valide le mappage pour le type de champ spécifique.
     *
     * @param mixed $mapping_value La valeur de mappage à valider.
     * @return array Un tableau contenant les messages d'erreur, s'il y en a.
     */
    public function validateMapping(array $mapping): void
    {
        if (!isset($mapping['field_name'])) {
            throw new \Exception(__('Il manque la clé field_name dans le mapping HtmlFieldType.', Config::$prefix));
        }
    }
}