<?php

namespace WireElements\WireExtender\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportScriptsAndAssets\SupportScriptsAndAssets;
use WireElements\WireExtender\WireExtender;

class EmbedController
{
    public function __invoke(Request $request)
    {
        $components = collect($request->json('components', []))->mapWithKeys(function ($component) {
            $componentName = $component['name'];
            $componentParams = json_decode($component['params'], true);

            if (WireExtender::isEmbeddable($componentName) === false) {
                return [$componentName => null];
            }

            return [
                $componentName => Blade::render('@livewire($component, $params)', [
                    'component' => $componentName,
                    'params' => $componentParams,
                ]),
            ];
        })->filter();

        return [
            'components' => $components,
            'assets' => SupportScriptsAndAssets::getAssets(),
        ];
    }
}
