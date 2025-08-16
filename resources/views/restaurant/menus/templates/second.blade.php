<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $data['name'] }} - Menü</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        h2, h3, h4 {
            font-family: 'Playfair Display', serif;
        }
        .menu-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
            border: 2px solid transparent;
        }
        .menu-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 12px 24px rgba(0,0,0,0.25);
            border-color: #facc15;
        }
        .price-tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: linear-gradient(135deg, #facc15, #f59e0b);
            color: #1f2937;
            font-weight: bold;
            border-radius: 9999px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .bg-gradient-overlay {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8));
        }
    </style>
</head>
<body class="bg-gray-900 text-white">

<!-- Menü Bölümü -->
<div id="menu" class="bg-fixed bg-center bg-cover" style="background-image: url('img/antique-cafe-bg-02.jpg');">
    <div class="bg-gradient-overlay min-h-screen flex items-center justify-center py-16 px-4">
        <div class="max-w-7xl w-full text-center">

            <!-- Başlık -->
            <h2 class="text-4xl md:text-5xl font-bold mb-12 py-4 px-8 rounded-lg border-b-4 border-yellow-400 inline-block bg-white bg-opacity-10">
                {{ $data['name'] }} - Menü
            </h2>

            <!-- Kategoriler -->
            @foreach($data['categories'] as $category)
                <div class="mb-16">
                    <h3 class="text-3xl font-semibold mb-8 text-yellow-300 border-b-4 border-yellow-300 inline-block pb-2">
                        {{ $category->name }}
                    </h3>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 px-4">
                        @foreach($category->products as $product)
                            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-lg shadow-lg menu-card p-4 flex flex-col sm:flex-row items-start sm:items-center">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full sm:w-48 h-48 object-cover rounded-lg mb-4 sm:mb-0 sm:mr-4 border-2 border-gray-200" />
                                <div class="flex-1 text-left">
                                    <h4 class="text-xl font-semibold mb-2 text-yellow-200">{{ $product->name }}</h4>
                                    <p class="mb-3 text-sm text-gray-200">{{ $product->details }}</p>
                                    <span class="price-tag">{{ $product->price }} ₺</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</div>

</body>
</html>
