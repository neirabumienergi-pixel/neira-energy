import React from "react";
import { AbsoluteFill, useCurrentFrame } from "remotion";
import { wave } from "./scene/SegmentScene";

/**
 * Light hero background (16:9, seamless 12 s loop).
 * Isometric line-art district along the edges, sun glow top-right,
 * energy pulses along the grid and drifting light particles.
 * The area behind the hero text (left-centre) is kept very faint.
 */

export const HERO_BG_FRAMES = 360;
const P = HERO_BG_FRAMES;

const W = 1920;
const H = 1080;
const OX = 960;
const OY = 560;
const U = 44;
const C = Math.cos(Math.PI / 6);

const ORANGE = "#f7941d";
const SUN = "#f9d84a";
const BG = "#f6f8f6";

type P3 = [number, number, number];
const pr = ([x, y, z]: P3): [number, number] => [OX + (x - y) * C * U, OY + ((x + y) * 0.5 - z) * U];
const pts = (ps: P3[]) => ps.map(pr).map(([a, b]) => `${a.toFixed(1)},${b.toFixed(1)}`).join(" ");

type B = { x: number; y: number; z?: number; w: number; d: number; h: number };
const faces = (b: B) => {
  const z = b.z ?? 0;
  return {
    top: [
      [b.x, b.y, z + b.h],
      [b.x + b.w, b.y, z + b.h],
      [b.x + b.w, b.y + b.d, z + b.h],
      [b.x, b.y + b.d, z + b.h],
    ] as P3[],
    right: [
      [b.x + b.w, b.y, z],
      [b.x + b.w, b.y + b.d, z],
      [b.x + b.w, b.y + b.d, z + b.h],
      [b.x + b.w, b.y, z + b.h],
    ] as P3[],
    left: [
      [b.x, b.y + b.d, z],
      [b.x + b.w, b.y + b.d, z],
      [b.x + b.w, b.y + b.d, z + b.h],
      [b.x, b.y + b.d, z + b.h],
    ] as P3[],
  };
};

const STROKE = "rgba(11,122,58,0.32)";

const Block: React.FC<{ b: B; tint?: string }> = ({ b, tint = "#ffffff" }) => {
  const f = faces(b);
  return (
    <g stroke={STROKE} strokeWidth={1.3} strokeLinejoin="round">
      <polygon points={pts(f.left)} fill="#e9f1ec" />
      <polygon points={pts(f.right)} fill="#f7faf8" />
      <polygon points={pts(f.top)} fill={tint} />
    </g>
  );
};

/** window rows on the +x (right) and +y (left) faces */
const Windows: React.FC<{ b: B; rows: number; cols: number; frame: number; seed: number }> = ({
  b,
  rows,
  cols,
  frame,
  seed,
}) => {
  const z = b.z ?? 0;
  const out: React.ReactNode[] = [];
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const v0 = z + b.h * (0.12 + (r * 0.78) / rows);
      const v1 = v0 + (b.h * 0.78) / rows * 0.55;
      const i = r * cols + c;
      const lit = 0.5 + 0.5 * wave(frame, 120, seed + i * 1.9);
      const fill = `rgba(249,216,74,${(0.18 + 0.4 * lit * ((i + seed) % 3 === 0 ? 1 : 0.35)).toFixed(3)})`;
      const u0 = (c + 0.25) / cols;
      const u1 = (c + 0.75) / cols;
      out.push(
        <polygon
          key={`r${i}`}
          points={pts([
            [b.x + b.w, b.y + b.d * u0, v0],
            [b.x + b.w, b.y + b.d * u1, v0],
            [b.x + b.w, b.y + b.d * u1, v1],
            [b.x + b.w, b.y + b.d * u0, v1],
          ])}
          fill={fill}
          stroke={STROKE}
          strokeWidth={0.8}
        />,
        <polygon
          key={`l${i}`}
          points={pts([
            [b.x + b.w * u0, b.y + b.d, v0],
            [b.x + b.w * u1, b.y + b.d, v0],
            [b.x + b.w * u1, b.y + b.d, v1],
            [b.x + b.w * u0, b.y + b.d, v1],
          ])}
          fill={fill}
          stroke={STROKE}
          strokeWidth={0.8}
        />,
      );
    }
  }
  return <>{out}</>;
};

