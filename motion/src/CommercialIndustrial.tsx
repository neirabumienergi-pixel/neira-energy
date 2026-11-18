import React from "react";
import {
  AbsoluteFill,
  Easing,
  interpolate,
  interpolateColors,
  spring,
  useCurrentFrame,
  useVideoConfig,
} from "remotion";
import { loadFont } from "@remotion/google-fonts/Poppins";
import {
  Box,
  P3,
  U,
  iso,
  leftFace,
  onLeft,
  onRight,
  pts,
  rightFace,
  topFace,
} from "./iso";

const { fontFamily } = loadFont("normal", {
  weights: ["400", "600", "700"],
  subsets: ["latin"],
});

// Brand colours (resources/css/app.css)
const BRAND = {
  green500: "#0e9447",
  green600: "#0b7a3a",
  green900: "#083f22",
  green950: "#042414",
  orange500: "#f7941d",
  orange400: "#f9a845",
  sun300: "#fbe38a",
  sun400: "#f9d84a",
};

const OX = 540;
const OY = 372;

type Palette = { top: string; right: string; left: string };

type Building = {
  id: string;
  label: string;
  box: Box;
  palette: Palette;
  delay: number;
  windows: (b: Box) => P3[][];
  accents?: (b: Box) => { poly: P3[]; fill: string }[];
};

const grid = (
  face: typeof onLeft,
  b: Box,
  cols: number,
  rows: number,
  [u0, u1]: [number, number],
  [v0, v1]: [number, number],
  gapU = 0.35,
  gapV = 0.35,
) => {
  const out: P3[][] = [];
  const cw = (u1 - u0) / cols;
  const rh = (v1 - v0) / rows;
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      const a = u0 + c * cw + (cw * gapU) / 2;
      const bb = u0 + (c + 1) * cw - (cw * gapU) / 2;
      const v = v0 + r * rh + (rh * gapV) / 2;
      const w = v0 + (r + 1) * rh - (rh * gapV) / 2;
      out.push(face(b, a, bb, v, w));
    }
  }
  return out;
};

const BUILDINGS: Building[] = [
  {
    id: "mall",
    label: "Gedung Komersial",
    box: { x: 1, y: 1, z: 0, w: 6, d: 5, h: 3.2 },
    palette: { top: "#f3efe6", right: "#ffffff", left: "#d7dde4" },
    delay: 20,
    windows: (b) => [
      ...grid(onLeft, b, 5, 1, [0.06, 0.94], [0.08, 0.5], 0.12, 0),
      ...grid(onRight, b, 4, 1, [0.08, 0.92], [0.08, 0.5], 0.12, 0),
    ],
    accents: (b) => [
      { poly: onLeft(b, 0, 1, 0.66, 0.8), fill: BRAND.orange500 },
      { poly: onRight(b, 0, 1, 0.66, 0.8), fill: BRAND.orange400 },
    ],
  },
  {
    id: "factory",
    label: "Pabrik",
    box: { x: 10, y: 1, z: 0, w: 7, d: 6, h: 2.6 },
    palette: { top: "#e4ebe7", right: "#f3f6f4", left: "#c6d2cc" },
    delay: 32,
    windows: (b) => [
      ...grid(onRight, b, 5, 1, [0.06, 0.94], [0.58, 0.82], 0.3, 0),
      ...grid(onLeft, b, 6, 1, [0.05, 0.6], [0.6, 0.82], 0.3, 0),
    ],
    accents: (b) => [
      { poly: onLeft(b, 0.66, 0.8, 0, 0.55), fill: "#8fa39a" },
      { poly: onLeft(b, 0.84, 0.97, 0, 0.55), fill: "#8fa39a" },
      { poly: onRight(b, 0, 1, 0.92, 1), fill: BRAND.green500 },
      { poly: onLeft(b, 0, 1, 0.92, 1), fill: BRAND.green600 },
    ],
  },
  {
    id: "tower",
    label: "Gedung Kantor",
    box: { x: 1, y: 9.5, z: 0, w: 3.5, d: 3.5, h: 7 },
    palette: { top: "#e8eef5", right: "#f6f9fc", left: "#cbd6e2" },
    delay: 44,
    windows: (b) => [
      ...grid(onLeft, b, 3, 9, [0.08, 0.92], [0.06, 0.94], 0.3, 0.38),
      ...grid(onRight, b, 3, 9, [0.08, 0.92], [0.06, 0.94], 0.3, 0.38),
    ],
  },
  {
    id: "warehouse",
    label: "Gudang",
    box: { x: 10.5, y: 11, z: 0, w: 6, d: 5.5, h: 2.2 },
    palette: { top: "#eee7dc", right: "#fbf7f0", left: "#d6ccbd" },
    delay: 56,
    windows: (b) => [
      ...grid(onRight, b, 4, 1, [0.08, 0.92], [0.62, 0.84], 0.35, 0),
      ...grid(onLeft, b, 2, 1, [0.6, 0.95], [0.62, 0.84], 0.35, 0),
    ],
    accents: (b) => [
      { poly: onLeft(b, 0.08, 0.5, 0, 0.62), fill: "#a69a87" },
      { poly: onLeft(b, 0, 1, 0.9, 1), fill: BRAND.orange500 },
      { poly: onRight(b, 0, 1, 0.9, 1), fill: BRAND.orange400 },
    ],
  },
];

