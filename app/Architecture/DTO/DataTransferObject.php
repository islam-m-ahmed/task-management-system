<?php

namespace App\Architecture\DTO;

use Illuminate\Http\Request;

abstract class DataTransferObject
{
    /**
     * DTO Constructor
     */
    public function __construct(array $parameters = [])
    {
        $class = new \ReflectionClass(static::class);

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            if (isset($parameters[$property])) {
                $this->{$property} = $parameters[$property];
            }
        }
    }

    /**
     * Create DTO from Request
     */
    public abstract static function fromRequest(Request $request): self;

    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $class = new \ReflectionClass(static::class);
        $array = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $property = $reflectionProperty->getName();
            if (isset($this->{$property})) {
                $array[$property] = $this->{$property};
            }
        }
        return $array;
    }
}
