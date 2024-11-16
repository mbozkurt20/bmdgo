@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3>Rapor Oluştur</h3>
                </div>
                <div class="card-body">
                    <form id="reportForm">
                        <div class="form-group" style="padding-bottom: 15px;">
                            <label for="">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" name="start_date" >
                        </div>
                        <div class="form-group" style="padding-bottom: 15px;">
                            <label for="">Bitiş Tarihi</label>
                            <input type="date" class="form-control" name="end_date" >
                        </div>
                        <button type="submit" class="btn btn-primary">Rapor Oluştur</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#reportForm').on('submit', function(e){
                e.preventDefault(); // Formun varsayılan gönderimini durdur
                $.downloadReport();
            });
            var utc = new Date().toJSON().slice(0,10).replace(/-/g,'-');
            $.downloadReport = function(){
                var formData = $("#reportForm").serialize(); // Form verilerini al
                $.ajax({
                    url: '{{ route("superadmin.reports.download") }}',
                    method: 'GET',
                    data: formData,
                    xhrFields: {
                        responseType: 'blob' // Yanıtı blob olarak al
                    },
                    success: function(data, status, xhr) {
                        var blob = new Blob([data], { type: xhr.getResponseHeader('Content-Type') });
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = 'report-'+utc+'.xlsx'; 
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    },
                    error: function(xhr, status, error) {
                        console.error('İndirme hatası:', error);
                    }
                });
            }
        });
    </script>
@endsection
