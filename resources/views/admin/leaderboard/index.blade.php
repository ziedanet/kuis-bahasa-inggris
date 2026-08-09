@extends('layouts.admin')

@section('title', 'Leaderboard')

@section('page-title')
    <i class="fa-solid fa-trophy" style="color: var(--primary-yellow);"></i> Leaderboard
@endsection

@section('content')
<div class="panel-card" style="padding: 16px 24px;">
    <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 12px; justify-content: space-between;">
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.leaderboard', ['tipe' => 'kelompok']) }}"
               class="btn btn-sm {{ $tipe === 'kelompok' ? 'btn-primary' : 'btn-dark' }}">
                <i class="fa-solid fa-people-group"></i> Kuis Kelompok
            </a>
            <a href="{{ route('admin.leaderboard', ['tipe' => 'individu']) }}"
               class="btn btn-sm {{ $tipe === 'individu' ? 'btn-primary' : 'btn-dark' }}">
                <i class="fa-solid fa-user-pen"></i> Kuis Individu
            </a>
        </div>

        <form method="GET" action="{{ route('admin.leaderboard') }}" style="display: flex; align-items: center; gap: 10px;">
            <input type="hidden" name="tipe" value="{{ $tipe }}">
            <label style="font-weight: 800; display: flex; align-items: center; gap: 8px; font-size: 0.85rem;">
                <i class="fa-solid fa-filter" style="color: var(--primary-pink);"></i> Kuis:
            </label>
            <select name="quiz_id" onchange="this.form.submit()" style="padding: 10px 16px; background: #0f172a; color: #fff; border: 2px solid #000; border-radius: 10px; font-weight: 700; width: 240px;">
                <option value="">Semua Kuis</option>
                @foreach ($quizList as $quizOption)
                    <option value="{{ $quizOption->id }}" @selected((string) $quizId === (string) $quizOption->id)>
                        {{ $quizOption->title }}
                    </option>
                @endforeach
            </select>
            @if ($quizId)
                <a href="{{ route('admin.leaderboard', ['tipe' => $tipe]) }}" class="btn btn-dark btn-sm">Reset</a>
            @endif
        </form>
    </div>
</div>

@if ($tipe === 'kelompok')
    <div class="panel-card">
        <div class="panel-header">
            <h3><i class="fa-solid fa-ranking-star" style="color: var(--primary-cyan);"></i> Peringkat Kelompok</h3>
        </div>

        <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -8px; margin-bottom: 16px;">
            Hanya kelompok yang sudah menyelesaikan seluruh soal. Urutan: skor tertinggi dulu, kalau skor sama yang waktu pengerjaannya paling singkat menang.
        </p>

        <table class="quiz-table">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Kelompok</th>
                    <th>Kelas</th>
                    <th>Kuis</th>
                    <th>Skor (0-100)</th>
                    <th>Predikat</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $index => $group)
                    <tr>
                        <td>
                            @php $rank = $index + 1; @endphp
                            <strong style="{{ $rank <= 3 ? 'color: var(--primary-yellow); font-size: 1.05rem;' : '' }}">
                                @if ($rank === 1) 🥇
                                @elseif ($rank === 2) 🥈
                                @elseif ($rank === 3) 🥉
                                @endif
                                #{{ $rank }}
                            </strong>
                        </td>
                        <td><strong>{{ $group->name }}</strong></td>
                        <td style="color: var(--text-muted);">{{ $group->kelas?->name ?? '-' }}</td>
                        <td style="color: var(--text-muted);">{{ $group->quiz?->title }}</td>
                        <td><strong style="color: var(--primary-cyan);">{{ number_format($group->scoreOn100(), 1) }}</strong></td>
                        <td>
                            @php $predikat = $group->predikat(); @endphp
                            <span class="badge
                                @if($predikat === 'Sangat Baik') badge-lime
                                @elseif($predikat === 'Baik') badge-cyan
                                @elseif($predikat === 'Cukup') badge-yellow
                                @else badge-pink
                                @endif">
                                {{ $predikat }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted);"><i class="fa-regular fa-clock"></i> {{ $group->durationLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada kelompok yang menyelesaikan kuis ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@else
    <div class="panel-card">
        <div class="panel-header">
            <h3><i class="fa-solid fa-ranking-star" style="color: var(--primary-cyan);"></i> Peringkat Individu</h3>
        </div>

        <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: -8px; margin-bottom: 16px;">
            Hanya siswa yang sudah menyelesaikan seluruh soal. Urutan: skor tertinggi dulu, kalau skor sama yang waktu pengerjaannya paling singkat menang.
        </p>

        <table class="quiz-table">
            <thead>
                <tr>
                    <th>Peringkat</th>
                    <th>Siswa</th>
                    <th>Kelas</th>
                    <th>Kuis</th>
                    <th>Skor (0-100)</th>
                    <th>Predikat</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $index => $attempt)
                    <tr>
                        <td>
                            @php $rank = $index + 1; @endphp
                            <strong style="{{ $rank <= 3 ? 'color: var(--primary-yellow); font-size: 1.05rem;' : '' }}">
                                @if ($rank === 1) 🥇
                                @elseif ($rank === 2) 🥈
                                @elseif ($rank === 3) 🥉
                                @endif
                                #{{ $rank }}
                            </strong>
                        </td>
                        <td><strong>{{ $attempt->siswa?->name ?? '-' }}</strong></td>
                        <td style="color: var(--text-muted);">{{ $attempt->siswa?->kelas?->name ?? '-' }}</td>
                        <td style="color: var(--text-muted);">{{ $attempt->quiz?->title }}</td>
                        <td><strong style="color: var(--primary-cyan);">{{ number_format($attempt->scoreOn100(), 1) }}</strong></td>
                        <td>
                            @php $predikat = $attempt->predikat(); @endphp
                            <span class="badge
                                @if($predikat === 'Sangat Baik') badge-lime
                                @elseif($predikat === 'Baik') badge-cyan
                                @elseif($predikat === 'Cukup') badge-yellow
                                @else badge-pink
                                @endif">
                                {{ $predikat }}
                            </span>
                        </td>
                        <td style="color: var(--text-muted);"><i class="fa-regular fa-clock"></i> {{ $attempt->durationLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center; color:var(--text-muted); padding:24px;">Belum ada siswa yang menyelesaikan kuis ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif
@endsection
