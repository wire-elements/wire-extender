<?php

use WireElements\WireExtender\Http\Controllers\EmbedController;

Route::any('livewire/embed', EmbedController::class)
    ->name('wire-extender.embed');
