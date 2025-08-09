@extends('restaurant.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="mb-sm-4 d-flex flex-wrap align-items-center text-head">
            <h2 class="mb-3 me-auto">Restaurant Ürünleri</h2>
            <div>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/restaurant/products">Ürünler</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Yeni</a></li>
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
                        <h4 class="card-title">Yeni Ürün Formu</h4>
                    </div>
                    <div class="card-body">
                        <div class="basic-form">
                            <form method="post" action="{{route('restaurant.products.create')}}" enctype="multipart/form-data">
                                @csrf
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
                                            @foreach($categories as $categorie)
                                                <option value="{{$categorie->id}}"> {{$categorie->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Adı</label>
                                        <input type="text" id="productName" class="form-control" name="name" placeholder="Ürün Adı" required>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Kodu</label>
                                        <input type="text" id="productCode" class="form-control" name="code" placeholder="Ürün Kodu" required>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Fiyatı</label>
                                        <input type="text" class="form-control" name="price" placeholder="Ürün Fiyatı" required>
                                    </div>

                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Hazırlanma Süresi Seçiniz</label>
                                        <input type="number" class="form-control" name="preparation_time"
                                               placeholder="Hazırlanma Süresi (dk)" value="0" required>
                                    </div>
                                    <div class="mb-3 col-md-4 mb-5">
                                        <label class="form-label text-dark fw-bold">Ürün Türü Seçiniz</label>
                                        <select class="form-control form-select" name="begenilen" required>
                                            <option selected value="deactive">Standart Ürün</option>
                                            <option value="active">Beğenilen Ürün</option>
                                        </select>
                                    </div>

                                    <div class="mb-3 col-md-8">
                                        <label class="form-label text-dark fw-bold">Ürün Detayları</label>
                                        <textarea cols="15" class="form-control border border-dark" name="details" rows="15" ></textarea>
                                    </div>
                                </div>

                                <button type="submit" class="special-button float-end mt-4">Kaydet</button>
                            </form>                        </div>
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
        document.addEventListener("DOMContentLoaded", function () {
            const nameInput = document.getElementById('productName');
            const codeInput = document.getElementById('productCode');

            function generateProductCode(name) {
                if (!name) return '';

                const namePart = name.trim().substring(0, 3).toUpperCase().replace(/\s/g, '');
                const chars = 'ABCDEFGHIKLMNOPQRSTUVWXYZ0123456789';
                let randomPart = '';
                for (let i = 0; i < 5; i++) {
                    randomPart += chars.charAt(Math.floor(Math.random() * chars.length));
                }

                return namePart + randomPart;
            }

            nameInput.addEventListener('input', () => {
                const code = generateProductCode(nameInput.value);
                codeInput.value = code;
            });
        });
    </script>

@endsection


