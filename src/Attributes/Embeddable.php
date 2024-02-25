<?php

namespace WireElements\WireExtender\Attributes;

use Livewire\Features\SupportAttributes\Attribute as LivewireAttribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Embeddable extends LivewireAttribute
{
}
