@extends('layouts.admin')

@section('title', 'Hasil Kelompok')

@section('page-title')
    <i class="fa-solid fa-chart-simple" style="color: var(--primary-yellow);"></i> Hasil Kelompok
@endsection

@section('content')
<div class="panel-card">
    <div class="panel-header">
        <h3><i class="fa-solid fa-users" style="color: var(--primary-cyan);"></i> Hasil Asesmen Diagnostik per Kelompok</h3>
    </div>

    <table class="quiz-table">
        <thead>
            <tr>
                <th>Kelompok</th>
                <th>Kelas</th>
                <th>Progres</th>
                <th>Skor</th>
                <th>Predikat</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($groups as $group)
                <tr>
                    <td><strong>{{ $group->name }}</strong></td>
                    <td style="color: var(--text-muted);">{{ $group->kelas?->name ?? '-' }}</td>
                    <td style="color: var(--text-muted);">
                        {{ $group->answers_count }}/{{ $totalImages }}
                        @if (! $group->is_complete)
                            <span style="color: var(--primary-yellow);">(belum selesai)</span>
                        @endif
                    </td>
                    <td><strong style="color: var(--primary-cyan);">{{ number_format($group->total_score, 1) }}</strong></td>
                    <td>
                        <span class="badge
                            @if($group->predikat_label === 'Sangat Baik') badge-lime
                            @elseif($group->predikat_label === 'Baik') badge-cyan
                            @elseif($group->predikat_label === 'Cukup') badge-yellow
                            @else badge-pink
                            @endif">
                            {{ $group->predikat_label }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.results.destroy', $group) }}"
                              onsubmit="return confirm('Hapus data kelompok ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-pink btn-sm"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada kelompok yang mengerjakan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="panel-card">
    <h3 style="margin-bottom: 14px; font-size: 1rem;"><i class="fa-solid fa-lightbulb" style="color: var(--primary-yellow);"></i> Panduan Tindak Lanjut</h3>
    <div style="display: grid; gap: 10px; font-size: 0.9rem; color: var(--text-muted);">
        <div><span class="badge badge-lime">45-50</span> Sangat Baik &mdash; penjelasan dapat dipercepat, langsung ke contoh lanjutan.</div>
        <div><span class="badge badge-cyan">30-40</span> Baik &mdash; penjelasan dengan tempo normal.</div>
        <div><span class="badge badge-yellow">15-25</span> Cukup &mdash; perlu pengulangan singkat sebelum lanjut.</div>
        <div><span class="badge badge-pink">&lt;15</span> Perlu Bimbingan &mdash; guru memberi penjelasan tambahan dengan contoh lebih sederhana.</div>
    </div>
</div>
@endsection
