import React from "react";
import { interpolate } from "remotion";
import { Box, iso, onLeft, onRight, pts } from "./iso";
import {
  BRAND,
  BoxShape,
  Dot,
  Rect,
  SegmentConfig,
  SegmentScene,
  grid,
} from "./scene/SegmentScene";

const NAVY = { top: "#2a4f86", right: "#1f3f72", left: "#183361" };

// ---- Stack of panel pallets outside the warehouse ----
const PALLETS: Rect = { x: 7.5, y: 1.6, w: 1.2, d: 1.8 };

const PalletStack = ({ t }: { t: number }) => {
  const stacks = [
    { x: PALLETS.x, y: PALLETS.y, n: 6 },
    { x: PALLETS.x, y: PALLETS.y + 1.0, n: 4 },
  ];
  return (
    <g>
      {stacks.map((st, i) => {
        const pallet: Box = { x: st.x, y: st.y, z: 0, w: 1.2, d: 0.8, h: 0.15 };
        return (
          <g key={i}>
            <BoxShape b={pallet} p={{ top: "#c79a64", right: "#b08454", left: "#946b3f" }} />
            {Array.from({ length: Math.round(st.n * t) }).map((_, k) => (
              <BoxShape
                key={k}
                b={{ x: st.x + 0.05, y: st.y + 0.05, z: 0.15 + k * 0.1, w: 1.1, d: 0.7, h: 0.08 }}
                p={NAVY}
                stroke="rgba(201,217,238,0.6)"
              />
            ))}
          </g>
        );
      })}
    </g>
  );
};

// ---- Mounting-structure rack outside the workshop ----
const RACK: Rect = { x: 16.4, y: 1.4, w: 1.2, d: 3.4 };

const SteelRack = ({ t, frame, on }: { t: number; frame: number; on: number }) => {
  const bars = Array.from({ length: 5 });
  // sparks at the workshop door
  const door = iso([10.5 + 5.5 * 0.73, 5.05, 0.7]);
  return (
    <g>
      {[0, 1].map((k) => (
        <BoxShape
          key={`s${k}`}
          b={{ x: RACK.x + 0.1 + k * 0.8, y: RACK.y, z: 0, w: 0.15, d: RACK.d, h: 0.35 }}
          p={{ top: "#9aa6b0", right: "#8a96a0", left: "#76838e" }}
        />
      ))}
      {bars.map((_, k) =>
        k < Math.round(bars.length * t) ? (
          <BoxShape
            key={k}
            b={{ x: RACK.x, y: RACK.y + 0.1, z: 0.35 + k * 0.14, w: 1.2, d: RACK.d - 0.2, h: 0.1 }}
            p={{ top: "#dfe5ea", right: "#c3ccd4", left: "#aab5bf" }}
          />
        ) : null,
      )}
      {on > 0
        ? Array.from({ length: 8 }).map((_, k) => {
            const life = ((frame * 1.3 + k * 9) % 24) / 24;
            const ang = (k * 47) % 140;
            const r = life * 26;
            const x = door[0] + Math.cos(((ang + 200) * Math.PI) / 180) * r;
            const y = door[1] + Math.sin(((ang + 200) * Math.PI) / 180) * r + life * life * 20;
            return <circle key={`sp${k}`} cx={x} cy={y} r={2.2} fill={k % 2 ? BRAND.orange500 : BRAND.sun400} opacity={on * (1 - life)} />;
          })
        : null}
    </g>
  );
};

// ---- Delivery truck along the roads ----
const TRUCK_PATH: [number, number][] = [
  [0.4, 7.65],
  [9.25, 7.65],
  [9.25, 12.5],
];
const PERIOD = 130;
const TRUCK_START = 96;

const truckState = (frame: number) => {
  const local = Math.max(0, frame - TRUCK_START) % PERIOD;
  const k = interpolate(local, [0, 105], [0, 1], { extrapolateRight: "clamp" });
  const eased = k < 0.5 ? 2 * k * k : 1 - (-2 * k + 2) ** 2 / 2;
  const L1 = TRUCK_PATH[1][0] - TRUCK_PATH[0][0];
  const L2 = TRUCK_PATH[2][1] - TRUCK_PATH[1][1];
  const dist = eased * (L1 + L2);
  const alpha =
    frame < TRUCK_START
      ? 0
      : interpolate(local, [0, 8, 112, 125], [0, 1, 1, 0], { extrapolateRight: "clamp" });
  if (dist <= L1) {
    return { x: TRUCK_PATH[0][0] + dist, y: TRUCK_PATH[0][1], dir: "x" as const, alpha };
  }
  return { x: TRUCK_PATH[1][0], y: TRUCK_PATH[1][1] + (dist - L1), dir: "y" as const, alpha };
};

