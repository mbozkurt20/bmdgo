@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurant Ürünleri</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Restaurant Ürünleri</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Düzenle</a></li>
                </ol>
            </div>
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
                        <h4 class="card-title">Ürün Düzenle Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('restaurant.products.update')}}" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="id" value="{{$product->id}}">
                                <div class="row">
                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold" for="imageInput">Görsel</label>
                                        <div class="image-upload-wrapper" onclick="document.getElementById('imageInput').click();">
                                            <label for="imageInput" class="image-upload-label">
                                                <!-- Basit resim ikon svg'si -->
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path d="M21 19V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2zM8.5 12.5l2.5 3.01L14.5 11l4.5 6H5l3.5-4.5z"/>
                                                </svg>
                                                Resim Seç veya Sürükle
                                            </label>
                                            <input type="file" id="imageInput" name="image" accept="image/*" required>
                                            <img id="imagePreview" alt="Seçilen Resim">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Kategori Seçiniz</label>
                                        <select class="form-control" name="category_id" required>
                                            @foreach($categories as $category)
                                                <option @if($category->id == $product->category_id) selected @endif value="{{$category->id}}">{{$category->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Adı</label>
                                        <input value="{{$product->name}}" type="text" class="form-control" name="name"
                                               placeholder="Ürün Adı" required>
                                    </div>


                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Kodu</label>
                                        <input  value="{{$product->code}}" type="text" class="form-control"
                                               name="code" placeholder="Ürün Kodu" required>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Fiyatı</label>
                                        <input value="{{$product->price}}" type="text" class="form-control"
                                               name="price" placeholder="Ürün Fiyatı" required>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Hazırlanma Süresi Seçiniz</label>
                                        <input  type="number" class="form-control" name="preparation_time"
                                               placeholder="Hazırlanma Süresi (dk)"
                                                value="{{$product->preparation_time}}"  required>
                                    </div>
                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Türü Seçiniz</label>
                                        <select class="form-control form-select" name="begenilen" required>
                                            <option {{$product->begenilen == 'deactive' ? 'selected' : null}}  value="deactive">Standart Ürün</option>
                                            <option {{$product->begenilen == 'active' ? 'selected' : null}} value="active">Beğenilen Ürün</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-8">
                                        <label class="form-label text-dark fw-bold">Ürün Detayları</label>
                                        <textarea
                                            cols="15" class="form-control border border-dark"
                                            name="details" rows="15">{{$product->details}}</textarea>
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydet</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        .image-upload-wrapper {
            position: relative;
            width: 250px;
            cursor: pointer;
            border: 2px dashed #6c757d;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: border-color 0.3s ease;
        }

        .image-upload-wrapper:hover {
            border-color: #0d6efd; /* Bootstrap primary renk */
        }

        .image-upload-wrapper input[type="file"] {
            display: none; /* Gizle dosya inputu */
        }

        .image-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            color: #6c757d;
            font-weight: 600;
        }

        .image-upload-label svg {
            width: 48px;
            height: 48px;
            margin-bottom: 10px;
            fill: #6c757d;
            transition: fill 0.3s ease;
        }

        .image-upload-wrapper:hover .image-upload-label svg {
            fill: #0d6efd;
        }

        #imagePreview {
            margin-top: 15px;
            max-width: 100%;
            max-height: 200px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
            display: none;
        }
    </style>

    <script>
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '';
                imagePreview.style.display = 'none';
            }
        });
    </script>

    <script>
        const nameInput = document.querySelector('input[name="name"]');
        const codeInput = document.querySelector('input[name="code"]');

        function generateCode(name) {
            if (!name) return '';
            // İsimden ilk 3 harfi al, büyük harfe çevir, boşluk varsa kaldır
            const shortName = name.trim().substring(0, 3).toUpperCase().replace(/\s/g, '');

            // Rastgele 3 karakter oluştur (harf ve rakam karışık)
            const randomStr = Math.random().toString(36).substring(2, 5).toUpperCase();

            // Kısa ve unique kod
            return shortName + randomStr;
        }

        // İsim inputuna her değişiklikte kodu otomatik yaz
        nameInput.addEventListener('input', () => {
            const generatedCode = generateCode(nameInput.value);
            codeInput.value = generatedCode;
        });
    </script>
@endsection


