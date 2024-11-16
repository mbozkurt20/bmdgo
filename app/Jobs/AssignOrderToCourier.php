<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use App\Models\Courier;
use App\Models\CourierOrder;
use Illuminate\Support\Facades\Log;

class AssignOrderToCourier implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $order;
    protected $retryCount;


    public function __construct(Order $order, $retryCount = 1)
    {
        $this->order = $order;
        $this->retryCount = $retryCount;
    }
    /////////////////////////////////////// Handle eski kodu ///////////////////////////////////////
    // Log::info("kuyruğa mesaj geldi!");
    // $courier = Courier::where('situation','Aktif')->first();

    // if($courier){

    //         $ordercourier = CourierOrder::where('order_id',$this->order->id)->first();
    //         if(!$ordercourier){

    //             $new = new CourierOrder();
    //             $new->courier_id = $courier->id;
    //             $new->order_id = $this->order->id;
    //             $new->save();

    //             $courier->situation = "Serviste";
    //             $courier->save();

    //         }
    //   }else {
    //     if ($this->retryCount < 5){
    //         AssignOrderToCourier::dispatch($this->order, $this->retryCount + 1)->onQueue('default')->delay(now()->addMinutes(10))->priority(10);
    //     }
    //   }
    public function handle()
    {

        Log::info("Job kuyruğa geldi! Sipariş ID: " . $this->order->id);

        // Durumu 'Aktif' olan ilk kuryeyi bul
        $courier = Courier::where('situation', 'Aktif')->where('admin_id', $this->order->restaurant->admin_id)->first();

        if ($courier) {
            // Eğer sipariş daha önce bir kuryeye atanmadıysa
            $orderCourier = CourierOrder::where('order_id', $this->order->id)->first();

            if (!$orderCourier) {
                // Yeni siparişi kuryeye atama
                $newOrderCourier = new CourierOrder();
                $newOrderCourier->courier_id = $courier->id;
                $newOrderCourier->order_id = $this->order->id;
                $newOrderCourier->save();

                // Kuryenin durumunu 'Serviste' yap
                $courier->situation = 'Serviste';
                $courier->save();

                // Sipariş tablosundaki courier_id'yi güncelle
                $this->order->courier_id = $courier->id;
                $this->order->save();

                Log::info("Kurye atandı ve durumu Serviste yapıldı. Sipariş ID: " . $this->order->id . " Kurye ID: " . $courier->id);
            }
        } else {
            // Eğer aktif kurye bulunamazsa ve tekrar deneme sayısı 5'ten azsa kuyruğa tekrar ekleyelim
            if ($this->retryCount < 5) {
                AssignOrderToCourier::dispatch($this->order, $this->retryCount + 1)
                    ->onQueue('default')
                    ->delay(now()->addMinutes(10))->priority(10); //;
                Log::info("Kurye bulunamadı, tekrar kuyruğa ekleniyor. Deneme sayısı: " . $this->retryCount);
            }
        }

        // Sipariş durumu kontrolü
        if ($this->order->status == 'DELIVERED' || $this->order->status == 'UNSUPPLIED') {
            // Sipariş kuryesini bul
            $orderCourier = CourierOrder::where('order_id', $this->order->id)->first();

            if ($orderCourier) {
                $courier = Courier::find($orderCourier->courier_id);

                if ($courier && $courier->situation == 'Serviste') {
                    // Eğer sipariş tamamlandı ya da iptal edildiyse kuryeyi aktif yap
                    $courier->situation = 'Aktif';
                    $courier->save();

                    Log::info("Sipariş durumu değişti, kuryenin durumu Aktif yapıldı. Kurye ID: " . $courier->id);
                }
            }
        }
    }
}
