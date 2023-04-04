<?php

namespace Roxielfr\WpPodsImport\FieldType;

use Roxielfr\WpPodsImport\Config\Config;

class TaxonomyFieldType implements FieldTypeInterface
{
    public function validate($data): bool
    {
        return is_string($data);
    }

    public function process($data): string|array
    {
        $taxonomies = array_map('trim', explode(',', $data));
        return array_map('sanitize_text_field', $taxonomies);
    }

    public function processField($data, array $field_info): array
    {   
        $taxonomy_name = $field_info['taxonomy_name'];
        $term_ids = $this->processRelationship($taxonomy_name, $data);

        return [$field_info['field_name'] => $term_ids];
    }

    public function processRelationship($taxonomy, array $term_names): array
    {
        $term_ids = [];
        foreach ($term_names as $term_name) {
            $term = get_term_by('name', $term_name, $taxonomy);

            if ($term) {
                $term_ids[] = $term->term_id;
            }else{
                $term_taxonomy_id = wp_insert_term($term_name,$taxonomy);
                if($term_taxonomy_id){
                    $term_ids[] = $term_taxonomy_id;
                }
            }
        }

        return $term_ids;
    }

    public function validateMapping(array $mapping): void
    {
        if (!isset($mapping['field_name'])) {
            throw new \Exception(__('Il manque la clé field_name dans le mapping TaxonomyFieldType.', Config::$prefix));
        }

        if (!isset($mapping['taxonomy_name'])) {
            throw new \Exception(__('Il manque la clé taxonomy_name dans le mapping TaxonomyFieldType.', Config::$prefix));
        }
    }
}