/** Grid of tilted solar panels on a horizontal plane at height z */
const Panels: React.FC<{
  x: number;
  y: number;
  w: number;
  d: number;
  z: number;
  frame: number;
  pw?: number;
  pd?: number;
  gx?: number;
  gy?: number;
}> = ({ x, y, w, d, z, frame, pw = 1.1, pd = 0.7, gx = 0.18, gy = 0.3 }) => {
  const cols = Math.floor((w + gx) / (pw + gx));
  const rows = Math.floor((d + gy) / (pd + gy));
  const ox = x + (w - (cols * pw + (cols - 1) * gx)) / 2;
  const oy = y + (d - (rows * pd + (rows - 1) * gy)) / 2;
  // glint sweeps across the frame twice per loop
  const gxScreen = ((frame % (P / 2)) / (P / 2)) * (W + 800) - 400;
  const list: React.ReactNode[] = [];
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const px = ox + c * (pw + gx);
      const py = oy + r * (pd + gy);
      const poly: P3[] = [
        [px, py, z + 0.35],
        [px + pw, py, z + 0.35],
        [px + pw, py + pd, z + 0.08],
        [px, py + pd, z + 0.08],
      ];
      const [sx] = pr([px + pw / 2, py + pd / 2, z]);
      const shine = Math.exp(-(((sx - gxScreen) / 90) ** 2));
      list.push(
        <polygon
          key={`${r}-${c}`}
          points={pts(poly)}
          fill={`rgba(${Math.round(41 + 120 * shine)},${Math.round(79 + 110 * shine)},${Math.round(134 + 100 * shine)},${(0.22 + 0.3 * shine).toFixed(3)})`}
          stroke="rgba(31,63,114,0.35)"
          strokeWidth={1}
          strokeLinejoin="round"
        />,
      );
    }
  }
  return <>{list}</>;
};

const Gable: React.FC<{ b: B; frame: number; seed: number }> = ({ b, frame, seed }) => {
  const rh = 1.1;
  const T = b.h;
  const m = b.y + b.d / 2;
  const S = (u: number, v: number): P3 => [b.x + u * b.w, b.y + b.d - (v * b.d) / 2, T + v * rh + 0.05];
  const lit = 0.5 + 0.5 * wave(frame, 90, seed);
  return (
    <g>
      <Block b={b} />
      <polygon
        points={pts([
          [b.x + b.w, b.y + b.d * 0.35, 0.35 * b.h],
          [b.x + b.w, b.y + b.d * 0.65, 0.35 * b.h],
          [b.x + b.w, b.y + b.d * 0.65, 0.75 * b.h],
          [b.x + b.w, b.y + b.d * 0.35, 0.75 * b.h],
        ])}
        fill={`rgba(249,216,74,${(0.2 + 0.35 * lit).toFixed(3)})`}
        stroke={STROKE}
        strokeWidth={0.8}
      />
      <g stroke={STROKE} strokeWidth={1.3} strokeLinejoin="round">
        <polygon points={pts([[b.x, b.y, T], [b.x + b.w, b.y, T], [b.x + b.w, m, T + rh], [b.x, m, T + rh]])} fill="#f1e2d6" />
        <polygon points={pts([[b.x + b.w, b.y, T], [b.x + b.w, b.y + b.d, T], [b.x + b.w, m, T + rh]])} fill="#f7faf8" />
        <polygon points={pts([[b.x, b.y + b.d, T], [b.x + b.w, b.y + b.d, T], [b.x + b.w, m, T + rh], [b.x, m, T + rh]])} fill="#f6e6d9" />
      </g>
      {[0, 1].map((k) => {
        const u0 = 0.12 + k * 0.4;
        const u1 = u0 + 0.34;
        return (
          <polygon
            key={k}
            points={pts([S(u0, 0.85), S(u1, 0.85), S(u1, 0.22), S(u0, 0.22)])}
            fill="rgba(41,79,134,0.25)"
            stroke="rgba(31,63,114,0.35)"
            strokeWidth={1}
          />
        );
      })}
    </g>
  );
};

