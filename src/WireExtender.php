<?php

namespace WireElements\WireExtender;

use Livewire\Exceptions\ComponentNotFoundException;
use Livewire\Mechanisms\ComponentRegistry;
use ReflectionClass;
use WireElements\WireExtender\Attributes\Embeddable;

class WireExtender
{
    public static function isEmbeddable($component): bool
    {
        try {
            $reflectionClass = new ReflectionClass(app(ComponentRegistry::class)->new($component));
            $embedAttribute = $reflectionClass->getAttributes(Embeddable::class)[0] ?? null;

            return is_null($embedAttribute) === false;
        } catch (ComponentNotFoundException $e) {
            return false;
        }

        return true;
    }
}
