<?php

namespace App\Observers;

use App\Models\Admin;
use App\Models\AdminSystemFeature;
use Illuminate\Support\Facades\Auth;

class AdminSystemFeautureObserver
{
    /**
     * Handle the AdminSystemFeature "created" event.
     *
     * @param  \App\Models\AdminSystemFeature  $adminSystemFeature
     * @return void
     */
    public function created(AdminSystemFeature $adminSystemFeature)
    {
        switch ($adminSystemFeature->system_feature_id){
            case 1: //paket gelince bildri
                break;
            case 2: // Paket Otomatik Atansın
                $auto = Admin::where('id', $adminSystemFeature->admin_id)->first();
                $auto->auto_orders = 1;
                $auto->update();
                break;
            case 3: //Kurye Paket Statüleri Bildir
                break;
            case 4: //Kurye Paket İptal Edebilsin
                break;
            case 5: //Kurye Boş Paketleri Görebilsin
                break;
        }
    }

    /**
     * Handle the AdminSystemFeature "deleted" event.
     *
     * @param  \App\Models\AdminSystemFeature  $adminSystemFeature
     * @return void
     */
    public function deleted(AdminSystemFeature $adminSystemFeature)
    {
        switch ($adminSystemFeature->system_feature_id){
            case 1: //paket gelince bildri
                break;
            case 2: // Paket Otomatik Atansın
                $auto = Admin::where('id', $adminSystemFeature->admin_id)->first();
                $auto->auto_orders = 0;
                $auto->update();
                break;
            case 3: //Kurye Paket Statüleri Bildir
                break;
            case 4: //Kurye Paket İptal Edebilsin
                break;
            case 5: //Kurye Boş Paketleri Görebilsin
                break;
        }
    }

    /**
     * Handle the AdminSystemFeature "force deleted" event.
     *
     * @param  \App\Models\AdminSystemFeature  $adminSystemFeature
     * @return void
     */
    public function forceDeleted(AdminSystemFeature $adminSystemFeature)
    {
        //
    }
}