const Lamp: React.FC<{ x: number; y: number; frame: number; seed: number }> = ({ x, y, frame, seed }) => {
  const [bx, by] = pr([x, y, 0]);
  const [tx, ty] = pr([x, y, 2.8]);
  const [hx, hy] = pr([x, y - 0.55, 2.7]);
  const glow = 0.5 + 0.5 * wave(frame, 120, seed);
  return (
    <g>
      <line x1={bx} y1={by} x2={tx} y2={ty} stroke="rgba(11,122,58,0.35)" strokeWidth={2} />
      <line x1={tx} y1={ty} x2={hx} y2={hy} stroke="rgba(11,122,58,0.35)" strokeWidth={1.6} />
      <circle cx={hx} cy={hy + 2} r={9} fill={SUN} opacity={0.18 + 0.2 * glow} />
      <circle cx={hx} cy={hy + 2} r={3} fill="#fff6c8" stroke="rgba(11,122,58,0.4)" />
      <polygon
        points={pts([
          [x - 0.35, y - 0.25, 3.15],
          [x + 0.35, y - 0.25, 3.15],
          [x + 0.35, y + 0.25, 2.95],
          [x - 0.35, y + 0.25, 2.95],
        ])}
        fill="rgba(41,79,134,0.25)"
        stroke="rgba(31,63,114,0.35)"
      />
    </g>
  );
};

const Tree: React.FC<{ x: number; y: number; s?: number }> = ({ x, y, s = 1 }) => {
  const [bx, by] = pr([x, y, 0]);
  return (
    <g transform={`translate(${bx},${by}) scale(${s})`}>
      <ellipse cx={0} cy={2} rx={15} ry={7} fill="rgba(14,148,71,0.08)" />
      <line x1={0} y1={0} x2={0} y2={-16} stroke="rgba(11,122,58,0.35)" strokeWidth={2} />
      <circle cx={0} cy={-28} r={15} fill="rgba(14,148,71,0.14)" stroke={STROKE} strokeWidth={1.2} />
    </g>
  );
};

// Energy routes on the ground (world x,y)
const ROUTES: [number, number][][] = [
  // right cluster spine
  [
    [3, -6],
    [8, -6],
    [8, -1],
    [15, -1],
  ],
  [
    [8, -6],
    [8, -11],
  ],
  // bottom run, below the hero content
  [
    [-6, 15],
    [1, 15],
    [1, 11],
    [9, 11],
    [9, 1],
    [15, 1],
  ],
  // left column
  [
    [-12, 8.8],
    [-12, 12.4],
    [-10.75, 12.4],
  ],
];

const polyLen = (ps: [number, number][]) =>
  ps.slice(1).reduce((s, p, i) => s + Math.hypot(p[0] - ps[i][0], p[1] - ps[i][1]), 0);

const at = (ps: [number, number][], t: number): [number, number] => {
  let d = t * polyLen(ps);
  for (let i = 1; i < ps.length; i++) {
    const seg = Math.hypot(ps[i][0] - ps[i - 1][0], ps[i][1] - ps[i - 1][1]);
    if (d <= seg) {
      const k = d / seg;
      return [ps[i - 1][0] + (ps[i][0] - ps[i - 1][0]) * k, ps[i - 1][1] + (ps[i][1] - ps[i - 1][1]) * k];
    }
    d -= seg;
  }
  return ps[ps.length - 1];
};

// deterministic pseudo-random
const rnd = (i: number) => {
  const s = Math.sin(i * 127.1 + 311.7) * 43758.5453;
  return s - Math.floor(s);
};

