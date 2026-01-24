@extends('admin.layouts.app')

@section('content')
    <style>
        .success-container {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-card {
            background: #ffffff;
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            max-width: 500px;
            width: 100%;
            text-align: center;
            border: 1px solid #f0f0f0;
        }

        .check-icon-wrapper {
            width: 100px;
            height: 100px;
            background: #dcfce7;
            color: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 25px;
            animation: scaleIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes scaleIn {
            from { transform: scale(0); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .success-title {
            color: #1e293b;
            font-weight: 800;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .success-text {
            color: #64748b;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-balance {
            background: #3b82f6;
            color: white;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            border: none;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .btn-balance:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .order-id {
            display: inline-block;
            background: #f8fafc;
            padding: 5px 15px;
            border-radius: 8px;
            color: #94a3b8;
            font-size: 0.85rem;
            margin-top: 20px;
            font-family: monospace;
        }
    </style>

    <div class="container success-container">
        <div class="success-card">
            <div class="check-icon-wrapper">
                <i class="fas fa-check"></i>
            </div>

            <h2 class="success-title">Ödeme Başarılı!</h2>
            <p class="success-text">
                Ödemeniz başarıyla gerçekleştirildi. Bakiyeniz otomatik olarak güncellenmiştir. Keyifli satışlar dileriz!
            </p>

            <div class="d-grid gap-2">
                <a href="/admin/top-up-balance" class="btn-balance">
                    <i class="fas fa-wallet"></i> Bakiyeme Git
                </a>

                <a href="/admin" class="btn mt-2 text-muted fw-bold">
                    Ana Sayfaya Dön
                </a>
            </div>
        </div>
    </div>
@endsection