const HUB: Box = { x: 6.3, y: 12.3, z: 0, w: 1.8, d: 1.8, h: 1.3 };

// Energy routes from the hub to each building (world coords on ground)
const ROUTES: Record<string, [number, number][]> = {
  mall: [
    [7.2, 12.3],
    [7.2, 9],
    [5.5, 9],
    [5.5, 6],
  ],
  factory: [
    [8.1, 12.7],
    [9.25, 12.7],
    [9.25, 8.6],
    [13, 8.6],
    [13, 7],
  ],
  tower: [
    [6.3, 12.6],
    [5.4, 12.6],
    [5.4, 11.5],
    [4.5, 11.5],
  ],
  warehouse: [
    [8.1, 13.6],
    [10.5, 13.6],
  ],
};

const TREES: [number, number, number][] = [
  [8.2, 1.2, 0.9],
  [9, 6.2, 0.8],
  [5.2, 16, 1],
  [7.8, 16.5, 0.85],
  [3, 15, 0.9],
  [17.2, 9.5, 0.8],
  [15, 9.3, 0.9],
  [0.8, 7.6, 0.8],
];

const PANEL_START = 112;
const ENERGY_START = 190;
const GLINT_START = 168;

const roofPanels = (b: Box) => {
  const margin = 0.45;
  const pw = 1.15;
  const pd = 0.75;
  const gap = 0.18;
  const cols = Math.floor((b.w - 2 * margin + gap) / (pw + gap));
  const rows = Math.floor((b.d - 2 * margin + gap) / (pd + gap));
  const offX = (b.w - (cols * pw + (cols - 1) * gap)) / 2;
  const offY = (b.d - (rows * pd + (rows - 1) * gap)) / 2;
  const out: { x: number; y: number; w: number; d: number }[] = [];
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      out.push({
        x: b.x + offX + c * (pw + gap),
        y: b.y + offY + r * (pd + gap),
        w: pw,
        d: pd,
      });
    }
  }
  return out;
};

const screenPolyline = (route: [number, number][]) =>
  route.map(([x, y]) => iso([x, y, 0.02]));

const polyLength = (ps: [number, number][]) => {
  let l = 0;
  for (let i = 1; i < ps.length; i++) {
    l += Math.hypot(ps[i][0] - ps[i - 1][0], ps[i][1] - ps[i - 1][1]);
  }
  return l;
};

const pointAt = (ps: [number, number][], t: number): [number, number] => {
  const total = polyLength(ps);
  let target = t * total;
  for (let i = 1; i < ps.length; i++) {
    const seg = Math.hypot(ps[i][0] - ps[i - 1][0], ps[i][1] - ps[i - 1][1]);
    if (target <= seg) {
      const k = seg === 0 ? 0 : target / seg;
      return [
        ps[i - 1][0] + (ps[i][0] - ps[i - 1][0]) * k,
        ps[i - 1][1] + (ps[i][1] - ps[i - 1][1]) * k,
      ];
    }
    target -= seg;
  }
  return ps[ps.length - 1];
};

const BoxShape: React.FC<{ b: Box; p: Palette; stroke?: string }> = ({
  b,
  p,
  stroke = "rgba(8,63,34,0.18)",
}) => (
  <g strokeLinejoin="round" stroke={stroke} strokeWidth={1}>
    <polygon points={pts(leftFace(b))} fill={p.left} />
    <polygon points={pts(rightFace(b))} fill={p.right} />
    <polygon points={pts(topFace(b))} fill={p.top} />
  </g>
);

