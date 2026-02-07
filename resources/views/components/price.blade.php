<input
    type="text"
    class="form-control"
    placeholder="0,00"
    name="{{ $name }}"
    oninput="formatMoney(this)"
    inputmode="decimal"
    value="{{$value}}"
    autocomplete="off"
    {{ $required ? 'required' : '' }}
>
<input type="hidden" name="amount">

@once
    <script>
        function formatMoney(el) {
            let value = el.value.replace(/\D/g, '');
            if (value === '') {
                el.value = '';
                document.querySelector('input[name="amount"]').value = '';
                return;
            }
            let decimalValue = (parseInt(value) / 100).toFixed(2);
            let parts = decimalValue.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            el.value = parts.join(',');
            const hidden = document.querySelector('input[name="amount"]');
            hidden.value = decimalValue;
        }
    </script>

@endonce
