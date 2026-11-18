import React from "react";
import { Easing, interpolate } from "remotion";
import { Box, P3, iso, onLeft, onRight, pts } from "./iso";
import {
  BRAND,
  BoxShape,
  Dot,
  PANEL_START,
  PanelQuad,
  SegmentConfig,
  SegmentScene,
  grid,
} from "./scene/SegmentScene";

const HOUSE = {
  palette: { top: "#f6efe2", right: "#fffaf2", left: "#e3d6c0" },
  gable: { h: 1.1, front: "#c8643b", back: "#a9522f" },
  windows: (b: Box) => [onRight(b, 0.3, 0.7, 0.35, 0.75), onLeft(b, 0.62, 0.85, 0.4, 0.75)],
  accents: (b: Box) => [{ poly: onLeft(b, 0.18, 0.38, 0, 0.72), fill: "#9b6b43" }],
};

// ---- Fishing boat with a solar canopy ----
const BOAT = { x: 10.4, y: 15.9, L: 4, W: 1.4 };

const Boat = ({ t, frame, on, glintX }: { t: number; frame: number; on: number; glintX: number }) => {
  const { x: bx, y: by, L, W } = BOAT;
  const bob = Math.sin(frame / 12) * 0.08;
  const z0 = -0.1 + bob;
  const zr = z0 + 0.55;
  const rim: P3[] = [
    [bx, by, zr],
    [bx + L - 0.9, by, zr],
    [bx + L, by + W / 2, zr + 0.15],
    [bx + L - 0.9, by + W, zr],
    [bx, by + W, zr],
  ];
  const side: P3[] = [
    [bx, by + W, zr],
    [bx + L - 0.9, by + W, zr],
    [bx + L, by + W / 2, zr + 0.15],
    [bx + L - 0.7, by + W / 2, z0],
    [bx + L - 1.1, by + W - 0.25, z0],
    [bx + 0.25, by + W - 0.25, z0],
  ];
  const posts: [number, number][] = [
    [bx + 0.5, by + 0.25],
    [bx + 2.4, by + 0.25],
    [bx + 0.5, by + W - 0.25],
    [bx + 2.4, by + W - 0.25],
  ];
  const ch = 1.1 * t;
  const drop = interpolate(frame, [PANEL_START + 70, PANEL_START + 86], [1, 0], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
    easing: Easing.out(Easing.cubic),
  });
  const lift = drop * 4;
  const panel: P3[] = [
    [bx + 0.3, by + 0.05, zr + ch + 0.2 + lift],
    [bx + 2.6, by + 0.05, zr + ch + 0.2 + lift],
    [bx + 2.6, by + W - 0.05, zr + ch + lift],
    [bx + 0.3, by + W - 0.05, zr + ch + lift],
  ];
  const post = ([px, py]: [number, number], k: number) => {
    const [x1, y1] = iso([px, py, zr]);
    const [x2, y2] = iso([px, py, zr + ch]);
    return <line key={k} x1={x1} y1={y1} x2={x2} y2={y2} stroke="#6b7680" strokeWidth={2.5} strokeLinecap="round" />;
  };
  const bow = iso([bx + L - 0.2, by + W / 2, zr + 0.5]);
  const wake = [0, 1, 2].map((k) => {
    const local = (frame + k * 25) % 75;
    const [cx, cy] = iso([bx - 0.3 - local / 40, by + W / 2, -0.1]);
    return (
      <ellipse key={k} cx={cx} cy={cy} rx={10 + local / 3} ry={5 + local / 6} fill="none" stroke="#ffffff" strokeWidth={1.5} opacity={interpolate(local, [0, 75], [0.7, 0])} />
    );
  });
  return (
    <g opacity={t}>
      {wake}
      {posts.slice(0, 2).map(post)}
      <polygon points={pts(rim)} fill="#c79a64" stroke="#8a6238" strokeWidth={1} strokeLinejoin="round" />
      <polygon points={pts(side)} fill="#ffffff" stroke="rgba(8,63,34,0.25)" strokeWidth={1} strokeLinejoin="round" />
      <polygon
        points={pts([
          [bx, by + W, zr - 0.12],
          [bx + L - 0.9, by + W, zr - 0.12],
          [bx + L - 0.25, by + W / 2 + 0.25, zr - 0.02],
          [bx + L - 0.35, by + W / 2 + 0.3, zr - 0.22],
          [bx + L - 0.95, by + W, zr - 0.25],
          [bx, by + W, zr - 0.25],
        ])}
        fill={BRAND.orange500}
      />
      {posts.slice(2).map((p, k) => post(p, k + 2))}
      {t > 0.95 && drop < 1 ? <PanelQuad poly={panel} glintX={glintX} opacity={1 - drop} /> : null}
      <circle cx={bow[0]} cy={bow[1]} r={12} fill={BRAND.sun300} opacity={0.7 * on} />
      <circle cx={bow[0]} cy={bow[1]} r={4} fill={on > 0.5 ? "#fff6c8" : "#c4cdd4"} stroke="#5f6d78" strokeWidth={1} />
    </g>
  );
};

