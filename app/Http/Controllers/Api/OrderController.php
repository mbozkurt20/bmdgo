<?php
namespace App\Http\Controllers\Api;

use App\Helpers\OrdersHelper;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Jobs\AssignOrderToCourier;

class OrderController extends Controller
{
    public function addOrder(Request $request)
    {
        Log::info('Gelen Data', (array)json_encode($request->all()));
    }

    public function cancelOrder(Request $request)
    {
        Log::info('İptal Edilen Data', (array)json_encode($request->all()));
    }
}
