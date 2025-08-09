<!-- Hızlı Sipariş Modal -->
<div id="quickOrderModal" class="custom-modal">
    <div class="custom-modal-content shadow-lg">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <form id="quickOrderForm" class="modal-content p-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="quickOrderModalLabel">Hızlı Sipariş</h5>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="full_name" class="form-label fw-semibold">Müşteri Adı</label>
                    <input type="text" name="full_name" id="full_name" class="form-control rounded-pill" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Telefon</label>
                    <input type="text" name="phone" id="phone" class="form-control rounded-pill" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label fw-semibold">Adres</label>
                    <textarea name="address" id="address" rows="3" class="form-control rounded-4" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label fw-semibold">Ödeme Yöntemi</label>
                    <div class="custom-select-wrapper">
                        <select name="payment_method" id="payment_method" class="form-select custom-select" required>
                            <option value="Kapıda Nakit İle Ödeme">Kapıda Nakit İle Ödeme</option>
                            <option value="Kapıda Ticket İle Ödeme">Kapıda Ticket İle Ödeme</option>
                            <option value="Kapıda Kredi Kartı İle Ödeme">Kapıda Kredi Kartı İle Ödeme</option>
                        </select>
                        <span class="select-arrow">&#9662;</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label fw-semibold">Sipariş Tutarı</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control rounded-pill" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold">
                    Siparişi Gönder
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .custom-modal-content {
        background-color: #fff;
        border-radius: 20px;
        width: 95%;
        max-width: 500px;
        position: relative;
        animation: modalFadeIn 0.3s ease;
    }

    @keyframes modalFadeIn {
        from { transform: translateY(-20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        cursor: pointer;
        color: #888;
        transition: color 0.2s;
    }
    .close-btn:hover {
        color: #000;
    }

    /* Select kutusu tasarımı */
    .custom-select-wrapper {
        position: relative;
    }
    .custom-select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        border-radius: 50px;
        padding-right: 40px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .custom-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgb(231, 0, 77);
        outline: none;
    }
    .select-arrow {
        position: absolute;
        top: 50%;
        right: 15px;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 14px;
        color: #6c757d;
    }

    /* Toast Tasarımı */
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 12px;
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1100;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .toast.show { opacity: 1; }
    .toast.success { background-color: #58ba5b; }
    .toast.error { background-color: #f44336; }
</style>



<script>
    const modal = document.getElementById('quickOrderModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    openBtn.addEventListener('click', () => modal.style.display = 'flex');
    closeBtn.addEventListener('click', () => modal.style.display = 'none');

    window.addEventListener('click', (e) => {
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });

    document.getElementById("quickOrderForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const form = e.target;
        const data = {
            restaurant_id: {{ auth()->user()->id }},
            full_name: form.full_name.value,
            phone: form.phone.value,
            address: form.address.value,
            verify_code: Math.floor(100000 + Math.random() * 900000),
            payment_method: form.payment_method.value,
            amount: form.amount.value,
            items: JSON.stringify([
                {
                    price: "0.000",
                    unitSellingPrice: "0.000",
                    items: [],
                    productId: 0,
                    name: "Hızlı Sipariş"
                }
            ])
        };

        const response = await fetch("{{ route('quick.order.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            form.reset();
            modal.style.display = 'none';

            // Başarı bildirimi
            showToast("Siparişiniz Başarıyla Eklendi", "success");
        } else {
            // Hata bildirimi
            showToast("Bir hata meydana geldi", "error");
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`; // Örn: toast success veya toast error
            toast.textContent = message;

            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }
    });
</script>
