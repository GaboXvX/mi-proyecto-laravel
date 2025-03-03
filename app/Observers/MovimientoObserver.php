<?php

namespace App\Observers;

use App\Models\persona;

class MovimientoObserver
{
    /**
     * Handle the persona "created" event.
     */
    public function created(persona $persona): void
    {
        //
    }

    /**
     * Handle the persona "updated" event.
     */
    public function updated(persona $persona): void
    {
        //
    }

    /**
     * Handle the persona "deleted" event.
     */
    public function deleted(persona $persona): void
    {
        //
    }

    /**
     * Handle the persona "restored" event.
     */
    public function restored(persona $persona): void
    {
        //
    }

    /**
     * Handle the persona "force deleted" event.
     */
    public function forceDeleted(persona $persona): void
    {
        //
    }
}
