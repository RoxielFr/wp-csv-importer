<?php

namespace Roxielfr\WpPodsImport\FieldType;

use Roxielfr\WpPodsImport\Config\Config;

class StringFieldType implements FieldTypeInterface
{
    public function validate($data): bool
    {
        return is_string($data);
    }

    public function process($data): string|array
    {
        return sanitize_text_field($data);
    }

    public function processField($data, array $field_info): array
    {
        return [$field_info['field_name'] => $data];
    }

    public function validateMapping(array $mapping): void
    {
        if (!isset($mapping['field_name'])) {
            throw new \Exception(__('Il manque la clé field_name dans le mapping StringFieldType.', Config::$prefix));
        }
    }
}