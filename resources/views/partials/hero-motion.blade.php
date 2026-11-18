{{-- Motion graphic hero: sinar matahari, aliran energi, partikel. Animasi di resources/css/app.css (.hero-motion) --}}
<div class="hero-motion pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    <svg viewBox="0 0 1440 800" preserveAspectRatio="xMidYMid slice" class="h-full w-full">
        <defs>
            <radialGradient id="hm-sun" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#f9d84a" stop-opacity=".55"/>
                <stop offset="35%" stop-color="#f7941d" stop-opacity=".22"/>
                <stop offset="100%" stop-color="#f7941d" stop-opacity="0"/>
            </radialGradient>
            <linearGradient id="hm-flow" x1="0" x2="1">
                <stop offset="0%" stop-color="#34ad69" stop-opacity="0"/>
                <stop offset="50%" stop-color="#f9d84a" stop-opacity=".9"/>
                <stop offset="100%" stop-color="#f7941d" stop-opacity=".2"/>
            </linearGradient>
        </defs>

        {{-- Cahaya matahari --}}
        <circle class="hm-glow" cx="1080" cy="110" r="340" fill="url(#hm-sun)"/>
        <g class="hm-rays" opacity=".07">
            @for($i = 0; $i < 18; $i++)
                <polygon points="1080,110 {{ 1080 + 1400 * cos(deg2rad($i * 20 - 1.6)) }},{{ 110 + 1400 * sin(deg2rad($i * 20 - 1.6)) }} {{ 1080 + 1400 * cos(deg2rad($i * 20 + 1.6)) }},{{ 110 + 1400 * sin(deg2rad($i * 20 + 1.6)) }}" fill="#f9d84a"/>
            @endfor
        </g>

        {{-- Garis panel surya (perspektif) --}}
        <g opacity=".07" stroke="#ffffff" stroke-width="1">
            @for($i = 0; $i <= 12; $i++)
                <line x1="{{ -200 + $i * 140 }}" y1="800" x2="{{ 420 + $i * 52 }}" y2="560"/>
            @endfor
            @foreach([800, 740, 690, 645, 605, 575] as $y)
                <line x1="-200" y1="{{ $y }}" x2="1640" y2="{{ $y }}"/>
            @endforeach
        </g>

        {{-- Aliran energi --}}
        <g fill="none" stroke="url(#hm-flow)" stroke-linecap="round">
            <path class="hm-flow" style="--hm-dur:7s" stroke-width="2" d="M-100 620 C 300 520, 600 700, 900 520 S 1400 300, 1600 260"/>
            <path class="hm-flow" style="--hm-dur:9s;--hm-delay:-3s" stroke-width="1.5" d="M-100 700 C 250 640, 650 760, 1000 600 S 1450 420, 1600 380"/>
            <path class="hm-flow" style="--hm-dur:11s;--hm-delay:-6s" stroke-width="1" d="M-100 520 C 350 420, 700 560, 1050 420 S 1400 220, 1600 160"/>
            <path class="hm-flow" style="--hm-dur:8s;--hm-delay:-1s" stroke-width="1.2" d="M-100 760 C 400 720, 800 780, 1100 680 S 1500 560, 1600 540"/>
        </g>

        {{-- Partikel --}}
        <g fill="#f9d84a">
            @for($i = 0; $i < 26; $i++)
                @php($x = (($i * 397) % 1440))
                @php($y = 420 + (($i * 211) % 360))
                <circle class="hm-spark" cx="{{ $x }}" cy="{{ $y }}" r="{{ 1 + ($i % 3) * 0.8 }}"
                        style="--hm-dur:{{ 6 + ($i % 5) * 1.7 }}s;--hm-delay:-{{ ($i * 1.3) % 9 }}s"/>
            @endfor
        </g>
    </svg>
</div>
