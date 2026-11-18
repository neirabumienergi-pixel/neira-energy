import React from "react";
import { interpolate, spring } from "remotion";
import { Box, onLeft, onRight } from "./iso";
import { BRAND, Building, Dot, SegmentConfig, SegmentScene, grid } from "./scene/SegmentScene";

type HouseStyle = {
  palette: Building["palette"];
  gable: NonNullable<Building["gable"]>;
};

const STYLES: HouseStyle[] = [
  {
    palette: { top: "#f6efe2", right: "#fffaf2", left: "#e3d6c0" },
    gable: { h: 1.1, front: "#c8643b", back: "#a9522f" },
  },
  {
    palette: { top: "#e8f0f6", right: "#f7fbfe", left: "#cfdde8" },
    gable: { h: 1.1, front: "#5b6670", back: "#47515a" },
  },
  {
    palette: { top: "#eaf3ec", right: "#f8fcf9", left: "#d0e2d6" },
    gable: { h: 1.1, front: "#9b6b43", back: "#7f5634" },
  },
];

const house = (id: string, x: number, y: number, style: number, delay: number, label?: string): Building => ({
  id,
  label,
  box: { x, y, z: 0, w: 3, d: 2.5, h: 1.7 },
  delay,
  ...STYLES[style],
  windows: (b: Box) => [onRight(b, 0.3, 0.7, 0.35, 0.75), onLeft(b, 0.6, 0.86, 0.4, 0.75)],
  accents: (b: Box) => [{ poly: onLeft(b, 0.16, 0.36, 0, 0.72), fill: "#8a5d38" }],
});

// ---- Monitoring app card (screen overlay) ----
const BARS = [0.12, 0.25, 0.45, 0.66, 0.84, 0.97, 1, 0.9, 0.72, 0.5, 0.28, 0.12];

const MonitorCard = ({ frame, fps }: { frame: number; fps: number }) => {
  const s = spring({ frame: frame - 236, fps, config: { damping: 14 } });
  if (s <= 0.001) return null;
  const kwh = interpolate(frame, [246, 320], [0, 14.2], {
    extrapolateLeft: "clamp",
    extrapolateRight: "clamp",
  });
  return (
    <div
      style={{
        position: "absolute",
        left: 708,
        top: 268,
        width: 300,
        padding: "18px 20px 16px",
        background: "#ffffff",
        borderRadius: 22,
        boxShadow: "0 18px 40px rgba(4,36,20,0.18)",
        opacity: Math.min(1, s * 1.4),
        transform: `translateY(${(1 - s) * 40}px) scale(${0.9 + 0.1 * s})`,
        transformOrigin: "50% 100%",
        color: BRAND.green950,
      }}
    >
      <div style={{ display: "flex", alignItems: "center", justifyContent: "space-between", fontSize: 18, fontWeight: 600 }}>
        <span style={{ display: "flex", alignItems: "center", gap: 8 }}>
          <span style={{ width: 10, height: 10, borderRadius: 99, background: BRAND.green500 }} />
          Monitoring PLTS
        </span>
        <span
          style={{
            fontSize: 14,
            color: BRAND.green600,
            background: "#ecf8f1",
            padding: "3px 10px",
            borderRadius: 99,
          }}
        >
          Live
        </span>
      </div>
      <div style={{ marginTop: 10, fontSize: 44, fontWeight: 700, lineHeight: 1, fontVariantNumeric: "tabular-nums" }}>
        {kwh.toFixed(1).replace(".", ",")}
        <span style={{ fontSize: 22, fontWeight: 600, marginLeft: 6 }}>kWh</span>
      </div>
      <div style={{ fontSize: 16, color: "#4b6357", marginTop: 4 }}>Produksi hari ini</div>
      <div style={{ display: "flex", alignItems: "flex-end", gap: 6, height: 64, marginTop: 12 }}>
        {BARS.map((h, i) => {
          const g = spring({ frame: frame - (250 + i * 3), fps, config: { damping: 16 } });
          return (
            <div
              key={i}
              style={{
                flex: 1,
                height: `${Math.max(4, h * 100 * g)}%`,
                borderRadius: 4,
                background: i === 6 ? BRAND.orange500 : BRAND.green500,
                opacity: 0.35 + 0.65 * h,
              }}
            />
          );
        })}
      </div>
    </div>
  );
};

const config: SegmentConfig = {
  title: ["Residential", "Rooftop Solar"],
  captions: [
    "Tagihan listrik turun, nilai rumah naik",
    <>
      Termasuk NIDI &amp; SLO
      <Dot />
      Garansi hingga 25 tahun
    </>,
  ],
  groundDecor: [
    {
      // pool
      poly: [
        [6.5, 1.4, 0.02],
        [8.4, 1.4, 0.02],
        [8.4, 4.2, 0.02],
        [6.5, 4.2, 0.02],
      ],
      fill: "#8cc9e6",
    },
  ],
  buildings: [
    {
      id: "modern",
      label: "PLTS Atap Rumah",
      box: { x: 1, y: 1, z: 0, w: 5, d: 4, h: 3.4 },
      palette: { top: "#eef0f2", right: "#ffffff", left: "#d9dee4" },
      delay: 20,
      windows: (b) => [
        ...grid(onLeft, b, 2, 2, [0.05, 0.6], [0.08, 0.9], 0.15, 0.25),
        ...grid(onRight, b, 2, 2, [0.1, 0.9], [0.08, 0.9], 0.2, 0.25),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0.66, 0.95, 0, 0.92), fill: "#a87b52" },
        { poly: onLeft(b, 0, 1, 0.94, 1), fill: BRAND.green500 },
        { poly: onRight(b, 0, 1, 0.94, 1), fill: BRAND.green600 },
      ],
    },
    house("h1", 10.5, 1.4, 1, 30),
    house("h2", 14.2, 1.4, 0, 36),
    house("h3", 1, 9.5, 2, 42),
    house("h4", 1, 13.2, 0, 48),
    house("h5", 11, 10, 0, 54),
    house("h6", 14.6, 10, 2, 58, "Perumahan"),
    house("h7", 11, 14, 1, 62),
    house("h8", 14.6, 14, 0, 66),
  ],
  hub: { x: 6, y: 10, z: 0, w: 1.6, d: 1.6, h: 1.3 },
  routes: {
    modern: [
      [6.8, 10],
      [6.8, 5],
    ],
    h3: [
      [6, 10.8],
      [4, 10.8],
    ],
    h5: [
      [7.6, 11.2],
      [11, 11.2],
    ],
    h1: [
      [7.6, 10.4],
      [8.5, 10.4],
      [8.5, 6.6],
      [12, 6.6],
      [12, 3.9],
    ],
  },
  trees: [
    [8.6, 5.6, 0.8],
    [5, 9.2, 0.8],
    [5, 14.6, 0.9],
    [17.6, 7, 0.8],
    [0.6, 7.6, 0.8],
    [7.5, 16, 1],
    [4.6, 17.2, 0.85],
    [10.2, 17.4, 0.8],
  ],
  overlay: ({ frame, fps }) => <MonitorCard frame={frame} fps={fps} />,
};

export const Residential: React.FC = () => <SegmentScene config={config} />;
