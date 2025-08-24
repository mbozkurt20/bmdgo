@extends('admin.layouts.app')
@section('content')
   <div class="container">
       <div class="max-w-lg mx-auto p-5 bg-white p-6 rounded-lg shadow">
           <h2 class="text-xl font-bold mb-4">Garanti Ödeme Formu</h2>

           <form method="POST" action="{{ route('payment.start') }}">
               @csrf
               <input hidden="" value="{{$topup}}" name="topup" type="text">
               <div class="mb-3">
                   <label class="block text-sm font-medium">Kart Üzerindeki İsim</label>
                   <input type="text"  value="mehmet bozkurt" name="card_name" class="w-full border rounded p-2" placeholder="Ad Soyad" required>
               </div>

               <div class="mb-3">
                   <label class="block text-sm font-medium">Kart Numarası</label>
                   <input type="text" value="4824910501747014" name="card_number" class="w-full border rounded p-2" maxlength="16" placeholder="1111 2222 3333 4444" required>
               </div>

               <div class=" gap-3 mb-3">
                   <div>
                       <label class="block text-sm font-medium">Son Kullanma (Ay)</label>
                       <input type="text" value="4" name="expire_month" class="w-full border rounded p-2" maxlength="2" placeholder="MM" required>
                   </div>
                   <div>
                       <label class="block text-sm font-medium">Son Kullanma (Yıl)</label>
                       <input type="text" value="2026" name="expire_year" class="w-full border rounded p-2" maxlength="4" placeholder="YYYY" required>
                   </div>
               </div>

               <div class="mb-3">
                   <label class="block text-sm font-medium">CVC</label>
                   <input type="password" name="cvc" class="w-full border rounded p-2" maxlength="4" placeholder="XXX" required>
               </div>

               <div class="mb-3">
                   <label class="block text-sm font-medium">Tutar (₺)</label>
                   <input type="number" value="{{$amount}}" step="0.01" name="amount" class="w-full border rounded p-2" placeholder="0.00" required>
               </div>

               <button type="submit" class="w-full  special-button text-white py-2 rounded-lg hover:bg-blue-700">
                   Ödeme Yap
               </button>
           </form>
       </div>
   </div>
@endsection
