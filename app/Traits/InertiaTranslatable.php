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

        if (method_exists($this, 'getTranslatableAttributes')) {
            foreach ($this->getTranslatableAttributes() as $field) {
                $attributes[$field] = $this->getTranslation($field, app()->getLocale());
            }
        }

        return $attributes;
    }
}
