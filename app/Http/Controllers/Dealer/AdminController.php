<?php

namespace App\Http\Controllers\Dealer;

use App\Helpers\CourierStatus;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\Courier;
use App\Models\District;
use App\Models\Order;
use App\Models\SuperAdmin;
use App\Models\TopupMovement;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    public function admin()
    {
        $admins = Admin::where('created_by_id',Auth::guard('dealer')->id())->where('created_by_type','dealer')->get();
        return view('dealer.admin.index', compact('admins'));
    }
    public function topup(Request $request, $adminId)
    {
        $admin = Admin::findOrFail($adminId);

        // Ortak base query (kartlar için)
        $topupQuery = TopupMovement::where('admin_id', $adminId);

        // Tarih filtresi ekle
        if ($request->filled('start_date')) {
            $topupQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $topupQuery->whereDate('created_at', '<=', $request->end_date);
        }

        // İstatistikleri tarih filtreleriyle hesapla
        $stats = [
            'current_balance'   => $admin->top_up_balance,
            'total_topup'       => (clone $topupQuery)->sum('top_up'),
            'paid_amount'       => (clone $topupQuery)->where('is_approved', true)->where('is_paid', true)->sum('total_amount'),
            'remaining_amount'  => (clone $topupQuery)->where('is_approved', true)->where('is_paid', false)->sum('total_amount'),
        ];

        // DataTable için query
        $recordsQuery = TopupMovement::where('admin_id', $adminId);
        if ($request->filled('start_date')) {
            $recordsQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $recordsQuery->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->ajax()) {
            return datatables()->query($recordsQuery)
                ->editColumn('created_at', function ($record) {
                    return $record->created_at->format('d-m-Y H:i:s');
                })
                ->addColumn('created_by_name', function ($record) {
                    switch ($record->created_type) {
                        case 'dealer':
                            return SuperAdmin::find($record->created_by_user_id)->name ?? '';
                        case 'admin':
                            return Admin::find($record->created_by_user_id)->name ?? '';
                        case 'dealer':
                            return User::find($record->created_by_user_id)->name ?? '';
                        default:
                            return '';
                    }
                })
                ->make(true);
        }

        // Normal sayfa yükleme
        $records = $recordsQuery->orderBy('created_at', 'desc')->get();

        return view('dealer.admin.topup', compact('admin', 'records', 'stats'));
    }
    public function list(Request $request)
    {
        $status = $request->get('status');

        $query = TopupMovement::query();

        $query->where('created_by_id', Auth::guard('dealer')->id());
        $query->where('created_by_type', 'dealer');

        // Filtre
        if ($status === 'pending') {
            $query->where('is_approved', false);
        } elseif ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'paid') {
            $query->where('is_approved', true)->where('is_paid', true);
        } elseif ($status === 'unpaid') {
            $query->where('is_approved', true)->where('is_paid', false);
        }

        return DataTables::of($query)
            ->addColumn('created_by', function ($row) {
                switch ($row->created_type) {
                    case 'dealer':
                        return SuperAdmin::find($row->created_by_user_id)?->name ?? '—';
                    case 'admin':
                        return Admin::find($row->created_by_user_id)?->name ?? '—';
                    case 'dealer':
                        return User::find($row->created_by_user_id)?->name ?? '—';
                    default:
                        return '—';
                }
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i:s');
            })
            ->make(true);
    }

    public function approve($recordId)
    {
        $record = TopupMovement::find($recordId);
        $record->is_approved = true;
        $record->update();

        echo 'OK';
    }

    public function unPaid($recordId)
    {
        $record = TopupMovement::find($recordId);
        $record->is_paid = false;
        $record->update();
        echo 'OK';
    }

    public function paid($recordId)
    {
        $record = TopupMovement::find($recordId);
        $record->is_paid = true;
        $record->update();
        echo 'OK';
    }

    public function topUpCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required',
            'top_up_price' => 'required',
            'top_up' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('test', $validator->getMessageBag()->first());
        }

        $topup = TopupMovement::create([
            'admin_id' => $request->input('admin_id'),
            'top_up_price' => $request->input('top_up_price'),
            'top_up' => $request->input('top_up'),
            'type' => 'yükleme',
            'is_approved' => 0,
            'total_amount' => $request->input('total_amount'),
            'created_by_user_id' => Auth::guard('dealer')->id(),
            'created_type' => 'dealer',
        ]);

        return redirect()->back()->with('message', 'Kontör Başarıyla Yüklendi!');
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::find($id);
        $delete = $admin->delete();
        if ($delete) {
            echo 'OK';
        } else {
            echo 'ERR';
        }
    }

    public function statusAdmin($id)
    {
        $admin = Admin::find($id);
        $delete = $admin->update([
            'is_active' => !$admin->is_active
        ]);

        if ($delete) {
            echo 'OK';
        } else {
            echo 'ERR';
        }
    }

    public function createAdmin()
    {
        $cities = City::all();
        return view('dealer.admin.create', compact('cities'));
    }

    public function getDistricts($cityId)
    {
        $districts = District::where('city_id', $cityId)->get(['id', 'name']);
        return response()->json($districts);
    }

    public function createAdminRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins',
            'phone' => 'required|unique:admins',
            'password' => 'required|min:5',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('test', $validator->getMessageBag()->first());
        }

        // Validasyon başarılı ise admin tablosuna kaydet
        Admin::create([
            'created_by_id' => \auth()->id(),
            'created_by_type' => 'dealer',
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'latitude' => $request->input('lat'),
            'longitude' => $request->input('lng'),
            'city_id' => $request->input('city_id'),
            'district_id' => $request->input('district_id'),
            'address' => $request->input('address'),
        ]);

        return redirect()->back()->with('message', 'Yeni Yönetici Başarıyla Eklendi!');
    }

    public function editAdmin($id)
    {
        $admin = Admin::find($id);
        $cities = City::all();
        return view('dealer.admin.edit', compact('admin', 'cities'));
    }

    public function updateAdmin($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required',
            'district_id' => 'required',
            'address' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('test', $validator->getMessageBag()->first());
        }
        // Admin kaydını bul ve güncelle
        $admin = Admin::find($id);

        // Eğer admin kaydı yoksa hata döndürülebilir
        if (!$admin->exists()) {
            return redirect()->back()->with(['test' => 'Yönetici Bulunamadı.']);
        }

        // Güncelleme verilerini hazırla
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'latitude' => $request->input('lat'),
            'longitude' => $request->input('lng'),
            'city_id' => $request->input('city_id'),
            'district_id' => $request->input('district_id'),
            'address' => $request->input('address'),
        ];

        // Şifre değiştirildiyse hashleyip güncelle
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        // Veritabanında güncelleme işlemi
        $admin->update($updateData);

        return redirect()->route('dealer.admin')->with('message', 'Yönetici bilgileri başarıyla güncellendi!');
    }
}
