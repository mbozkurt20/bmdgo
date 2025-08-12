<?php

namespace App\Observers;

use App\Jobs\AssignOrderToCourier;
use App\Models\Courier;
use App\Models\Order;

class CourierObserver
{
    /**
     * Handle the Courier "created" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function created(Courier $courier)
    {
        //
    }

    /**
     * Handle the Courier "updated" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function updated(Courier $courier)
    {
        if ($courier->status === 'active') {
            $order = Order::whereNull('courier_id')
                ->where('status', 'pending')
                ->orderBy('created_at')
                ->first();

            if ($order) {
                dispatch(new AssignOrderToCourier($order));
            }
        }
    }

    /**
     * Handle the Courier "deleted" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function deleted(Courier $courier)
    {
        //
    }

    /**
     * Handle the Courier "restored" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function restored(Courier $courier)
    {
        //
    }

    /**
     * Handle the Courier "force deleted" event.
     *
     * @param  \App\Models\Courier  $courier
     * @return void
     */
    public function forceDeleted(Courier $courier)
    {
        //
    }
}