export const HeroBackground: React.FC = () => {
  const frame = useCurrentFrame();
  const drift = wave(frame, P) * 10;
  const breathe = 0.5 + 0.5 * wave(frame, P / 2);

  // grid lines
  const gridLines: React.ReactNode[] = [];
  for (let k = -26; k <= 26; k++) {
    const a = pr([k, -30, 0]);
    const b = pr([k, 30, 0]);
    const c = pr([-30, k, 0]);
    const d = pr([30, k, 0]);
    gridLines.push(
      <line key={`a${k}`} x1={a[0]} y1={a[1]} x2={b[0]} y2={b[1]} />,
      <line key={`b${k}`} x1={c[0]} y1={c[1]} x2={d[0]} y2={d[1]} />,
    );
  }

  // Scene objects, painter-sorted by depth (x + y)
  type It = { depth: number; node: React.ReactNode };
  const items: It[] = [];
  const add = (depth: number, node: React.ReactNode) => items.push({ depth, node });

  // ---- right cluster (to the right of the hero card) ----
  const tower: B = { x: 9, y: -14, w: 2.6, d: 2.6, h: 7.5 };
  add(tower.x + tower.y + 5, (
    <g key="tower">
      <Block b={tower} />
      <Windows b={tower} rows={9} cols={3} frame={frame} seed={3} />
      <Panels x={tower.x + 0.2} y={tower.y + 0.2} w={2.2} d={2.2} z={tower.h} frame={frame} pw={0.95} pd={0.6} />
    </g>
  ));
  const factory: B = { x: 12, y: -9, w: 6, d: 5, h: 2.4 };
  add(factory.x + factory.y + 11, (
    <g key="factory">
      <Block b={factory} />
      <Windows b={factory} rows={1} cols={5} frame={frame} seed={7} />
      <Panels x={factory.x + 0.3} y={factory.y + 0.3} w={5.4} d={4.4} z={factory.h} frame={frame} />
    </g>
  ));
  add(16 + -2 + 5, <Gable key="h1" b={{ x: 13.5, y: -2.5, w: 2.8, d: 2.3, h: 1.6 }} frame={frame} seed={1} />);
  add(18 + 2 + 5, <Gable key="h2" b={{ x: 16, y: 1.5, w: 2.8, d: 2.3, h: 1.6 }} frame={frame} seed={2} />);
  add(21 - 7.6, (
    <g key="field-r">
      <Panels x={17} y={-8} w={5} d={4} z={0} frame={frame} pw={1.2} pd={0.8} gx={0.25} gy={0.5} />
    </g>
  ));

  // ---- left column (left of the hero text) ----
  const gov: B = { x: -14.8, y: 5.85, w: 3.5, d: 2.6, h: 2.6 };
  add(gov.x + gov.w + gov.y + gov.d, (
    <g key="gov">
      <Block b={gov} />
      <Windows b={gov} rows={2} cols={2} frame={frame} seed={11} />
      <Panels x={gov.x + 0.3} y={gov.y + 0.3} w={2.9} d={2.0} z={gov.h} frame={frame} pw={0.9} pd={0.6} />
    </g>
  ));
  add(-10.75 + 2.8 + 11.8 + 2.3, <Gable key="h3" b={{ x: -10.75, y: 11.8, w: 2.8, d: 2.3, h: 1.6 }} frame={frame} seed={4} />);
  add(-3.8 + 2.8 + 14.85 + 2.3, <Gable key="h4" b={{ x: -3.8, y: 14.85, w: 2.8, d: 2.3, h: 1.6 }} frame={frame} seed={5} />);

  // ---- bottom strip (below the hero content) ----
  add(1 + 8 + 11.4 + 3.5, (
    <g key="field-b">
      <Panels x={1} y={11.4} w={8} d={3.5} z={0} frame={frame} pw={1.2} pd={0.8} gx={0.25} gy={0.5} />
    </g>
  ));

  // lamps & trees
  [
    [10.5, -2.2],
    [14.5, 0.2],
  ].forEach(([x, y], i) => add(x + y + 0.5, <Lamp key={`lamp${i}`} x={x} y={y} frame={frame} seed={i * 1.7} />));
  [
    [7, -12, 1],
    [19.5, -3, 0.9],
    [12, 3, 0.9],
    [-17, 6.5, 1],
    [-5, 11.5, 0.9],
    [-4.3, 19.8, 0.9],
    [10, 10, 0.9],
  ].forEach(([x, y, s], i) => add(x + y + 0.3, <Tree key={`tree${i}`} x={x} y={y} s={s} />));

  items.sort((a, b) => a.depth - b.depth);

  // energy pulses
  const pulses = ROUTES.map((r, ri) => {
    const sp = r.map(([x, y]) => pr([x, y, 0.02]));
    const d = `M ${sp.map(([x, y]) => `${x.toFixed(1)},${y.toFixed(1)}`).join(" L ")}`;
    const dots = [0, 1].map((k) => {
      const t = ((frame / P) * 2 + k / 2 + ri * 0.17) % 1;
      const [x, y] = pr([...at(r, t), 0.02] as P3);
      const [x2, y2] = pr([...at(r, Math.max(0, t - 0.06)), 0.02] as P3);
      return (
        <g key={k}>
          <line x1={x2} y1={y2} x2={x} y2={y} stroke={SUN} strokeWidth={4} strokeLinecap="round" opacity={0.7} />
          <circle cx={x} cy={y} r={5} fill="#fffbe6" stroke={ORANGE} strokeWidth={1.5} />
        </g>
      );
    });
    return (
      <g key={ri}>
        <path d={d} fill="none" stroke="rgba(247,148,29,0.22)" strokeWidth={3} strokeLinecap="round" strokeLinejoin="round" />
        {dots}
      </g>
    );
  });

  // light particles drifting upward
  const particles = Array.from({ length: 26 }).map((_, i) => {
    const x0 = rnd(i) * W;
    const speed = 1 + (i % 2);
    const t = ((frame / P) * speed + rnd(i + 50)) % 1;
    const y = H - t * (H + 80);
    const x = x0 + wave(frame, P, i) * 18;
    const o = Math.sin(t * Math.PI) * (0.25 + 0.35 * rnd(i + 9));
    const r = 2 + rnd(i + 3) * 3;
    return <circle key={i} cx={x} cy={y} r={r} fill={i % 3 ? SUN : ORANGE} opacity={o} />;
  });

  return (
    <AbsoluteFill style={{ background: BG, overflow: "hidden" }}>
      <svg width={W} height={H} viewBox={`0 0 ${W} ${H}`}>
        <defs>
          <radialGradient id="warm" cx="88%" cy="6%" r="55%">
            <stop offset="0%" stopColor="#f9e7b8" stopOpacity={0.85 + 0.15 * breathe} />
            <stop offset="45%" stopColor="#f7efd9" stopOpacity={0.55} />
            <stop offset="100%" stopColor={BG} stopOpacity={0} />
          </radialGradient>
          <radialGradient id="ray" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(1700 40) scale(900)">
            <stop offset="0%" stopColor="#fbe38a" stopOpacity={0.35} />
            <stop offset="100%" stopColor="#fbe38a" stopOpacity={0} />
          </radialGradient>
          <radialGradient id="sunCore">
            <stop offset="0%" stopColor="#fbe38a" stopOpacity={0.75} />
            <stop offset="55%" stopColor="#fbe38a" stopOpacity={0.3} />
            <stop offset="100%" stopColor="#fbe38a" stopOpacity={0} />
          </radialGradient>
          {/* fade the ground grid in from the top */}
          <linearGradient id="gridFade" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0.3" stopColor="#fff" stopOpacity={0} />
            <stop offset="0.75" stopColor="#fff" stopOpacity={1} />
          </linearGradient>
          <mask id="gridMask">
            <rect width={W} height={H} fill="url(#gridFade)" />
          </mask>
          {/* keep the area behind the hero text quiet */}
          <radialGradient id="quiet" cx="34%" cy="52%" r="38%">
            <stop offset="0%" stopColor="#000" />
            <stop offset="55%" stopColor="#000" stopOpacity={0.85} />
            <stop offset="100%" stopColor="#000" stopOpacity={0} />
          </radialGradient>
          <mask id="sceneMask">
            <rect width={W} height={H} fill="#fff" />
            <rect width={W} height={H} fill="url(#quiet)" />
          </mask>
        </defs>

        <rect width={W} height={H} fill="url(#warm)" />
        {/* slow sun rays */}
        <g transform={`translate(1700 40) rotate(${(frame / P) * 30})`} opacity={0.55 + 0.25 * breathe}>
          {Array.from({ length: 12 }).map((_, i) => (
            <polygon key={i} points="0,0 -60,900 60,900" fill="url(#ray)" transform={`rotate(${i * 30})`} />
          ))}
        </g>
        <circle cx={1700} cy={40} r={170 + 14 * breathe} fill="url(#sunCore)" />

        <g transform={`translate(${drift} 0)`}>
          <g mask="url(#gridMask)" stroke="rgba(14,148,71,0.10)" strokeWidth={1}>
            {gridLines}
          </g>
          <g mask="url(#sceneMask)">
            {pulses}
            {items.map((it, i) => (
              <React.Fragment key={i}>{it.node}</React.Fragment>
            ))}
          </g>
        </g>
        {particles}
      </svg>
    </AbsoluteFill>
  );
};
