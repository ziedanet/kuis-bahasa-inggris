{{-- $step: 0 = Check-in, 1 = Session 1, 2 = Session 2, 3 = Arrival --}}
@php($labels = ['Check-in', 'Session 1', 'Session 2', 'Arrival'])
<ol class="route" aria-label="Journey progress">
    @foreach ($labels as $i => $label)
        <li class="{{ $i < $step ? 'done' : ($i === $step ? 'now' : '') }}" @if ($i === $step) aria-current="step" @endif>
            <span class="dot" aria-hidden="true">
                @if ($i < $step)
                    <svg class="ico"><use href="#i-check"/></svg>
                @elseif ($i === $step)
                    <svg class="ico" style="transform: rotate(180deg);"><use href="#i-plane"/></svg>
                @endif
            </span>
            <span class="lbl">{{ $label }}</span>
        </li>
    @endforeach
</ol>