// ---- Water tower fed by a solar pump ----
const TOWER = { x: 15.1, y: 9.5, w: 1.4, d: 1.4 };

const WaterTower = ({ t, frame, on }: { t: number; frame: number; on: number }) => {
  const legH = 2.4 * t;
  const tank: Box = { x: TOWER.x, y: TOWER.y, z: legH, w: TOWER.w, d: TOWER.d, h: 1.4 * t };
  const corners: [number, number][] = [
    [TOWER.x + 0.15, TOWER.y + 0.15],
    [TOWER.x + TOWER.w - 0.15, TOWER.y + 0.15],
    [TOWER.x + 0.15, TOWER.y + TOWER.d - 0.15],
    [TOWER.x + TOWER.w - 0.15, TOWER.y + TOWER.d - 0.15],
  ];
  const spout: P3 = [TOWER.x + TOWER.w + 0.35, TOWER.y + TOWER.d / 2, legH + 0.2];
  const [sx, sy] = iso(spout);
  const [, gy] = iso([spout[0], spout[1], 0.15]);
  return (
    <g>
      {corners.map(([cx, cy], k) => {
        const [x1, y1] = iso([cx, cy, 0]);
        const [x2, y2] = iso([cx, cy, legH]);
        return <line key={k} x1={x1} y1={y1} x2={x2} y2={y2} stroke="#7b8a96" strokeWidth={3} strokeLinecap="round" />;
      })}
      <line {...(() => {
        const [x1, y1] = iso([corners[0][0], corners[0][1], legH * 0.5]);
        const [x2, y2] = iso([corners[3][0], corners[3][1], legH * 0.5]);
        return { x1, y1, x2, y2 };
      })()} stroke="#9aa6b0" strokeWidth={2} />
      <BoxShape b={tank} p={{ top: "#e3f1f8", right: "#ffffff", left: "#c7dde9" }} />
      <polygon points={pts(onLeft(tank, 0, 1, 0.62, 0.8))} fill={BRAND.green500} />
      <polygon points={pts(onRight(tank, 0, 1, 0.62, 0.8))} fill={BRAND.green600} />
      {t > 0.95 ? (
        <>
          <line
            {...(() => {
              const [x1, y1] = iso([TOWER.x + TOWER.w, TOWER.y + TOWER.d / 2, legH + 0.2]);
              return { x1, y1, x2: sx, y2: sy };
            })()}
            stroke="#7b8a96"
            strokeWidth={4}
            strokeLinecap="round"
          />
          {/* trough */}
          <polygon
            points={pts([
              [spout[0] - 0.4, spout[1] - 0.4, 0.2],
              [spout[0] + 0.4, spout[1] - 0.4, 0.2],
              [spout[0] + 0.4, spout[1] + 0.4, 0.2],
              [spout[0] - 0.4, spout[1] + 0.4, 0.2],
            ])}
            fill="#8cc9e6"
            stroke="#6b7680"
            strokeWidth={1.5}
          />
          {on > 0
            ? [0, 1, 2, 3].map((k) => {
                const f = ((frame / 10 + k / 4) % 1);
                return <circle key={k} cx={sx} cy={sy + (gy - sy) * f} r={3.2} fill="#4ea3d8" opacity={on} />;
              })
            : null}
        </>
      ) : null}
    </g>
  );
};

