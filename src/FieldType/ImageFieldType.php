<?php

namespace Roxielfr\WpPodsImport\FieldType;

use Roxielfr\WpPodsImport\Config\Config;

use Roxielfr\WpPodsImport\Helper\FileHelper;

class ImageFieldType implements FieldTypeInterface
{
    public function validate($data): bool
    {
        return FileHelper::isImageFile($data);
    }

    public function process($data): string|array
    {
        return FileHelper::saveImageToMediaLibrary($data);
    }

    public function processField($data, array $field_info): array
    {
        return [$field_info['field_name'] => $data];
    }

    public function validateMapping(array $mapping): void
    {
        if (!isset($mapping['field_name'])) {
            throw new \Exception(__('Il manque la clé field_name dans le mapping HtmlFieldType.', Config::$prefix));
        }
    }
}
