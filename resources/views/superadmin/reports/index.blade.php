@extends('superadmin.layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="mb-4 text-center">Raporlar</h1>

        <form method="GET" class="row mb-4">
            <div class="col-md-3">
                <label>Başlangıç Tarihi</label>
                <input type="date" name="start_date" class="form-control"
                       value="{{ \Carbon\Carbon::parse($startDate)->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label>Bitiş Tarihi</label>
                <input type="date" name="end_date" class="form-control"
                       value="{{ \Carbon\Carbon::parse($endDate)->toDateString() }}">
            </div>
            <div class="col-md-3">
                <label>Grupla</label>
                <select name="group_by" class="form-control">
                    <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Günlük</option>
                    <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Haftalık</option>
                </select>
            </div>
            <div class="col-md-2 mt-4">
                <button class="special-ok-button mt-2" type="submit">Filtrele</button>
            </div>
        </form>

        <div class="row">
            @foreach ($metrics as $index => $metric)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $metric['title'] }}</h5>
                            <h3 class="card-text">{{ $metric['value'] }}</h3>
                        </div>
                        <div class="p-2" style="height:200px;">
                            <canvas id="chart-{{ $index }}"></canvas>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

{{-- 👇 scriptler buraya, direkt --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const metrics = @json($metrics);

        console.log("Metrics:", metrics);

        metrics.forEach((metric, index) => {
            const canvas = document.getElementById(`chart-${index}`);
            if (!canvas) {
                console.error("Canvas bulunamadı:", `chart-${index}`);
                return;
            }

            const ctx = canvas.getContext('2d');
            const labels = Object.keys(metric.data || {});
            const values = Object.values(metric.data || {});

            console.log(metric.title, labels, values);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: metric.title,
                        data: values,
                        borderColor: 'rgb(231,0,77)',
                        backgroundColor: 'rgb(231,0,77)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: { ticks: { autoSkip: true, maxTicksLimit: 6 } }
                    }
                }
            });
        });
    });
</script>
