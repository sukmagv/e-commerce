<?php

namespace App\Supports;

use Illuminate\Http\Request;
use ReflectionClass;
use ReflectionProperty;

abstract class BaseDTO
{
    /**
     * Create DTO from Array
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $dto = new static();
        $reflection = new ReflectionClass($dto);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            if (!array_key_exists($name, $data)) {
                continue;
            }

            $value = $data[$name];
            $type  = $property->getType()?->getName();

            if (!$type) {
                $dto->$name = $value;
                continue;
            }

            // If property is Enum
            if (enum_exists($type)) {
                $dto->$name = $value !== null ? $type::from($value) : null;
                continue;
            }

            // If property is nested DTO
            if (class_exists($type) && is_subclass_of($type, BaseDTO::class)) {
                $dto->$name = $type::fromArray($value);
                continue;
            }

            $dto->$name = $value;
        }

        return $dto;
    }

    /**
     * Create DTO from Request
     *
     * @param Request $request
     * @return static
     */
    public static function fromRequest(Request $request): static
    {
        return static::fromArray($request->all());
    }

    public function toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $data = [];

        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();

            if (!$property->isInitialized($this)) {
                continue;
            }

            $value = $this->$name;

            // Enum
            if ($value instanceof \UnitEnum) {
                $data[$name] = $value->value;
                continue;
            }

            // Nested DTO
            if ($value instanceof BaseDTO) {
                $data[$name] = $value->toArray();
                continue;
            }

            $data[$name] = $value;
        }

        return $data;
    }

}
