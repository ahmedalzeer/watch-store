<?php

namespace App\Traits;

trait InertiaTranslatable
{
    /**
     * Convert the model instance to an array.
     * Overriding to ensure translatable fields return only the current locale's value for Inertia.
     *
     * @return array
     */
    public function toArray()
    {
        $attributes = parent::toArray();

        // For administrative areas, we want to see all translations (for editing)
        if (request()->is('vendor/*') || request()->is('admin/*')) {
            return $attributes;
        }

        if (isset($this->translatable) && is_array($this->translatable)) {
            foreach ($this->translatable as $field) {
                // Disable fallback to other locales to remain strict per-language
                $attributes[$field] = $this->getTranslation($field, app()->getLocale(), false);
            }
        }

        return $attributes;
    }
}
