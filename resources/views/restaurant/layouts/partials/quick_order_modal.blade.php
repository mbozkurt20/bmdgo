<!-- Hızlı Sipariş Modal -->
<div id="quickOrderModal" class="custom-modal" style="display: none;">
    <div class="custom-modal-content shadow-lg">
        <span class="close-btn" id="closeModalBtn">&times;</span>
        <form id="quickOrderForm" class="modal-content p-4">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="quickOrderModalLabel">Hızlı Sipariş</h5>
            </div>
            <div class="modal-body">
              <div class="row">
                  <div class="col-md-6 mb-3">
                      <label for="full_name" class="form-label fw-semibold text-black">Müşteri Adı</label>
                      <input type="text" name="full_name" id="full_name" class="form-control rounded-pill" required>
                  </div>
                  <div class="col-md-6 mb-3">
                      <label for="phone" class="form-label fw-semibold text-black">Telefon</label>
                      <input type="text" name="phone" id="phone" class="form-control rounded-pill" required>
                  </div>

                  <div class="mb-3 col-md-6">
                      <label class="form-label">İlçe Seçiniz</label>
                      <select class="form-control" required name="ilce" id="ilce">
                          @foreach(App\Models\District::where('city_id',\App\Models\Admin::find(auth()->user()->admin_id)->city_id)->get() as $d)
                              <option value="{{$d->id}}">{{$d->name}}</option>
                          @endforeach
                      </select>
                  </div>
                  <div class="mb-3 mt-3 col-md-6">
                      <label for="payment_method" class="form-label fw-semibold text-black">Ödeme Yöntemi</label>
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
                      <label for="amount" class="form-label fw-semibold text-black">Sipariş Tutarı</label>
                      <input placeholder="0.00" type="number" step="0.01" name="amount" id="amount" class="form-control rounded-pill" required>
                  </div>

                  <div class="mb-3">
                      <hr>
                      <h5 class="fw-semibold text-black mb-3">Adres Bilgileri</h5>

                      <div class="d-flex align-items-center mb-2">
                          <span class="me-2 text-black">Mah.</span>
                          <input type="text" name="mahalle" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: Ankara" required>
                      </div>

                      <div class="d-flex align-items-center mb-2">
                          <span class="me-2 text-black">Sok.</span>
                          <input type="text" name="sokak_cadde" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 5021" required>
                      </div>

                      <div class="d-flex align-items-center mb-2">
                          <span class="me-2 text-black">Apt Adı.</span>
                          <input type="text" name="bina_no" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: Deniz Apt." required>
                      </div>

                      <div class="d-flex align-items-center mb-2">
                          <span class="me-2 text-black">Kat:</span>
                          <input type="text" name="kat" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 3" required>
                      </div>

                      <div class="d-flex align-items-center mb-2">
                          <span class="me-2 text-black">Daire:</span>
                          <input type="text" name="daire_no" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 5" required>
                      </div>

                      <div class="d-flex align-items-center mb-2">
                          <span class="me-2 text-black">Adres Tarifi (opsiyonel):</span>
                          <input type="text" name="adress_tarifi" class="flex-grow-1 border-0 border-bottom bg-transparent" placeholder="Örn: 5" required>
                      </div>

                  </div>


              </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button id="submitOrderBtn"  type="submit" class="special-button w-100 rounded-pill py-2 fw-bold">
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
        border-color: #e7004d;
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
    const openBtn = document.getElementById('openModalBtn2');
    const closeBtn = document.getElementById('closeModalBtn');

    // Modal başlangıçta kapalı kalsın
    modal.style.display = 'none';

    if (openBtn) {
        openBtn.addEventListener('click', () => {
            modal.style.display = 'flex';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    document.getElementById("quickOrderForm").addEventListener("submit", async function(e) {
        e.preventDefault();

        const form = e.target;
        const submitBtn = document.getElementById("submitOrderBtn");

        // Butonu loading moduna al
        submitBtn.disabled = true;
        submitBtn.textContent = "Sipariş Alınıyor...";
        submitBtn.classList.remove("special-button");
        submitBtn.classList.add("special-ok-button");

        const data = {
            restaurant_id: {{ auth()->user()->id }},
            full_name: form.full_name.value,
            phone: form.phone.value,
            ilce: form.ilce.value,
            mahalle: form.mahalle.value,
            sokak_cadde: form.sokak_cadde.value,
            bina_no: form.bina_no.value,
            kat: form.kat.value,
            daire_no: form.daire_no.value,
            adress_tarifi: form.adress_tarifi.value,

            verify_code: Math.floor(100000 + Math.random() * 900000),
            payment_method: form.payment_method.value,
            amount: form.amount.value,
            items: JSON.stringify([
                {
                    price: 0.00,
                    unitSellingPrice: 0.00,
                    quantity: 1,
                    productId: 0,
                    name: "Hızlı Sipariş"
                }
            ])
        };


        try {
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
                showToast("Siparişiniz Başarıyla Eklendi", "success");
            } else {
                if(response.status === 400){
                    showToast("Lütfen Adres Bilginizi Detaylı ve Anlaşılır Yazınız.", "error");
                }else {
                    showToast("Bir hata meydana geldi", "error");
                }
            }
        } catch (error) {
            showToast("Sunucu hatası: " + error.message, "error");
        }

        // Butonu eski haline döndür
        submitBtn.disabled = false;
        submitBtn.textContent = "Siparişi Gönder";
        submitBtn.classList.remove("special-ok-button");
        submitBtn.classList.add("special-button");
    });

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
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
</script>
