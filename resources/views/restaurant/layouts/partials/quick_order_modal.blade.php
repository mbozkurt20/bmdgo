
<!-- Hızlı Sipariş Modal -->
<div id="quickOrderModal" class="custom-modal">
    <div class="custom-modal-content">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <form id="quickOrderForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="quickOrderModalLabel">Hızlı Sipariş</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Müşteri Adı</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Telefon</label>
                    <input type="text" name="phone" id="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Adres</label>
                    <textarea name="address" id="address" rows="3" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Ödeme Yöntemi</label>
                    <select name="payment_method" id="payment_method" class="form-select" required>
                        <option value="Kapıda Nakit İle Ödeme">Kapıda Nakit İle Ödeme</option>
                        <option value="Kapıda Ticket İle Ödeme">Kapıda Ticket İle Ödeme</option>
                        <option value="Kapıda Kredi Kartı İle Ödeme">Kapıda Kredi Kartı İle Ödeme</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Sipariş Tutarı</label>
                    <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Siparişi Gönder</button>
            </div>
        </form>

    </div>
</div>

<style>
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.5);
    }

    .custom-modal-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 8px;
        width: 90%;
        max-width: 500px;
        position: relative;
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 24px;
        cursor: pointer;
    }

    input, textarea, select {
        width: 100%;
        margin: 8px 0;
        padding: 10px;
        box-sizing: border-box;
    }
</style>

<script>
    const modal = document.getElementById('quickOrderModal');
    const openBtn = document.getElementById('openModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');

    openBtn.addEventListener('click', () => modal.style.display = 'block');
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
            tracking_id: Math.random().toString(36).substring(2, 10).toUpperCase(),
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
                    items: [{ packageItemId: "ahtaPOS" }],
                    productId: 15,
                    name: "Hizli Sipariş"
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
            alert("Sipariş başarıyla gönderildi.");
            form.reset();
            modal.style.display = 'none';
        } else {
            alert("Sipariş gönderilirken bir hata oluştu.");
        }
    });
</script>