const truckFp = (frame: number): Rect => {
  const s = truckState(frame);
  return s.dir === "x"
    ? { x: s.x - 2.2, y: s.y - 0.35, w: 2.2, d: 0.7 }
    : { x: s.x - 0.35, y: s.y - 2.2, w: 0.7, d: 2.2 };
};

const Truck = ({ frame }: { frame: number }) => {
  const s = truckState(frame);
  if (s.alpha <= 0) return null;
  const W = 0.7;
  const mk = (a: number, b: number, z: number, h: number): Box =>
    s.dir === "x"
      ? { x: s.x - b, y: s.y - W / 2, z, w: b - a, d: W, h }
      : { x: s.x - W / 2, y: s.y - b, z, w: W, d: b - a, h };
  const chassis = mk(0, 2.2, 0.08, 0.2);
  const cargo = mk(0.65, 2.2, 0.28, 0.95);
  const cab = mk(0, 0.6, 0.28, 0.7);
  return (
    <g opacity={s.alpha}>
      <BoxShape b={chassis} p={{ top: "#3d4852", right: "#2f3942", left: "#252e36" }} />
      <BoxShape b={cargo} p={{ top: "#f6f8f7", right: "#ffffff", left: "#dfe6e2" }} />
      <polygon
        points={pts(s.dir === "x" ? onLeft(cargo, 0, 1, 0.2, 0.42) : onRight(cargo, 0, 1, 0.2, 0.42))}
        fill={BRAND.green500}
      />
      <BoxShape b={cab} p={{ top: BRAND.orange400, right: BRAND.orange500, left: "#d97d12" }} />
      <polygon
        points={pts(s.dir === "x" ? onRight(cab, 0.15, 0.85, 0.45, 0.85) : onLeft(cab, 0.15, 0.85, 0.45, 0.85))}
        fill="#bfe0f2"
      />
    </g>
  );
};

// ---- Workers ----
const WORKERS: [number, number][] = [
  [10.05, 9.15],
  [13.4, 9.15],
  [9.95, 12.2],
  [9.95, 16],
];

const Worker = ({ x, y, frame, t, i }: { x: number; y: number; frame: number; t: number; i: number }) => {
  const [sx, sy] = iso([x, y, 0]);
  const swing = Math.sin(frame / 5 + i * 1.3) * 25;
  const bob = Math.abs(Math.sin(frame / 10 + i)) * 1.5;
  return (
    <g transform={`translate(${sx},${sy - bob}) scale(${t})`}>
      <ellipse cx={0} cy={1} rx={9} ry={4} fill="rgba(8,63,34,0.18)" />
      <rect x={-4.5} y={-13} width={3.5} height={13} rx={1.5} fill="#2f4858" />
      <rect x={1} y={-13} width={3.5} height={13} rx={1.5} fill="#2f4858" />
      <rect x={-6} y={-28} width={12} height={16} rx={3} fill={BRAND.orange500} />
      <rect x={-6} y={-22} width={12} height={2.5} fill="#fff6c8" />
      <g transform={`rotate(${swing}, 5, -25)`}>
        <rect x={4} y={-26} width={3} height={12} rx={1.5} fill="#e0a77a" />
        <rect x={2} y={-16} width={9} height={3} rx={1} fill="#6b7680" />
      </g>
      <circle cx={0} cy={-33} r={5.5} fill="#e0a77a" />
      <path d="M -7,-34 A 7 6.5 0 0 1 7,-34 Z" fill={BRAND.sun400} stroke="#d1a91f" strokeWidth={1} />
    </g>
  );
};

