@php
    $fmtTime = fn (?int $s) => $s === null ? '--:--' : sprintf('%02d:%02d', intdiv($s, 60), $s % 60);
    $isKelompok = $tipe === 'kelompok';
@endphp

<div class="lb-rows">
    @forelse ($rows as $index => $item)
        @php
            $rank = $index + 1;
            $name = $isKelompok ? $item->name : $item->displayName();
            $kelas = $isKelompok ? $item->kelas?->name : $item->displayKelas();
            $score = $item->scoreOn100();
            $showUrl = $isKelompok
                ? route('admin.leaderboard.kelompok.show', $item)
                : route('admin.leaderboard.individu.show', $item);
            $deleteUrl = $isKelompok
                ? route('admin.leaderboard.kelompok.destroy', $item)
                : route('admin.leaderboard.individu.destroy', $item);
        @endphp
        <div class="lb-row {{ $rank === 1 ? 'lead' : '' }}">
            <div class="lb-rank">{{ $rank }}</div>
            <div class="lb-who">
                <div class="lb-name-line">
                    @if ($rank === 1)<i class="fa-solid fa-crown lb-crown" aria-hidden="true"></i>@endif
                    <span class="lb-name">{{ $name }}</span>
                    @if ($rank === 1)<span class="lb-tag">Leading</span>@endif
                </div>
                <div class="lb-sub">
                    {{ $kelas ?? '-' }} &middot; {{ $item->quiz?->title }}
                </div>
                <div class="lb-bar"><i style="width: {{ max(0, min(100, $score)) }}%;"></i></div>
            </div>
            <div class="lb-pts">{{ rtrim(rtrim(number_format($score, 1), '0'), '.') }}<small>/100</small></div>
            <div class="lb-time"><i class="fa-regular fa-clock"></i> {{ $fmtTime($item->durationSeconds()) }}</div>
            <div class="lb-actions">
                <a href="{{ $showUrl }}" class="btn btn-cyan btn-sm" title="Lihat detail jawaban"><i class="fa-solid fa-list-check"></i> Detail</a>
                <form method="POST" action="{{ $deleteUrl }}"
                      onsubmit="return confirm('Hapus riwayat kuis {{ e($name) }}? Skor & jawabannya hilang permanen, dan {{ $isKelompok ? 'kelompok' : 'siswa' }} ini bisa mengisi kuis lagi dari awal.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-pink btn-sm" title="Hapus riwayat"><i class="fa-solid fa-trash"></i></button>
                </form>
            </div>
        </div>
    @empty
        <p class="lb-empty">Belum ada {{ $isKelompok ? 'kelompok' : 'siswa' }} yang menyelesaikan kuis ini.</p>
    @endforelse

    @foreach ($waiting as $w)
        <div class="lb-row wait">
            <div class="lb-rank">&ndash;</div>
            <div class="lb-who">
                <div class="lb-name-line"><span class="lb-name">{{ $w['name'] }}</span></div>
                <div class="lb-sub">{{ $w['kelas'] ?? '-' }}</div>
                <div class="lb-bar"><i style="width: 0%;"></i></div>
            </div>
            <div class="lb-pts">&ndash;</div>
            <div class="lb-time">{{ $w['playing'] ? 'Playing' : 'Waiting' }}</div>
            <div class="lb-actions">
                @if ($w['reset_url'])
                    <form method="POST" action="{{ $w['reset_url'] }}"
                          onsubmit="return confirm('Reset kuis {{ e($w['name']) }} yang sedang berjalan? Jawaban yang sudah masuk dihapus dan dia mulai dari awal.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-dark btn-sm" title="Reset kuis yang sedang berjalan"><i class="fa-solid fa-rotate-left"></i></button>
                    </form>
                @endif
            </div>
        </div>
    @endforeach

    @if ($waitingTotal > $waiting->count())
        <p class="lb-more">+ {{ $waitingTotal - $waiting->count() }} lainnya.</p>
    @endif
</div>
