/**
 * Projenin JS bağımlılıklarını yükle
 */
require('./bootstrap');
window.Vue = require('vue');

// SweetAlert2'yi import et
import Swal from 'sweetalert2';
window.Swal = Swal;

// ------------------------------------------------------------

/**
 * Vue Bileşenlerini Kayıt Et
 */

// Manuel bileşen kaydı
Vue.component('create-screen', require('./components/orders/create-screen.vue').default);

// Otomatik tarama istersen, şu satırların yorumunu kaldır:
// const files = require.context('./components', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// ------------------------------------------------------------

/**
 * Vue Instance oluştur
 */
const app = new Vue({
    el: '#app',
});

// ------------------------------------------------------------

/**
 * Laravel Echo & Pusher Kurulumu
 */
import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

// ------------------------------------------------------------

/**
 * Gerçek zamanlı "order-assigned" kanalını dinle
 */
window.Echo.channel('order-assigned')
    .listen('.order.assigned', (e) => {
        console.log('Yeni sipariş atandı: ', e.order);

        // Zil sesi çal
        const audio = new Audio('/pos/audio/bell_small_002.mp3');
        audio.play().catch(error => {
            console.error('Ses çalarken hata oluştu:', error);
        });

        // Bildirim göster
        Swal.fire({
            title: 'Yeni Sipariş!',
            text: 'Yeni sipariş kuryeye atandı.',
            icon: 'info',
            confirmButtonText: 'Tamam'
        });
    });
