@props([
    'name',
    'value' => '',
    'class' => '',
    'label' => null,
    'required' => false,
    'placeholder' => '0,00'
])

@php
    $displayValue = $value !== '' ? number_format((float)$value, 2, ',', '.') : '';
    $hiddenValue = $value !== '' ? number_format((float)$value, 2, '.', '') : '';
@endphp

<div class="mb-4 text-dark money-input-container">
    @if($label)
        <label>{{ $label }}</label>
    @endif

    <input
        {{ $attributes->merge([
            'type' => 'text',
            'class' => $class ?? 'form-control',
            'placeholder' => $placeholder,
            'inputmode' => 'decimal',
            'autocomplete' => 'off',
        ]) }}
        name="{{ $name }}_display"
        value="{{ $displayValue }}"
        oninput="formatMoneyForComponent(this)"
        {{ $required ? 'required' : '' }}
    >
    <input type="hidden" name="{{ $name }}" value="{{ $hiddenValue }}">
</div>

@once
    <script>
        function formatMoneyForComponent(el) {
            // 1. Sadece rakamları al
            let value = el.value.replace(/\D/g, '');

            // 2. Eğer içi boşsa temizle ve çık
            if (value === '') {
                el.value = '';
                const hiddenInput = el.closest('.money-input-container').querySelector('input[type="hidden"]');
                if(hiddenInput) hiddenInput.value = '';
                return;
            }

            // 3. Sayıyı kuruş mantığına çevir (71 -> 0.71)
            let decimalValue = (parseInt(value) / 100).toFixed(2);

            // 4. Görünüm için formatla (1250.50 -> 1.250,50)
            let parts = decimalValue.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            el.value = parts.join(',');

            // 5. Backend için gizli inputu güncelle
            const container = el.closest('.money-input-container');
            if (container) {
                const hiddenInput = container.querySelector('input[type="hidden"]');
                if (hiddenInput) {
                    hiddenInput.value = decimalValue;
                }
            }
        }
    </script>
@endonce
