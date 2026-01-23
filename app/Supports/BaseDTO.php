<?php

namespace App\Supports;

use UnitEnum;
use ReflectionClass;
use ReflectionNamedType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

abstract class BaseDTO
{
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new static();
        }

        $parameters = $constructor->getParameters();
        $args = [];

        foreach ($parameters as $parameter) {
            $name = $parameter->getName();
            $type = $parameter->getType();

            $snakeName = Str::snake($name);

            $value = $data[$name] ?? $data[$snakeName] ?? null;

            if (is_null($value) && $parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            }

            if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
                $className = $type->getName();

                // 1. Handle Nested DTOs (Recursion)
                if (is_subclass_of($className, self::class) && is_array($value)) {
                    $value = $className::fromArray($value);
                }

                // 2. Handle Enums (Fixes your TypeError)
                elseif (enum_exists($className) && !is_null($value) && !$value instanceof UnitEnum) {
                    if (method_exists($className, 'from')) {
                        $value = $className::from($value);
                    }
                }
            }

            $args[$name] = $value;
        }

        return new static(...$args);
    }

    public static function fromRequest(Request $request): static
    {
        // Use validated() to ensure only clean data enters your DTOs
        return static::fromArray($request->validated());
    }

    /**
     * Convert DTO to array recursively (Handles Nested DTOs and Enums)
     */
    public function toArray(): array
    {
        $array = get_object_vars($this);
        $result = [];

        foreach ($array as $key => $value) {
            $snakeKey = Str::snake($key);

            if ($value instanceof self) {
                $result[$snakeKey] = $value->toArray();
            } elseif ($value instanceof \UnitEnum) {
                $result[$snakeKey] = $value->value ?? $value->name;
            } else {
                $result[$snakeKey] = $value;
            }
        }

        return $result;
    }
}
