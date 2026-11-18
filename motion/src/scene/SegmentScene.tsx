import React from "react";
import {
  AbsoluteFill,
  Easing,
  Img,
  interpolate,
  interpolateColors,
  spring,
  staticFile,
  useCurrentFrame,
  useVideoConfig,
} from "remotion";
import { loadFont } from "@remotion/google-fonts/Poppins";
import { Box, P3, U, iso, leftFace, onLeft, pts, rightFace, topFace } from "../iso";

const { fontFamily } = loadFont("normal", {
  weights: ["400", "600", "700"],
  subsets: ["latin"],
});

// Brand colours (resources/css/app.css)
export const BRAND = {
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

export const PANEL_START = 112;
export const ENERGY_START = 190;
const GLINT_START = 168;

export type Palette = { top: string; right: string; left: string };

export type Building = {
  id: string;
  label?: string;
  box: Box;
  palette: Palette;
  delay: number;
  /** rooftop solar panels (default true) */
  panels?: boolean;
  windows?: (b: Box) => P3[][];
  accents?: (b: Box) => { poly: P3[]; fill: string }[];
  /** pitched roof, ridge along x; panels go on the front (+y) slope */
  gable?: { h: number; front: string; back: string };
};

export type Rect = { x: number; y: number; w: number; d: number };

export type ExtraCtx = {
  t: number; // grow-in progress 0..1
  frame: number;
  on: number; // energy arrived 0..1
  glintX: number;
};

/** Custom scene object (boat, water tower, ...) */
export type Extra = {
  id: string;
  fp: Rect | ((frame: number) => Rect);
  delay: number;
  label?: { text: string; anchor: P3; start?: number };
  render: (ctx: ExtraCtx) => React.ReactNode;
};

/** Ground-mounted solar array */
export type Field = {
  id: string;
  label?: string;
  area: { x: number; y: number; w: number; d: number };
};

/** Solar street light (PJU tenaga surya) */
export type Lamp = { x: number; y: number; arm: [number, number] };

/** Flag pole */
export type Flag = { x: number; y: number };

export type SegmentConfig = {
  title: [string, string];
  captions: [React.ReactNode, React.ReactNode];
  buildings: Building[];
  hub: Box;
  /** routes from hub to a building/field id (world x,y on ground) */
  routes: Record<string, [number, number][]>;
  trees: [number, number, number][];
  fields?: Field[];
  lamps?: Lamp[];
  lampLabel?: { text: string; index: number };
  flags?: Flag[];
  extras?: Extra[];
  /** water area on the ground (assumed to reach the front-left edge y=18) */
  water?: { x: number; y: number; w: number; d: number };
  roads?: { x: number; y: number; w: number; d: number }[];
  groundDecor?: { poly: P3[]; fill: string }[];
  /** screen-space overlay drawn above everything (e.g. an app card) */
  overlay?: (ctx: { frame: number; fps: number; fontFamily: string }) => React.ReactNode;
};

export const shineFill = (sx: number, glintX: number) =>
  interpolateColors(Math.exp(-(((sx - glintX) / 70) ** 2)), [0, 1], ["#1f3f72", "#8fb8f0"]);

/** Generic solar panel quad: corners [backL, backR, frontR, frontL] */
export const PanelQuad: React.FC<{ poly: P3[]; glintX: number; opacity?: number }> = ({
  poly,
  glintX,
  opacity = 1,
}) => {
  const c: P3 = [
    (poly[0][0] + poly[2][0]) / 2,
    (poly[0][1] + poly[2][1]) / 2,
    (poly[0][2] + poly[2][2]) / 2,
  ];
  const [sx] = iso(c);
  const lines: [P3, P3][] = [
    [mix(poly[0], poly[1], 1 / 3), mix(poly[3], poly[2], 1 / 3)],
    [mix(poly[0], poly[1], 2 / 3), mix(poly[3], poly[2], 2 / 3)],
    [mix(poly[0], poly[3], 0.5), mix(poly[1], poly[2], 0.5)],
  ];
  return (
    <g opacity={opacity}>
      <polygon points={pts(poly)} fill={shineFill(sx, glintX)} stroke="#c9d9ee" strokeWidth={1} strokeLinejoin="round" />
      {lines.map(([a, b], k) => {
        const [x1, y1] = iso(a);
        const [x2, y2] = iso(b);
        return <line key={k} x1={x1} y1={y1} x2={x2} y2={y2} stroke="rgba(201,217,238,0.55)" strokeWidth={0.7} />;
      })}
    </g>
  );
};

// ---------- helpers ----------

export const grid = (
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


const panelGrid = (
  area: Rect,
  margin: number,
  pw: number,
  pd: number,
  gapX: number,
  gapY: number,
): Rect[] => {
  const cols = Math.floor((area.w - 2 * margin + gapX) / (pw + gapX));
  const rows = Math.floor((area.d - 2 * margin + gapY) / (pd + gapY));
  const offX = (area.w - (cols * pw + (cols - 1) * gapX)) / 2;
  const offY = (area.d - (rows * pd + (rows - 1) * gapY)) / 2;
  const out: Rect[] = [];
  for (let r = 0; r < rows; r++) {
    for (let c = 0; c < cols; c++) {
      out.push({
        x: area.x + offX + c * (pw + gapX),
        y: area.y + offY + r * (pd + gapY),
        w: pw,
        d: pd,
      });
    }
  }
  return out.sort((a, b) => a.x + a.y - (b.x + b.y));
};

const polyLength = (ps: [number, number][]) => {
  let l = 0;
  for (let i = 1; i < ps.length; i++) {
    l += Math.hypot(ps[i][0] - ps[i - 1][0], ps[i][1] - ps[i - 1][1]);
  }
  return l;
};

const pointAt = (ps: [number, number][], t: number): [number, number] => {
  let target = t * polyLength(ps);
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

const mix = (a: P3, c: P3, k: number): P3 => [
  a[0] + (c[0] - a[0]) * k,
  a[1] + (c[1] - a[1]) * k,
  a[2] + (c[2] - a[2]) * k,
];

export const BoxShape: React.FC<{ b: Box; p: Palette; stroke?: string }> = ({
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

/** A tilted solar panel. Back edge (low y) is higher. */
const Panel: React.FC<{
  pn: Rect;
  baseZ: number;
  low: number;
  high: number;
  p: number;
  glintX: number;
}> = ({ pn, baseZ, low, high, p, glintX }) => {
  const lift = (1 - p) * 5;
  const z0 = baseZ + low + lift;
  const z1 = baseZ + high + lift;
  const poly: P3[] = [
    [pn.x, pn.y, z1],
    [pn.x + pn.w, pn.y, z1],
    [pn.x + pn.w, pn.y + pn.d, z0],
    [pn.x, pn.y + pn.d, z0],
  ];
  const [sx] = iso([pn.x + pn.w / 2, pn.y + pn.d / 2, z0]);
  const shine = Math.exp(-(((sx - glintX) / 70) ** 2));
  const fill = interpolateColors(shine, [0, 1], ["#1f3f72", "#8fb8f0"]);
  const lines: [P3, P3][] = [
    [mix(poly[0], poly[1], 1 / 3), mix(poly[3], poly[2], 1 / 3)],
    [mix(poly[0], poly[1], 2 / 3), mix(poly[3], poly[2], 2 / 3)],
    [mix(poly[0], poly[3], 0.5), mix(poly[1], poly[2], 0.5)],
  ];
  return (
    <g opacity={Math.min(1, p * 1.5)}>
      <polygon
        points={pts([
          [pn.x, pn.y + pn.d, baseZ + lift],
          [pn.x + pn.w, pn.y + pn.d, baseZ + lift],
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
};

// ---------- scene ----------

export const SegmentScene: React.FC<{ config: SegmentConfig }> = ({ config }) => {
  const frame = useCurrentFrame();
  const { fps, durationInFrames } = useVideoConfig();
  const { buildings, hub: HUB, routes, trees, fields = [], lamps = [], flags = [] } = config;

  const zoom = interpolate(frame, [0, durationInFrames], [0.97, 1.05]);
  const drift = Math.sin(frame / 40) * 4;

  const groundIn = spring({ frame, fps, config: { damping: 200 } });
  const groundY = interpolate(groundIn, [0, 1], [80, 0]);

  const sunIn = spring({ frame: frame - 95, fps, config: { damping: 18 } });
  const sunY = interpolate(sunIn, [0, 1], [330, 175]);

  const glintX = interpolate(frame, [GLINT_START, GLINT_START + 50], [-650, 650], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
    easing: Easing.inOut(Easing.quad),
  });

  const logoIn = spring({ frame: frame - 6, fps, config: { damping: 200 } });
  const titleIn = spring({ frame: frame - 14, fps, config: { damping: 200 } });
  const sub1 = interpolate(frame, [118, 132, 232, 244], [0, 1, 1, 0], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  const sub2 = interpolate(frame, [250, 264], [0, 1], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });

  const hubIn = spring({ frame: frame - 70, fps, config: { damping: 14 } });
  const hubBox: Box = { ...HUB, h: HUB.h * hubIn };

  // Route timing: index in the routes object
  const routeIds = Object.keys(routes);
  const arriveAt = (id: string) => {
    const i = routeIds.indexOf(id);
    return i < 0 ? ENERGY_START + 40 : ENERGY_START + i * 10 + 28;
  };

  // Panel install order: buildings with panels, then fields
  const panelOwners = [
    ...buildings.filter((b) => b.panels !== false).map((b) => b.id),
    ...fields.map((f) => f.id),
  ];
  const panelStartOf = (id: string) => PANEL_START + panelOwners.indexOf(id) * 14;

  const labels: { id: string; text: string; anchor: P3; start: number; dy?: number }[] = [];

  type Item = { depth: number; fp: Rect; node: React.ReactNode };
  const pt = (x: number, y: number): Rect => ({ x, y, w: 0.01, d: 0.01 });
  const items: Item[] = [];

  // Buildings
  buildings.forEach((bd) => {
    const g = spring({ frame: frame - bd.delay, fps, config: { damping: 13, mass: 0.8 } });
    const depth = bd.box.x + bd.box.w + bd.box.y + bd.box.d;
    if (g <= 0.001) return;
    const b: Box = { ...bd.box, h: bd.box.h * g };
    const arrive = arriveAt(bd.id);
    const lit = interpolate(frame, [arrive, arrive + 18], [0, 1], {
      extrapolateLeft: "clamp",
      extrapolateRight: "clamp",
    });
    const hasPanels = bd.panels !== false;
    const panelStart = hasPanels ? panelStartOf(bd.id) : 0;
    const roofZ = b.z + b.h;
    const panels = hasPanels
      ? panelGrid({ x: bd.box.x, y: bd.box.y, w: bd.box.w, d: bd.box.d }, 0.45, 1.15, 0.75, 0.18, 0.18)
      : [];

    if (bd.label) {
      labels.push({
        id: bd.id,
        text: bd.label,
        anchor: [bd.box.x + bd.box.w / 2, bd.box.y + bd.box.d / 2, bd.box.z + bd.box.h + (bd.gable?.h ?? 0)],
        start: (hasPanels ? panelStart : bd.delay + 60) + 18,
      });
    }

    items.push({
      depth,
      fp: { x: bd.box.x, y: bd.box.y, w: bd.box.w, d: bd.box.d },
      node: (
        <g key={bd.id}>
          <polygon
            points={pts([
              [b.x + 0.2, b.y + b.d, b.z],
              [b.x + b.w, b.y + b.d, b.z],
              [b.x + b.w + 0.9, b.y + b.d + 0.9, b.z],
              [b.x + 1.1, b.y + b.d + 0.9, b.z],
            ])}
            fill="rgba(8,63,34,0.10)"
            opacity={g}
          />
          <BoxShape b={b} p={bd.palette} />
          {(bd.windows?.(b) ?? []).map((w, i) => {
            const k = lit * (0.85 + 0.15 * Math.sin(frame / 7 + i * 1.7));
            return (
              <polygon
                key={i}
                points={pts(w)}
                fill={interpolateColors(Math.min(1, k), [0, 1], ["#6f8aa3", BRAND.sun400])}
                stroke="rgba(4,36,20,0.25)"
                strokeWidth={0.8}
              />
            );
          })}
          {(bd.accents?.(b) ?? []).map((a, i) => (
            <polygon key={`a${i}`} points={pts(a.poly)} fill={a.fill} />
          ))}
          {bd.gable ? (
            (() => {
              const rh = bd.gable.h * g;
              const T = roofZ;
              const m = b.y + b.d / 2;
              const S = (u: number, v: number, lift = 0): P3 => [
                b.x + u * b.w,
                b.y + b.d - (v * b.d) / 2,
                T + v * rh + 0.06 + lift,
              ];
              const cols = Math.max(1, Math.floor((b.w - 0.4) / 1.0));
              const slopePanels: [number, number][][] = [];
              for (let c = 0; c < cols; c++) {
                const u0 = 0.08 + (c * 0.84) / cols + 0.02;
                const u1 = 0.08 + ((c + 1) * 0.84) / cols - 0.02;
                slopePanels.push([[u0, 0.85], [u1, 0.85], [u1, 0.2], [u0, 0.2]]);
              }
              return (
                <g strokeLinejoin="round" stroke="rgba(8,63,34,0.18)" strokeWidth={1}>
                  <polygon points={pts([[b.x, b.y, T], [b.x + b.w, b.y, T], [b.x + b.w, m, T + rh], [b.x, m, T + rh]])} fill={bd.gable.back} />
                  <polygon points={pts([[b.x + b.w, b.y, T], [b.x + b.w, b.y + b.d, T], [b.x + b.w, m, T + rh]])} fill={bd.palette.right} />
                  <polygon points={pts([[b.x, b.y + b.d, T], [b.x + b.w, b.y + b.d, T], [b.x + b.w, m, T + rh], [b.x, m, T + rh]])} fill={bd.gable.front} />
                  {hasPanels && g >= 0.98
                    ? slopePanels.map((q, i) => {
                        const p = spring({ frame: frame - (panelStart + i * 2), fps, config: { damping: 15, mass: 0.6 } });
                        if (p <= 0.001) return null;
                        const lift = (1 - p) * 5;
                        const poly = q.map(([u, v]) => S(u, v, lift));
                        return <PanelQuad key={i} poly={poly} glintX={glintX} opacity={Math.min(1, p * 1.5)} />;
                      })
                    : null}
                </g>
              );
            })()
          ) : (
            <polygon
              points={pts(
                topFace({ ...b, x: b.x + 0.18, y: b.y + 0.18, w: b.w - 0.36, d: b.d - 0.36 }),
              )}
              fill="rgba(8,63,34,0.06)"
            />
          )}
          {g >= 0.98 && !bd.gable
            ? panels.map((pn, i) => {
                const p = spring({
                  frame: frame - (panelStart + i * 1.4),
                  fps,
                  config: { damping: 15, mass: 0.6 },
                });
                if (p <= 0.001) return null;
                return (
                  <Panel key={i} pn={pn} baseZ={roofZ} low={0.12} high={0.42} p={p} glintX={glintX} />
                );
              })
            : null}
        </g>
      ),
    });
  });

  // Ground-mounted fields
  fields.forEach((f) => {
    const start = panelStartOf(f.id);
    const panels = panelGrid(f.area, 0.3, 1.3, 0.9, 0.25, 0.55);
    if (f.label) {
      labels.push({
        id: f.id,
        text: f.label,
        anchor: [f.area.x + f.area.w / 2, f.area.y + f.area.d / 2, 0.8],
        start: start + 18,
      });
    }
    panels.forEach((pn, i) => {
      const p = spring({ frame: frame - (start + i * 0.6), fps, config: { damping: 15, mass: 0.6 } });
      if (p <= 0.001) return;
      items.push({
        depth: pn.x + pn.y + pn.w,
        fp: pn,
        node: <Panel key={`${f.id}${i}`} pn={pn} baseZ={0} low={0.25} high={0.75} p={p} glintX={glintX} />,
      });
    });
  });

  // Trees
  trees.forEach(([x, y, s], i) => {
    const t = spring({ frame: frame - (60 + i * 4), fps, config: { damping: 12 } });
    if (t <= 0.001) return;
    const [bx, by] = iso([x, y, 0]);
    items.push({
      depth: x + y + 0.5,
      fp: pt(x, y),
      node: (
        <g key={`t${i}`} transform={`translate(${bx},${by}) scale(${s * t})`}>
          <ellipse cx={0} cy={2} rx={16} ry={8} fill="rgba(8,63,34,0.14)" />
          <rect x={-2.5} y={-18} width={5} height={20} fill="#8a6a45" />
          <circle cx={0} cy={-30} r={17} fill={BRAND.green600} />
          <circle cx={-5} cy={-35} r={11} fill={BRAND.green500} />
        </g>
      ),
    });
  });

  // Solar street lights
  lamps.forEach((lp, i) => {
    const t = spring({ frame: frame - (66 + i * 3), fps, config: { damping: 14 } });
    if (t <= 0.001) return;
    const H = 3.2 * t;
    const base = iso([lp.x, lp.y, 0]);
    const top = iso([lp.x, lp.y, H]);
    const headW: P3 = [lp.x + lp.arm[0], lp.y + lp.arm[1], H - 0.1];
    const head = iso(headW);
    const ground = iso([headW[0], headW[1], 0]);
    const on = interpolate(frame, [ENERGY_START + 30 + i * 4, ENERGY_START + 42 + i * 4], [0, 1], {
      extrapolateLeft: "clamp",
      extrapolateRight: "clamp",
    });
    const pz = H + 0.15;
    const panel: P3[] = [
      [lp.x - 0.4, lp.y - 0.3, pz + 0.22],
      [lp.x + 0.4, lp.y - 0.3, pz + 0.22],
      [lp.x + 0.4, lp.y + 0.3, pz],
      [lp.x - 0.4, lp.y + 0.3, pz],
    ];
    const [psx] = iso([lp.x, lp.y, pz]);
    const shine = Math.exp(-(((psx - glintX) / 70) ** 2));
    if (i === config.lampLabel?.index) {
      labels.push({
        id: `lamp${i}`,
        text: config.lampLabel.text,
        anchor: [lp.x, lp.y, 3.6],
        start: ENERGY_START + 34,
      });
    }
    items.push({
      depth: lp.x + lp.y + 0.3,
      fp: pt(lp.x, lp.y),
      node: (
        <g key={`l${i}`}>
          <ellipse
            cx={ground[0]}
            cy={ground[1]}
            rx={34}
            ry={17}
            fill={BRAND.sun400}
            opacity={0.35 * on}
          />
          <line x1={base[0]} y1={base[1]} x2={top[0]} y2={top[1]} stroke="#7b8a96" strokeWidth={4} strokeLinecap="round" />
          <line x1={top[0]} y1={top[1]} x2={head[0]} y2={head[1]} stroke="#7b8a96" strokeWidth={3} strokeLinecap="round" />
          <circle cx={head[0]} cy={head[1] + 3} r={16} fill={BRAND.sun300} opacity={0.6 * on} />
          <ellipse cx={head[0]} cy={head[1] + 2} rx={8} ry={4} fill={interpolateColors(on, [0, 1], ["#c4cdd4", "#fff6c8"])} stroke="#5f6d78" strokeWidth={1.5} />
          {t > 0.95 ? (
            <polygon
              points={pts(panel)}
              fill={interpolateColors(shine, [0, 1], ["#1f3f72", "#8fb8f0"])}
              stroke="#c9d9ee"
              strokeWidth={1}
            />
          ) : null}
        </g>
      ),
    });
  });

  // Flags (red & white)
  flags.forEach((fl, i) => {
    const t = spring({ frame: frame - (80 + i * 4), fps, config: { damping: 14 } });
    if (t <= 0.001) return;
    const H = 4.6 * t;
    const base = iso([fl.x, fl.y, 0]);
    const top = iso([fl.x, fl.y, H]);
    const wave = Math.sin(frame / 6);
    const w = 34;
    const h = 22;
    const pathFor = (y0: number, y1: number) => {
      const c1 = wave * 4;
      return `M ${top[0]},${top[1] + y0} Q ${top[0] + w / 2},${top[1] + y0 + c1} ${top[0] + w},${top[1] + y0 + 6} L ${top[0] + w},${top[1] + y1 + 6} Q ${top[0] + w / 2},${top[1] + y1 + c1} ${top[0]},${top[1] + y1} Z`;
    };
    items.push({
      depth: fl.x + fl.y + 0.2,
      fp: pt(fl.x, fl.y),
      node: (
        <g key={`f${i}`}>
          <line x1={base[0]} y1={base[1]} x2={top[0]} y2={top[1]} stroke="#8b98a3" strokeWidth={3} strokeLinecap="round" />
          {t > 0.9 ? (
            <g opacity={interpolate(t, [0.9, 1], [0, 1], { extrapolateRight: "clamp" })}>
              <path d={pathFor(2, 2 + h / 2)} fill="#d62828" />
              <path d={pathFor(2 + h / 2, 2 + h)} fill="#ffffff" stroke="rgba(0,0,0,0.08)" strokeWidth={0.5} />
            </g>
          ) : null}
        </g>
      ),
    });
  });

  // Custom extras
  (config.extras ?? []).forEach((ex) => {
    const t = spring({ frame: frame - ex.delay, fps, config: { damping: 13, mass: 0.8 } });
    if (ex.label) {
      labels.push({
        id: ex.id,
        text: ex.label.text,
        anchor: ex.label.anchor,
        start: ex.label.start ?? ex.delay + 90,
      });
    }
    if (t <= 0.001) return;
    const a = arriveAt(ex.id);
    const on = interpolate(frame, [a, a + 18], [0, 1], { extrapolateLeft: "clamp", extrapolateRight: "clamp" });
    const fp = typeof ex.fp === "function" ? ex.fp(frame) : ex.fp;
    items.push({
      depth: fp.x + fp.w + fp.y + fp.d,
      fp,
      node: <g key={`x${ex.id}`}>{ex.render({ t, frame, on, glintX })}</g>,
    });
  });

  // Hub (inverter / energy centre)
  items.push({
    depth: HUB.x + HUB.w + HUB.y + HUB.d,
    fp: { x: HUB.x, y: HUB.y, w: HUB.w, d: HUB.d },
    node:
      hubIn > 0.001 ? (
        <g key="hub">
          <BoxShape b={hubBox} p={{ top: BRAND.orange400, right: "#ffffff", left: "#dfe6ec" }} />
          {(() => {
            const [cx, cy] = iso([HUB.x + HUB.w / 2, HUB.y + HUB.d, hubBox.h / 2]);
            return (
              <g transform={`translate(${cx},${cy}) skewY(26.5) scale(${hubIn})`}>
                <polygon points="2,-12 -7,2 -1,2 -3,12 7,-3 1,-3" fill={BRAND.orange500} />
              </g>
            );
          })()}
        </g>
      ) : null,
  });

  // Topological painter's order: an item whose footprint lies fully behind
  // another (on x or y) is drawn first; ties fall back to depth.
  const behind = (a: Rect, b: Rect) => a.x + a.w <= b.x || a.y + a.d <= b.y;
  const n = items.length;
  const indeg = new Array(n).fill(0);
  const edges: number[][] = items.map(() => []);
  for (let i = 0; i < n; i++) {
    for (let j = 0; j < n; j++) {
      if (i === j) continue;
      const ij = behind(items[i].fp, items[j].fp);
      const ji = behind(items[j].fp, items[i].fp);
      if (ij && !ji) {
        edges[i].push(j);
        indeg[j]++;
      }
    }
  }
  const sorted: Item[] = [];
  const done = new Array(n).fill(false);
  for (let k = 0; k < n; k++) {
    let pick = -1;
    for (let i = 0; i < n; i++) {
      if (done[i] || indeg[i] > 0) continue;
      if (pick < 0 || items[i].depth < items[pick].depth) pick = i;
    }
    if (pick < 0) {
      // cycle: fall back to smallest depth
      for (let i = 0; i < n; i++) {
        if (!done[i] && (pick < 0 || items[i].depth < items[pick].depth)) pick = i;
      }
    }
    done[pick] = true;
    sorted.push(items[pick]);
    edges[pick].forEach((j) => indeg[j]--);
  }
  items.splice(0, n, ...sorted);

  const hubTop = iso([HUB.x + HUB.w / 2, HUB.y + HUB.d / 2, HUB.h]);
  const pulses =
    frame >= ENERGY_START - 6
      ? [0, 1, 2].map((k) => {
          const local = (frame - (ENERGY_START - 6) + k * 20) % 60;
          const r = interpolate(local, [0, 60], [10, 90]);
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
              opacity={interpolate(local, [0, 60], [0.6, 0])}
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
          <defs>
            <radialGradient id="sunG" cx="35%" cy="35%" r="70%">
              <stop offset="0%" stopColor="#fff6c8" />
              <stop offset="100%" stopColor={BRAND.orange500} stopOpacity={0.6} />
            </radialGradient>
          </defs>
          <g transform={`rotate(${frame * 0.6})`}>
            {Array.from({ length: 12 }).map((_, i) => (
              <rect key={i} x={-3} y={-68} width={6} height={16} rx={3} fill={BRAND.sun400} transform={`rotate(${i * 30})`} />
            ))}
          </g>
          <circle r={70 + Math.sin(frame / 10) * 4} fill={BRAND.sun300} opacity={0.35} />
          <circle r={42} fill={BRAND.sun400} />
          <circle r={42} fill="url(#sunG)" />
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
            <BoxShape b={ground} p={{ top: "#d8efe1", right: "#a9d4ba", left: "#8fc2a4" }} stroke="rgba(8,63,34,0.12)" />
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
            {(config.roads ?? [
              { x: 0, y: 7.3, w: 18, d: 0.7 },
              { x: 8.9, y: 0, w: 0.7, d: 18 },
            ]).map((r, i) => (
              <polygon
                key={`road${i}`}
                points={pts([[r.x, r.y, 0], [r.x + r.w, r.y, 0], [r.x + r.w, r.y + r.d, 0], [r.x, r.y + r.d, 0]])}
                fill="rgba(4,36,20,0.07)"
              />
            ))}
            {config.water
              ? (() => {
                  const w = config.water;
                  return (
                    <g>
                      <polygon
                        points={pts([[w.x, w.y + w.d, -0.8], [w.x + w.w, w.y + w.d, -0.8], [w.x + w.w, w.y + w.d, -0.1], [w.x, w.y + w.d, -0.1]])}
                        fill="#5fa8cf"
                      />
                      <polygon
                        points={pts([[w.x, w.y, -0.1], [w.x + w.w, w.y, -0.1], [w.x + w.w, w.y + w.d, -0.1], [w.x, w.y + w.d, -0.1]])}
                        fill="#8cc9e6"
                        stroke="#6fb3d6"
                        strokeWidth={1}
                      />
                      {Array.from({ length: 9 }).map((_, i) => {
                        const k = (i * 0.37) % 1;
                        const yy = w.y + 0.4 + ((i * 1.3) % (w.d - 0.8));
                        const xx = w.x + 1 + k * (w.w - 3) + Math.sin(frame / 25 + i) * 0.4;
                        const [x1, y1] = iso([xx, yy, -0.1]);
                        const [x2, y2] = iso([xx + 1.1, yy, -0.1]);
                        return (
                          <line key={i} x1={x1} y1={y1} x2={x2} y2={y2} stroke="#ffffff" strokeOpacity={0.6 + 0.3 * Math.sin(frame / 12 + i)} strokeWidth={2} strokeLinecap="round" />
                        );
                      })}
                    </g>
                  );
                })()
              : null}
            {(config.groundDecor ?? []).map((g, i) => (
              <polygon key={`gd${i}`} points={pts(g.poly)} fill={g.fill} stroke="rgba(4,36,20,0.15)" strokeWidth={1} />
            ))}
            {/* energy routes */}
            {routeIds.map((id, i) => {
              const sp = routes[id].map(([x, y]) => iso([x, y, 0.02]));
              const len = polyLength(sp);
              const start = ENERGY_START + i * 10;
              const draw = interpolate(frame, [start, start + 28], [0, 1], {
                extrapolateLeft: "clamp",
                extrapolateRight: "clamp",
                easing: Easing.out(Easing.cubic),
              });
              const d = `M ${sp.map(([x, y]) => `${x},${y}`).join(" L ")}`;
              return (
                <g key={`r${id}`}>
                  <path d={d} fill="none" stroke="rgba(8,63,34,0.12)" strokeWidth={7} strokeLinecap="round" strokeLinejoin="round" />
                  {draw > 0 ? (
                    <>
                      <path d={d} fill="none" stroke={BRAND.orange500} strokeOpacity={0.35} strokeWidth={10} strokeLinecap="round" strokeLinejoin="round" strokeDasharray={len} strokeDashoffset={len * (1 - draw)} />
                      <path d={d} fill="none" stroke={BRAND.sun400} strokeWidth={3.5} strokeLinecap="round" strokeLinejoin="round" strokeDasharray={len} strokeDashoffset={len * (1 - draw)} />
                    </>
                  ) : null}
                  {draw >= 1
                    ? [0, 1, 2].map((k) => {
                        const [x, y] = pointAt(sp, ((frame - start) / 45 + k / 3) % 1);
                        return <circle key={k} cx={x} cy={y} r={5} fill="#fffbe6" stroke={BRAND.orange500} strokeWidth={2} />;
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

        {labels.map((l) => {
          const s = spring({ frame: frame - l.start, fps, config: { damping: 12 } });
          const [lx, ly] = iso(l.anchor);
          return (
            <div
              key={l.id}
              style={{
                position: "absolute",
                left: OX + lx,
                top: OY + ly - 34,
                transform: `translate(-50%, -100%) scale(${s})`,
                transformOrigin: "50% 100%",
                opacity: Math.min(1, s * 1.4),
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
              <span style={{ width: 12, height: 12, borderRadius: 999, background: BRAND.green500 }} />
              {l.text}
            </div>
          );
        })}
      </AbsoluteFill>

      {/* Logo + title */}
      <div style={{ position: "absolute", left: 72, top: 52 }}>
        <Img
          src={staticFile("logo.png")}
          style={{
            width: 230,
            display: "block",
            opacity: logoIn,
            transform: `translateY(${(1 - logoIn) * 20}px)`,
          }}
        />
        <div
          style={{
            marginTop: 14,
            fontSize: 62,
            lineHeight: 1.05,
            fontWeight: 700,
            color: BRAND.green950,
            opacity: titleIn,
            transform: `translateY(${(1 - titleIn) * 30}px)`,
          }}
        >
          {config.title[0]}
          <br />
          <span style={{ color: BRAND.green500 }}>{config.title[1]}</span>
        </div>
      </div>

      {config.overlay?.({ frame, fps, fontFamily })}

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
        <div style={{ position: "absolute", inset: 0, opacity: sub1, transform: `translateY(${(1 - sub1) * 16}px)` }}>
          {config.captions[0]}
        </div>
        <div style={{ position: "absolute", inset: 0, opacity: sub2, transform: `translateY(${(1 - sub2) * 16}px)` }}>
          {config.captions[1]}
        </div>
      </div>
    </AbsoluteFill>
  );
};

export const Dot: React.FC = () => <span style={{ color: BRAND.orange500 }}> · </span>;
