@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title')
    <i class="fa-solid fa-gauge-high" style="color: var(--primary-yellow);"></i> Dashboard
@endsection

@section('top-actions')
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-primary">
        <i class="fa-solid fa-layer-group"></i> Lihat Daftar Kuis
    </a>
@endsection

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-yellow);"><i class="fa-solid fa-gamepad"></i></div>
        <div class="stat-info"><h4>Total Kuis</h4><span>{{ $totalKuis }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-cyan);"><i class="fa-solid fa-list-check"></i></div>
        <div class="stat-info"><h4>Total Soal</h4><span>{{ $totalSoal }}</span></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: var(--primary-pink); color: #fff;"><i class="fa-solid fa-users"></i></div>
        <div class="stat-info"><h4>Peserta Selesai</h4><span>{{ $pesertaSelesai }}</span></div>
    </div>
</div>

<style>
    .dashboard-charts { display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .dashboard-charts { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-charts">
    <div class="panel-card">
        <div class="panel-header">
            <h3><i class="fa-solid fa-chart-column" style="color: var(--primary-cyan);"></i> Jumlah Kelompok per Kuis</h3>
        </div>
        @if (array_sum($participationCounts) > 0)
            <canvas id="chartParticipation" height="220"></canvas>
        @else
            <p style="text-align:center; color:var(--text-muted); padding: 30px 0;">Belum ada kelompok yang main kuis.</p>
        @endif
    </div>

    <div class="panel-card">
        <div class="panel-header">
            <h3><i class="fa-solid fa-chart-pie" style="color: var(--primary-pink);"></i> Predikat Kelompok Selesai</h3>
        </div>
        @if ($pesertaSelesai > 0)
            <canvas id="chartPredikat" height="220"></canvas>
        @else
            <p style="text-align:center; color:var(--text-muted); padding: 30px 0;">Belum ada kelompok yang menyelesaikan kuis.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const rootStyle = getComputedStyle(document.documentElement);
        const color = (name) => rootStyle.getPropertyValue(name).trim();

        const participationEl = document.getElementById('chartParticipation');
        if (participationEl) {
            new Chart(participationEl, {
                type: 'bar',
                data: {
                    labels: @json($participationLabels),
                    datasets: [{
                        label: 'Kelompok',
                        data: @json($participationCounts),
                        backgroundColor: color('--primary-cyan'),
                        borderColor: '#000',
                        borderWidth: 2,
                        borderRadius: 8,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1, color: '#94a3b8' }, grid: { color: '#334155' } },
                        x: { ticks: { color: '#94a3b8' }, grid: { display: false } },
                    },
                },
            });
        }

        const predikatEl = document.getElementById('chartPredikat');
        if (predikatEl) {
            const predikatData = @json($predikatCounts);
            new Chart(predikatEl, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(predikatData),
                    datasets: [{
                        data: Object.values(predikatData),
                        backgroundColor: [
                            color('--primary-lime'),
                            color('--primary-cyan'),
                            color('--primary-yellow'),
                            color('--primary-pink'),
                        ],
                        borderColor: '#000',
                        borderWidth: 2,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { color: '#f8fafc' } } },
                },
            });
        }
    });
</script>
@endpush
