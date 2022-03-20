<?php

namespace App\Dto;

class Dto
{
    /**
     * @param array $values
     * @return $this
     */
    public function attachValues(array $values): Dto
    {
        foreach ($values as $property => $propertyValue) {
            if (property_exists($this, $property)) {
                $this->{$property} = $propertyValue;
            }
        }

        return $this;
    }

    /**
     * @param bool $snakeCase
     * @return array
     */
    public function toArray(bool $snakeCase = false): array
    {
        $arrayToReturn = [];
        $classProperties = get_class_vars(get_class($this));
        foreach ($classProperties as $classProperty => $value) {
            $propertyValue = $this->{$classProperty};
            if (is_null($propertyValue)) {
                continue;
            }
            $classProperty = $snakeCase
                ? $this->toSnakeCase($classProperty)
                : $classProperty;

            $arrayToReturn[$classProperty] = $propertyValue;
        }

        return $arrayToReturn;
    }

    /**
     * @param string $string
     * @return string
     */
    protected function toSnakeCase(string $string): string
    {
        return ltrim(strtolower(preg_replace('/[A-Z]/', '_$0', $string )), '_');
    }
}
