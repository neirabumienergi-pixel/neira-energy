import React from "react";
import { onLeft, onRight } from "./iso";
import { BRAND, Dot, SegmentConfig, SegmentScene, grid } from "./scene/SegmentScene";

const config: SegmentConfig = {
  title: ["Commercial &", "Industrial"],
  captions: [
    "PLTS atap untuk pabrik, gudang & gedung komersial",
    <>
      Hemat biaya listrik
      <Dot />
      Capai target ESG
    </>,
  ],
  buildings: [
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
  ],
  hub: { x: 6.3, y: 12.3, z: 0, w: 1.8, d: 1.8, h: 1.3 },
  routes: {
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
  },
  trees: [
    [8.2, 1.2, 0.9],
    [9, 6.2, 0.8],
    [5.2, 16, 1],
    [7.8, 16.5, 0.85],
    [3, 15, 0.9],
    [17.2, 9.5, 0.8],
    [15, 9.3, 0.9],
    [0.8, 7.6, 0.8],
  ],
};

export const CommercialIndustrial: React.FC = () => <SegmentScene config={config} />;