export const CommercialIndustrial: React.FC = () => {
  const frame = useCurrentFrame();
  const { fps, durationInFrames } = useVideoConfig();

  // Camera
  const zoom = interpolate(frame, [0, durationInFrames], [0.97, 1.05]);
  const drift = Math.sin(frame / 40) * 4;

  // Ground
  const groundIn = spring({ frame, fps, config: { damping: 200 } });
  const groundY = interpolate(groundIn, [0, 1], [80, 0]);

  // Sun
  const sunIn = spring({ frame: frame - 95, fps, config: { damping: 18 } });
  const sunY = interpolate(sunIn, [0, 1], [330, 175]);

  // Glint sweeping across panels (screen x)
  const glintX = interpolate(frame, [GLINT_START, GLINT_START + 50], [-650, 650], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
    easing: Easing.inOut(Easing.quad),
  });

  // Text
  const eyebrowIn = spring({ frame: frame - 6, fps, config: { damping: 200 } });
  const titleIn = spring({ frame: frame - 14, fps, config: { damping: 200 } });
  const sub1 = interpolate(frame, [118, 132, 232, 244], [0, 1, 1, 0], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  const sub2 = interpolate(frame, [250, 264], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  // Hub
  const hubIn = spring({ frame: frame - 70, fps, config: { damping: 14 } });
  const hubBox: Box = { ...HUB, h: HUB.h * hubIn };
  const hubPulseActive = frame >= ENERGY_START - 6;

  // Draw order: back to front
  const ordered = [...BUILDINGS].sort(
    (a, b) => a.box.x + a.box.w / 2 + a.box.y + a.box.d / 2 - (b.box.x + b.box.w / 2 + b.box.y + b.box.d / 2),
  );

  const labelData: {
    id: string;
    label: string;
    x: number;
    y: number;
    s: number;
  }[] = [];

  const renderBuilding = (bd: Building, idx: number) => {
    const g = spring({
      frame: frame - bd.delay,
      fps,
      config: { damping: 13, mass: 0.8 },
    });
    if (g <= 0.001) return null;
    const b: Box = { ...bd.box, h: bd.box.h * g };

    const routeIdx = BUILDINGS.findIndex((x) => x.id === bd.id);
    const arrive = ENERGY_START + routeIdx * 10 + 28;
    const lit = interpolate(frame, [arrive, arrive + 18], [0, 1], {
      extrapolateLeft: "clamp",
      extrapolateRight: "clamp",
    });

    const panels = roofPanels(bd.box).sort((a, c) => a.x + a.y - (c.x + c.y));
    const panelStart = PANEL_START + idx * 14;
    const roofZ = b.z + b.h;

    const top = iso([
      bd.box.x + bd.box.w / 2,
      bd.box.y + bd.box.d / 2,
      bd.box.h,
    ]);
    const labelS = spring({
      frame: frame - (panelStart + 18),
      fps,
      config: { damping: 12 },
    });
    labelData.push({
      id: bd.id,
      label: bd.label,
      x: top[0],
      y: top[1] - 34,
      s: labelS,
    });

    return (
      <g key={bd.id}>
        {/* soft contact shadow */}
        <polygon
          points={pts([
            [b.x + 0.2, b.y + b.d, 0],
            [b.x + b.w, b.y + b.d, 0],
            [b.x + b.w + 0.9, b.y + b.d + 0.9, 0],
            [b.x + 1.1, b.y + b.d + 0.9, 0],
          ])}
          fill="rgba(8,63,34,0.10)"
          opacity={g}
        />
        <BoxShape b={b} p={bd.palette} />
        {bd.windows(b).map((w, i) => {
          const flicker = interpolate(
            lit,
            [0, 1],
            [0, 1],
          ) * (0.85 + 0.15 * Math.sin(frame / 7 + i * 1.7));
          const fill = interpolateColors(
            Math.min(1, flicker),
            [0, 1],
            ["#6f8aa3", BRAND.sun400],
          );
          return (
            <polygon
              key={i}
              points={pts(w)}
              fill={fill}
              stroke="rgba(4,36,20,0.25)"
              strokeWidth={0.8}
            />
          );
        })}
        {(bd.accents?.(b) ?? []).map((a, i) => (
          <polygon key={`a${i}`} points={pts(a.poly)} fill={a.fill} />
        ))}
        {/* parapet line */}
        <polygon
          points={pts(topFace({ ...b, x: b.x + 0.18, y: b.y + 0.18, w: b.w - 0.36, d: b.d - 0.36 }))}
          fill="rgba(8,63,34,0.06)"
        />
        {/* rooftop solar panels */}
        {panels.map((pn, i) => {
          const p = spring({
            frame: frame - (panelStart + i * 1.4),
            fps,
            config: { damping: 15, mass: 0.6 },
          });
          if (p <= 0.001 || g < 0.98) return null;
          const lift = (1 - p) * 5;
          const z0 = roofZ + 0.12 + lift;
          const z1 = roofZ + 0.42 + lift;
          const poly: P3[] = [
            [pn.x, pn.y, z1],
            [pn.x + pn.w, pn.y, z1],
            [pn.x + pn.w, pn.y + pn.d, z0],
            [pn.x, pn.y + pn.d, z0],
          ];
          const [sx] = iso([pn.x + pn.w / 2, pn.y + pn.d / 2, z0]);
          const shine = Math.exp(-(((sx - glintX) / 70) ** 2));
          const fill = interpolateColors(shine, [0, 1], ["#1f3f72", "#8fb8f0"]);
          const mid = (a: P3, c: P3, k: number): P3 => [
            a[0] + (c[0] - a[0]) * k,
            a[1] + (c[1] - a[1]) * k,
            a[2] + (c[2] - a[2]) * k,
          ];
          const lines: [P3, P3][] = [
            [mid(poly[0], poly[1], 1 / 3), mid(poly[3], poly[2], 1 / 3)],
            [mid(poly[0], poly[1], 2 / 3), mid(poly[3], poly[2], 2 / 3)],
            [mid(poly[0], poly[3], 0.5), mid(poly[1], poly[2], 0.5)],
          ];
          return (
            <g key={`p${i}`} opacity={Math.min(1, p * 1.5)}>
              {/* support / shadow */}
              <polygon
                points={pts([
                  [pn.x, pn.y + pn.d, roofZ + lift],
                  [pn.x + pn.w, pn.y + pn.d, roofZ + lift],
                  [pn.x + pn.w, pn.y + pn.d, z0],
                  [pn.x, pn.y + pn.d, z0],
                ])}
                fill="#93a5b8"
              />
              <polygon
                points={pts(poly)}
                fill={fill}
                stroke="#c9d9ee"
                strokeWidth={1}
                strokeLinejoin="round"
              />
              {lines.map(([a, c], k) => {
                const [x1, y1] = iso(a);
                const [x2, y2] = iso(c);
                return (
                  <line
                    key={k}
                    x1={x1}
                    y1={y1}
                    x2={x2}
                    y2={y2}
                    stroke="rgba(201,217,238,0.55)"
                    strokeWidth={0.7}
                  />
                );
              })}
            </g>
          );
        })}
      </g>
    );
  };

  const renderTree = ([x, y, s]: [number, number, number], i: number) => {
    const t = spring({ frame: frame - (60 + i * 4), fps, config: { damping: 12 } });
    if (t <= 0.001) return null;
    const [bx, by] = iso([x, y, 0]);
    const sc = s * t;
    return (
      <g key={`t${i}`} transform={`translate(${bx},${by}) scale(${sc})`}>
        <ellipse cx={0} cy={2} rx={16} ry={8} fill="rgba(8,63,34,0.14)" />
        <rect x={-2.5} y={-18} width={5} height={20} fill="#8a6a45" />
        <circle cx={0} cy={-30} r={17} fill={BRAND.green600} />
        <circle cx={-5} cy={-35} r={11} fill={BRAND.green500} />
      </g>
    );
  };

  // Tree draw-order: mix trees with buildings by depth
  type Item = { depth: number; node: React.ReactNode };
  const items: Item[] = [];
  ordered.forEach((bd, i) => {
    const idx = BUILDINGS.indexOf(bd);
    items.push({
      depth: bd.box.x + bd.box.w + bd.box.y + bd.box.d,
      node: renderBuilding(bd, idx),
    });
    void i;
  });
  TREES.forEach((t, i) => items.push({ depth: t[0] + t[1] + 0.5, node: renderTree(t, i) }));
  items.push({
    depth: HUB.x + HUB.w + HUB.y + HUB.d,
    node: (
      <g key="hub">
        {hubIn > 0.001 ? (
          <>
            <BoxShape
              b={hubBox}
              p={{ top: BRAND.orange400, right: "#ffffff", left: "#dfe6ec" }}
            />
            {/* lightning icon on +y face */}
            {(() => {
              const [cx, cy] = iso([HUB.x + HUB.w / 2, HUB.y + HUB.d, hubBox.h / 2]);
              return (
                <g transform={`translate(${cx},${cy}) skewY(26.5) scale(${hubIn})`}>
                  <polygon
                    points="2,-12 -7,2 -1,2 -3,12 7,-3 1,-3"
                    fill={BRAND.orange500}
                  />
                </g>
              );
            })()}
          </>
        ) : null}
      </g>
    ),
  });
  items.sort((a, b) => a.depth - b.depth);

  // Pulse rings from hub
  const hubTop = iso([HUB.x + HUB.w / 2, HUB.y + HUB.d / 2, HUB.h]);
  const pulses = hubPulseActive
    ? [0, 1, 2].map((k) => {
        const local = (frame - (ENERGY_START - 6) + k * 20) % 60;
        const r = interpolate(local, [0, 60], [10, 90]);
        const o = interpolate(local, [0, 60], [0.6, 0]);
        return (
          <ellipse
            key={k}
            cx={hubTop[0]}
            cy={hubTop[1]}
            rx={r}
            ry={r * 0.5}
            fill="none"
            stroke={BRAND.orange500}
            strokeWidth={2.5}
            opacity={o}
          />
        );
      })
    : null;

  const ground: Box = { x: 0, y: 0, z: -0.8, w: 18, d: 18, h: 0.8 };

  return (
    <AbsoluteFill
      style={{
        background: `radial-gradient(120% 90% at 75% 15%, #fff7e6 0%, #f1f9f4 45%, #dff1e6 100%)`,
        fontFamily,
        overflow: "hidden",
      }}
    >
      {/* Sun */}
      <div
        style={{
          position: "absolute",
          left: 930 - 70,
          top: sunY - 70,
          width: 140,
          height: 140,
          opacity: sunIn,
        }}
      >
        <svg width={140} height={140} viewBox="-70 -70 140 140" style={{ overflow: "visible" }}>
          <g transform={`rotate(${frame * 0.6})`}>
            {Array.from({ length: 12 }).map((_, i) => (
              <rect
                key={i}
                x={-3}
                y={-68}
                width={6}
                height={16}
                rx={3}
                fill={BRAND.sun400}
                transform={`rotate(${i * 30})`}
              />
            ))}
          </g>
          <circle r={70 + Math.sin(frame / 10) * 4} fill={BRAND.sun300} opacity={0.35} />
          <circle r={42} fill={BRAND.sun400} />
          <circle r={42} fill="url(#sunG)" />
          <defs>
            <radialGradient id="sunG" cx="35%" cy="35%" r="70%">
              <stop offset="0%" stopColor="#fff6c8" />
              <stop offset="100%" stopColor={BRAND.orange500} stopOpacity={0.6} />
            </radialGradient>
          </defs>
        </svg>
      </div>

      {/* Scene with camera */}
      <AbsoluteFill
        style={{
          transform: `translateY(${groundY + drift}px) scale(${zoom})`,
          transformOrigin: `${OX}px ${OY + 9 * U}px`,
          opacity: groundIn,
        }}
      >
        <svg width={1080} height={1080} style={{ position: "absolute", inset: 0 }}>
          <g transform={`translate(${OX},${OY})`}>
            {/* ground slab */}
            <BoxShape
              b={ground}
              p={{ top: "#d8efe1", right: "#a9d4ba", left: "#8fc2a4" }}
              stroke="rgba(8,63,34,0.12)"
            />
            {/* plaza grid */}
            {Array.from({ length: 17 }).map((_, i) => {
              const k = i + 1;
              const [a1, b1] = iso([k, 0, 0]);
              const [a2, b2] = iso([k, 18, 0]);
              const [c1, d1] = iso([0, k, 0]);
              const [c2, d2] = iso([18, k, 0]);
              return (
                <g key={i} stroke="rgba(8,63,34,0.07)" strokeWidth={1}>
                  <line x1={a1} y1={b1} x2={a2} y2={b2} />
                  <line x1={c1} y1={d1} x2={c2} y2={d2} />
                </g>
              );
            })}
            {/* road */}
            <polygon
              points={pts([
                [0, 7.3, 0],
                [18, 7.3, 0],
                [18, 8.0, 0],
                [0, 8.0, 0],
              ])}
              fill="rgba(4,36,20,0.08)"
            />
            <polygon
              points={pts([
                [8.9, 0, 0],
                [9.6, 0, 0],
                [9.6, 18, 0],
                [8.9, 18, 0],
              ])}
              fill="rgba(4,36,20,0.06)"
            />

            {/* energy routes */}
            {BUILDINGS.map((bd, i) => {
              const sp = screenPolyline(ROUTES[bd.id]);
              const len = polyLength(sp);
              const start = ENERGY_START + i * 10;
              const draw = interpolate(frame, [start, start + 28], [0, 1], {
                extrapolateLeft: "clamp",
                extrapolateRight: "clamp",
                easing: Easing.out(Easing.cubic),
              });
              const d = `M ${sp.map(([x, y]) => `${x},${y}`).join(" L ")}`;
              return (
                <g key={`r${bd.id}`}>
                  <path
                    d={d}
                    fill="none"
                    stroke="rgba(8,63,34,0.12)"
                    strokeWidth={7}
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  />
                  {draw > 0 ? (
                    <>
                      <path
                        d={d}
                        fill="none"
                        stroke={BRAND.orange500}
                        strokeOpacity={0.35}
                        strokeWidth={10}
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeDasharray={len}
                        strokeDashoffset={len * (1 - draw)}
                      />
                      <path
                        d={d}
                        fill="none"
                        stroke={BRAND.sun400}
                        strokeWidth={3.5}
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeDasharray={len}
                        strokeDashoffset={len * (1 - draw)}
                      />
                    </>
                  ) : null}
                  {draw >= 1
                    ? [0, 1, 2].map((k) => {
                        const t = ((frame - start) / 45 + k / 3) % 1;
                        const [x, y] = pointAt(sp, t);
                        return (
                          <circle
                            key={k}
                            cx={x}
                            cy={y}
                            r={5}
                            fill="#fffbe6"
                            stroke={BRAND.orange500}
                            strokeWidth={2}
                          />
                        );
                      })
                    : null}
                </g>
              );
            })}

            {items.map((it, i) => (
              <React.Fragment key={i}>{it.node}</React.Fragment>
            ))}
            {pulses}
          </g>
        </svg>

        {/* labels */}
        {labelData.map((l) => (
          <div
            key={l.id}
            style={{
              position: "absolute",
              left: OX + l.x,
              top: OY + l.y,
              transform: `translate(-50%, -100%) scale(${l.s})`,
              transformOrigin: "50% 100%",
              opacity: Math.min(1, l.s * 1.4),
              background: "#ffffff",
              color: BRAND.green950,
              borderRadius: 999,
              padding: "8px 18px 8px 14px",
              fontSize: 22,
              fontWeight: 600,
              whiteSpace: "nowrap",
              display: "flex",
              alignItems: "center",
              gap: 10,
              boxShadow: "0 8px 20px rgba(4,36,20,0.14)",
            }}
          >
            <span
              style={{
                width: 12,
                height: 12,
                borderRadius: 999,
                background: BRAND.green500,
              }}
            />
            {l.label}
          </div>
        ))}
      </AbsoluteFill>

      {/* Title */}
      <div style={{ position: "absolute", left: 72, top: 64 }}>
        <div
          style={{
            fontSize: 24,
            fontWeight: 600,
            letterSpacing: 4,
            color: BRAND.orange500,
            opacity: eyebrowIn,
            transform: `translateY(${(1 - eyebrowIn) * 20}px)`,
          }}
        >
          SEGMEN PASAR
        </div>
        <div
          style={{
            marginTop: 6,
            fontSize: 68,
            lineHeight: 1.05,
            fontWeight: 700,
            color: BRAND.green950,
            opacity: titleIn,
            transform: `translateY(${(1 - titleIn) * 30}px)`,
          }}
        >
          Commercial &amp;
          <br />
          <span style={{ color: BRAND.green500 }}>Industrial</span>
        </div>
      </div>

      {/* Bottom captions */}
      <div
        style={{
          position: "absolute",
          left: 0,
          right: 0,
          bottom: 62,
          height: 50,
          textAlign: "center",
          fontSize: 32,
          fontWeight: 600,
          color: BRAND.green950,
        }}
      >
        <div
          style={{
            position: "absolute",
            inset: 0,
            opacity: sub1,
            transform: `translateY(${(1 - sub1) * 16}px)`,
          }}
        >
          PLTS atap untuk pabrik, gudang &amp; gedung komersial
        </div>
        <div
          style={{
            position: "absolute",
            inset: 0,
            opacity: sub2,
            transform: `translateY(${(1 - sub2) * 16}px)`,
          }}
        >
          Hemat biaya listrik <span style={{ color: BRAND.orange500 }}>·</span> Capai
          target ESG
        </div>
      </div>
    </AbsoluteFill>
  );
};
