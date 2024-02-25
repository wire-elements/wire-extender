<?php

namespace WireElements\WireExtender\Http\Middlewares;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Livewire\LivewireManager;
use WireElements\WireExtender\WireExtender;

trait IgnoreForWireExtender
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     *
     * @throws TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        // We only care about requests from an embedded component
        if ($this->isLivewireUpdateRequest($request)) {
            // Loop through all components that are part of the update
            foreach ($request->json('components', []) as $component) {
                $snapshot = json_decode($component['snapshot'], true);
                $component = $snapshot['memo']['name'] ?? false;

                // All components must be embeddable otherwise we will apply the existing middleware
                if (WireExtender::isEmbeddable($component) === false) {
                    return parent::handle($request, $next);
                }
            }
        }

        return $next($request);
    }

    private function isLivewireUpdateRequest($request): bool
    {
        return $request->method() === 'POST' &&
            app(LivewireManager::class)->getUpdateUri() === $request->getRequestUri() &&
            $request->hasHeader('X-Wire-Extender') &&
            $request->hasHeader('X-Livewire');
    }
}
