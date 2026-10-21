@php($artId = $artId ?? 'heroArt')
<svg class="art" id="{{ $artId }}" viewBox="0 0 480 440" role="img" aria-label="Illustration of a tropical island with a volcano, a palm tree, a boat and a plane in the sky">
  <rect class="sky" width="480" height="440"/>
  <circle class="halo" cx="378" cy="104" r="74"/>
  <circle class="sun" cx="378" cy="104" r="48"/>
  <g class="cloud"><ellipse cx="92" cy="86" rx="40" ry="14"/><ellipse cx="120" cy="76" rx="26" ry="14"/><ellipse cx="68" cy="78" rx="24" ry="12"/></g>
  <g class="cloud"><ellipse cx="410" cy="226" rx="34" ry="11"/><ellipse cx="430" cy="218" rx="20" ry="11"/></g>
  <path class="trail" d="M28 236 C 90 226, 170 188, 232 132"/>
  <g transform="translate(252 112) rotate(58) scale(2.3)"><use class="plane" href="#i-plane" x="-12" y="-12" width="24" height="24"/></g>
  <path class="mtn" d="M62 322 L152 188 Q170 166 188 188 L282 322 Z"/>
  <path class="mtn2" d="M170 170 Q180 164 188 188 L282 322 L196 322 Z"/>
  <path class="sea1" d="M0 296 Q40 286 80 296 T160 296 T240 296 T320 296 T400 296 T480 296 V440 H0Z"/>
  <ellipse class="sand" cx="190" cy="322" rx="124" ry="22"/>
  <path class="hill" d="M104 322 Q146 292 196 308 Q238 292 276 322 Z"/>
  <path class="trunk" d="M248 324 Q258 292 242 260"/>
  <path class="frond" d="M242 260 Q220 246 204 258"/>
  <path class="frond" d="M242 260 Q230 236 212 234"/>
  <path class="frond" d="M242 260 Q248 234 268 230"/>
  <path class="frond" d="M242 260 Q268 246 284 260"/>
  <path class="frond" d="M242 260 Q260 268 274 282"/>
  <path class="sea2" d="M0 342 Q40 332 80 342 T160 342 T240 342 T320 342 T400 342 T480 342 V440 H0Z"/>
  <path class="sail" d="M394 356 L394 318 L424 356Z"/>
  <path class="hull" d="M382 356 H432 L424 370 H390Z"/>
  <path class="sea3" d="M0 394 Q40 384 80 394 T160 394 T240 394 T320 394 T400 394 T480 394 V440 H0Z"/>
</svg>
