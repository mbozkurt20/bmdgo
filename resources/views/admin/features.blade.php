@extends('admin.layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Yönetici Özellikler</h2>
        </div>
        @if(session()->has('message'))
            <div class="custom-alert success">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('message') }}</span>
            </div>
        @endif

        @if(session()->has('test') )
            <div class="custom-alert error">
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
                <span class="alert-message">{{ session()->get('test') }}</span>
            </div>
        @endif

        <div class="row">
            <div class="col-xl-8 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Aktif/Pasif Özellikler</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            @foreach($features as $feature)
                                <div class="form-check form-switch me-3">
                                    <label class="form-check-label text-dark fw-bold px-3 mt-3" for="s-{{$feature->id}}">{{$feature->name}}</label>
                                    <input class="form-check-input" type="checkbox" id="s-{{$feature->id}}" onclick="changeFeature('{{$feature->id}}')"
                                           role="switch" style="height: 40px; width: 80px;"
                                           @if(\App\Models\AdminSystemFeature::where('admin_id',$admin->id)->where('system_feature_id', $feature->id)->exists()) checked @endif
                                    >
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function changeFeature(e) {
            $.ajax({
                type: 'GET',
                url: '/admin/features/update/' + e,
                success: function(data) {
                    let message = '';

                    message = 'Durum Güncellendi';

                    Swal.fire({
                        title: message,
                        icon: 'success',
                        text: 'Özellik Durumu Güncellendi!',
                        confirmButtonText: 'Tamam',
                        background: '#ffffff',
                        color: '#fff',
                        iconColor: '#e7004d',
                        confirmButtonColor: '#e7004d',
                        customClass: {
                            popup: 'rounded-xl shadow-2xl',
                            confirmButton: 'px-6 py-3 text-lg font-semibold',
                        },
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown',
                        },
                        hideClass: {
                            popup: 'animate__animated animate__fadeOutUp',
                        }
                    });
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    </script>

@endsection


