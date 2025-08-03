<!-- Required vendors -->
<script src="{{ asset('theme/js/global.min.js') }}"></script>
<script src="{{ asset('theme/js/Chart.bundle.min.js') }}"></script>
<script src="{{ asset('theme/js/sweetalert2.all.min.js') }}"></script>
<script src="{{ asset('theme/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('theme/js/datatables.init.js') }}"></script>
<script src="{{ asset('theme/js/jquery.nice-select.min.js') }}"></script>
<script src="{{ asset('theme/js/jquery.repeater.min.js') }}"></script>
<script src="{{ asset('theme/js/form-repeater.int.js') }}"></script>
<script src="{{ asset('theme/js/select2.full.min.js') }}"></script>
<script src="{{ asset('theme/js/select2-init.js') }}"></script>
<!-- Chart piety plugin files -->
<!-- Dashboard 1 -->
<script src="{{ asset('theme/js/dashboard-1.js') }}"></script>
<script src="{{ asset('theme/js/custom.min.js') }}"></script>
<script src="{{ asset('theme/js/deznav-init.js') }}"></script>

<script src="https://cdn.socket.io/4.0.1/socket.io.min.js"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script type="text/javascript">
    const socket = io('https://kuryenkapinda.com:5000');

    socket.on('orderCourierNotification', (data) => {

        $('#totalOrders').html(data.total);

        const bildirim = document.getElementById('orderNotice').checked;
        if (bildirim === true) {

            let src = '{{ url('pos/audio/new_beep.mp3') }}';
            let audio = new Audio(src);
            audio.play();
        }
    });

    function AutoOrders(e) {
        const durum = document.getElementById('auto_order').checked;
        if (durum === true) {
            var status = 1;
        } else {
            var status = 0;
        }
        console.log(status)
        $.ajax({
            type: 'GET', //THIS NEEDS TO BE GET
            url: '/admin/setting/auto_order/' + status,
            success: function(data) {
                if (data == "OK") {
                    Swal.fire('Otomatik Sipariş Aktif!!');
                }
                if (data == "ERR") {
                    Swal.fire('Otomatik Sipari Kapal!!');
                }

            },
            error: function() {
                console.log(data);
            }
        });
    }
</script>

<script>
    window.onload = function () {
        var audio = document.getElementById('audioPlayer');

        // Ses kontrol fonksiyonları
        window.playAudio = function () {
            if (audio) audio.play().catch(err => console.error('Ses çalınamadı:', err));
        };

        window.pauseAudio = function () {
            if (audio) audio.pause();
        };

        // Pusher başlatma
        Pusher.logToConsole = true;

        var pusher = new Pusher('a293a751fd7a10f5a071', {
            cluster: 'mt1'
        });

        var channel = pusher.subscribe('new-order-channel');

        channel.bind('new-order-event', function (data) {
            var newOrderMessage = document.getElementById('new-order');
            if (newOrderMessage) {
                newOrderMessage.style.display = 'block';

                setTimeout(function () {
                    newOrderMessage.style.display = 'none';
                }, 3000);
            }

            playAudio();

            console.log('Yeni sipariş verisi:', data);
        });
    };
</script>