const config: SegmentConfig = {
  title: ["EPC", "Partnership"],
  captions: [
    "Supply material, fabrikasi, manpower & engineering",
    <>
      Material Tier-1
      <Dot />
      Manpower bersertifikat
    </>,
  ],
  buildings: [
    {
      id: "warehouse",
      label: "Supply Material",
      box: { x: 1, y: 1, z: 0, w: 6, d: 4.6, h: 2.6 },
      palette: { top: "#eee7dc", right: "#fbf7f0", left: "#d6ccbd" },
      delay: 20,
      windows: (b) => [
        ...grid(onRight, b, 4, 1, [0.08, 0.92], [0.62, 0.84], 0.35, 0),
        ...grid(onLeft, b, 3, 1, [0.55, 0.95], [0.62, 0.84], 0.35, 0),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0.06, 0.46, 0, 0.6), fill: "#a69a87" },
        { poly: onLeft(b, 0, 1, 0.9, 1), fill: BRAND.orange500 },
        { poly: onRight(b, 0, 1, 0.9, 1), fill: BRAND.orange400 },
      ],
    },
    {
      id: "workshop",
      label: "Fabrikasi",
      box: { x: 10.5, y: 1, z: 0, w: 5.5, d: 4, h: 2.6 },
      palette: { top: "#e4ebe7", right: "#f3f6f4", left: "#c6d2cc" },
      delay: 30,
      windows: (b) => [
        ...grid(onRight, b, 3, 1, [0.08, 0.92], [0.58, 0.82], 0.3, 0),
        ...grid(onLeft, b, 3, 1, [0.04, 0.55], [0.6, 0.82], 0.3, 0),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0.64, 0.84, 0, 0.55), fill: "#3d4852" },
        { poly: onLeft(b, 0, 1, 0.92, 1), fill: BRAND.green600 },
        { poly: onRight(b, 0, 1, 0.92, 1), fill: BRAND.green500 },
      ],
    },
    {
      id: "engineering",
      label: "Engineering",
      box: { x: 1, y: 9.5, z: 0, w: 3.5, d: 3.5, h: 4 },
      palette: { top: "#e8eef5", right: "#f6f9fc", left: "#cbd6e2" },
      delay: 40,
      windows: (b) => [
        ...grid(onLeft, b, 3, 4, [0.08, 0.92], [0.08, 0.9], 0.3, 0.38),
        ...grid(onRight, b, 3, 4, [0.08, 0.92], [0.08, 0.9], 0.3, 0.38),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0, 1, 0.95, 1), fill: BRAND.orange500 },
        { poly: onRight(b, 0, 1, 0.95, 1), fill: BRAND.orange400 },
      ],
    },
    {
      id: "container",
      box: { x: 1.2, y: 14.6, z: 0, w: 3.2, d: 1.2, h: 1.2 },
      palette: { top: "#f2a54a", right: BRAND.orange500, left: "#d97d12" },
      delay: 52,
      panels: false,
      accents: (b) =>
        Array.from({ length: 9 }).map((_, i) => ({
          poly: onLeft(b, 0.05 + i * 0.1, 0.08 + i * 0.1, 0.06, 0.94),
          fill: "rgba(120,60,0,0.25)",
        })),
    },
  ],
  fields: [{ id: "field", label: "Proyek PLTS", area: { x: 10.2, y: 9.6, w: 7.4, d: 7.8 } }],
  extras: [
    {
      id: "pallets",
      fp: PALLETS,
      delay: 60,
      render: ({ t }) => <PalletStack t={t} />,
    },
    {
      id: "workshop-rack",
      fp: RACK,
      delay: 66,
      render: ({ t, frame }) => (
        <SteelRack
          t={t}
          frame={frame}
          on={interpolate(frame, [228, 240], [0, 1], { extrapolateLeft: "clamp", extrapolateRight: "clamp" })}
        />
      ),
    },
    {
      id: "truck",
      fp: truckFp,
      delay: 0,
      render: ({ frame }) => <Truck frame={frame} />,
    },
    ...WORKERS.map(([x, y], i) => ({
      id: `worker${i}`,
      fp: { x, y, w: 0.01, d: 0.01 },
      delay: 100 + i * 6,
      label:
        i === 3
          ? { text: "Manpower Bersertifikat", anchor: [x, y, 1.6] as [number, number, number], start: 205 }
          : undefined,
      render: ({ t, frame }: { t: number; frame: number }) => <Worker x={x} y={y} frame={frame} t={t} i={i} />,
    })),
  ],
  hub: { x: 6, y: 10, z: 0, w: 1.6, d: 1.6, h: 1.3 },
  routes: {
    field: [
      [7.6, 11],
      [10.2, 11],
    ],
    warehouse: [
      [6.8, 10],
      [6.8, 5.6],
    ],
    workshop: [
      [7.6, 10.4],
      [8.5, 10.4],
      [8.5, 6.6],
      [13, 6.6],
      [13, 5],
    ],
    engineering: [
      [6, 10.8],
      [4.5, 10.8],
    ],
  },
  trees: [
    [8.3, 4.7, 0.85],
    [0.6, 7.6, 0.8],
    [3.2, 17, 0.9],
    [6, 15.8, 1],
    [7.8, 17.2, 0.85],
    [17.4, 7, 0.8],
    [5.6, 13.6, 0.8],
  ],
};

export const EpcPartnership: React.FC = () => <SegmentScene config={config} />;