const config: SegmentConfig = {
  title: ["Community", "Empowerment"],
  captions: [
    "Energi bersih untuk desa, sekolah & nelayan",
    <>
      Off grid &amp; hybrid
      <Dot />
      Pelatihan operator lokal
    </>,
  ],
  water: { x: 0, y: 15.2, w: 18, d: 2.8 },
  roads: [
    { x: 0, y: 7.3, w: 18, d: 0.7 },
    { x: 8.9, y: 0, w: 0.7, d: 15.2 },
  ],
  groundDecor: [
    {
      poly: [
        [8.9, 15.1, 0.04],
        [9.8, 15.1, 0.04],
        [9.8, 17.0, 0.04],
        [8.9, 17.0, 0.04],
      ],
      fill: "#b98d5c",
    },
  ],
  buildings: [
    {
      id: "school",
      label: "Sekolah",
      box: { x: 1, y: 1, z: 0, w: 6, d: 4, h: 2.6 },
      palette: { top: "#f3efe6", right: "#ffffff", left: "#dde2e8" },
      delay: 20,
      windows: (b) => [
        ...grid(onLeft, b, 5, 1, [0.05, 0.95], [0.3, 0.72], 0.35, 0),
        ...grid(onRight, b, 3, 1, [0.08, 0.92], [0.3, 0.72], 0.35, 0),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0, 1, 0.86, 1), fill: BRAND.orange500 },
        { poly: onRight(b, 0, 1, 0.86, 1), fill: BRAND.orange400 },
      ],
    },
    {
      id: "clinic",
      label: "Puskesmas",
      box: { x: 10.5, y: 1, z: 0, w: 5, d: 4, h: 2.4 },
      palette: { top: "#eef3f0", right: "#ffffff", left: "#d9e2dd" },
      delay: 30,
      windows: (b) => [
        ...grid(onLeft, b, 2, 1, [0.04, 0.36], [0.3, 0.72], 0.35, 0),
        ...grid(onLeft, b, 2, 1, [0.64, 0.96], [0.3, 0.72], 0.35, 0),
        ...grid(onRight, b, 3, 1, [0.08, 0.92], [0.3, 0.72], 0.35, 0),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0.41, 0.59, 0.28, 0.82), fill: "#ffffff" },
        { poly: onLeft(b, 0.43, 0.57, 0.48, 0.62), fill: BRAND.green500 },
        { poly: onLeft(b, 0.475, 0.525, 0.34, 0.76), fill: BRAND.green500 },
        { poly: onLeft(b, 0, 1, 0.88, 1), fill: BRAND.green500 },
        { poly: onRight(b, 0, 1, 0.88, 1), fill: BRAND.green600 },
      ],
    },
    { id: "house1", label: "Rumah Warga", box: { x: 1, y: 9, z: 0, w: 3, d: 2.4, h: 1.6 }, delay: 40, ...HOUSE },
    { id: "house2", box: { x: 1, y: 12.2, z: 0, w: 3, d: 2.4, h: 1.6 }, delay: 48, ...HOUSE },
    {
      id: "house3",
      box: { x: 11.2, y: 9.4, z: 0, w: 3, d: 2.4, h: 1.6 },
      delay: 56,
      ...HOUSE,
    },
  ],
  fields: [{ id: "pump", area: { x: 14.4, y: 11.3, w: 3.6, d: 3.4 } }],
  extras: [
    {
      id: "tower",
      fp: TOWER,
      delay: 64,
      label: { text: "Pompa Air Tenaga Surya", anchor: [TOWER.x + 0.7, TOWER.y + 0.7, 4.1], start: 200 },
      render: ({ t, frame, on }) => <WaterTower t={t} frame={frame} on={on} />,
    },
    {
      id: "boat",
      fp: { x: BOAT.x, y: BOAT.y, w: BOAT.L, d: BOAT.W },
      delay: 72,
      label: { text: "Perahu Nelayan Surya", anchor: [BOAT.x + 1.5, BOAT.y + 0.7, 2.2], start: PANEL_START + 92 },
      render: (ctx) => <Boat {...ctx} />,
    },
  ],
  lamps: [
    { x: 5.2, y: 8.4, arm: [0, -0.6] },
    { x: 13, y: 8.4, arm: [0, -0.6] },
  ],
  flags: [{ x: 2, y: 6.4 }],
  hub: { x: 6, y: 10, z: 0, w: 1.6, d: 1.6, h: 1.3 },
  routes: {
    school: [
      [6.8, 10],
      [6.8, 5],
    ],
    clinic: [
      [7.6, 10.4],
      [9.25, 10.4],
      [9.25, 6.6],
      [13, 6.6],
      [13, 5],
    ],
    house1: [
      [6, 10.6],
      [4, 10.6],
    ],
    house2: [
      [6.4, 11.6],
      [6.4, 13.4],
      [4, 13.4],
    ],
    house3: [
      [7.6, 11.2],
      [10.4, 11.2],
      [10.4, 12.4],
      [12.7, 12.4],
      [12.7, 11.8],
    ],
    tower: [
      [7, 11.6],
      [7, 14.9],
      [16.2, 14.9],
      [16.2, 14.7],
    ],
  },
  trees: [
    [8.2, 1.2, 0.9],
    [17.2, 2, 0.9],
    [17.2, 6, 0.8],
    [0.6, 7.6, 0.8],
    [5, 9.2, 0.8],
    [5, 14.2, 0.9],
    [10.5, 14, 0.85],
    [16.8, 8.6, 0.8],
  ],
};

export const Community: React.FC = () => <SegmentScene config={config} />;
