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

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

<script>
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
