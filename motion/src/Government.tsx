import React from "react";
import { onLeft, onRight } from "./iso";
import { BRAND, Dot, SegmentConfig, SegmentScene, grid } from "./scene/SegmentScene";

const config: SegmentConfig = {
  title: ["Government", "Projects"],
  captions: [
    "PLTS & PJU tenaga surya untuk instansi pemerintah",
    <>
      SBU PLTS
      <Dot />
      IUJPTL
      <Dot />
      Member AESI
    </>,
  ],
  buildings: [
    {
      id: "office",
      label: "Kantor Pemerintahan",
      box: { x: 1, y: 1, z: 0, w: 7, d: 4.5, h: 3 },
      palette: { top: "#f1eee6", right: "#ffffff", left: "#dbe0e6" },
      delay: 20,
      windows: (b) => [
        ...grid(onLeft, b, 3, 2, [0.04, 0.34], [0.12, 0.82], 0.35, 0.4),
        ...grid(onLeft, b, 3, 2, [0.66, 0.96], [0.12, 0.82], 0.35, 0.4),
        ...grid(onRight, b, 4, 2, [0.06, 0.94], [0.12, 0.82], 0.35, 0.4),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0, 1, 0.9, 1), fill: BRAND.green500 },
        { poly: onRight(b, 0, 1, 0.9, 1), fill: BRAND.green600 },
      ],
    },
    {
      // Columned entrance (portico) in front of the office
      id: "portico",
      box: { x: 3.4, y: 5.5, z: 0, w: 2.2, d: 0.9, h: 2.7 },
      palette: { top: "#e9e4d8", right: "#fbfaf6", left: "#f4f1ea" },
      delay: 28,
      panels: false,
      accents: (b) => [
        ...[0, 1, 2, 3].map((i) => ({
          poly: onLeft(b, 0.1 + i * 0.22, 0.2 + i * 0.22, 0, 0.8),
          fill: "#c3cad3",
        })),
        { poly: onLeft(b, 0, 1, 0.8, 1), fill: "#e2dccd" },
        { poly: onRight(b, 0, 1, 0, 0.8), fill: "#d6dce3" },
      ],
    },
    {
      id: "agency",
      label: "Gedung Instansi",
      box: { x: 11, y: 1.5, z: 0, w: 3.5, d: 3.5, h: 6 },
      palette: { top: "#e8eef5", right: "#f6f9fc", left: "#cbd6e2" },
      delay: 40,
      windows: (b) => [
        ...grid(onLeft, b, 3, 8, [0.08, 0.92], [0.06, 0.94], 0.3, 0.38),
        ...grid(onRight, b, 3, 8, [0.08, 0.92], [0.06, 0.94], 0.3, 0.38),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0, 1, 0.96, 1), fill: BRAND.orange500 },
        { poly: onRight(b, 0, 1, 0.96, 1), fill: BRAND.orange400 },
      ],
    },
  ],
  fields: [
    {
      id: "field",
      label: "PLTS Ground-Mounted",
      area: { x: 10.2, y: 10, w: 7.4, d: 7.6 },
    },
  ],
  lamps: [
    { x: 1.5, y: 8.4, arm: [0, -0.6] },
    { x: 4.5, y: 8.4, arm: [0, -0.6] },
    { x: 12, y: 8.4, arm: [0, -0.6] },
    { x: 15.5, y: 8.4, arm: [0, -0.6] },
    { x: 8.5, y: 13.5, arm: [0.6, 0] },
    { x: 8.5, y: 16.6, arm: [0.6, 0] },
  ],
  lampLabel: { text: "PJU Tenaga Surya", index: 3 },
  flags: [{ x: 4.5, y: 6.9 }],
  hub: { x: 5.4, y: 11, z: 0, w: 1.8, d: 1.8, h: 1.3 },
  routes: {
    field: [
      [7.2, 12],
      [10.2, 12],
    ],
    office: [
      [6.6, 11],
      [6.6, 5.5],
    ],
    agency: [
      [7.2, 11.4],
      [9.25, 11.4],
      [9.25, 6.6],
      [12.75, 6.6],
      [12.75, 5],
    ],
  },
  trees: [
    [1.5, 10.5, 0.9],
    [3.2, 13, 1],
    [1.5, 15.5, 0.85],
    [4.2, 16.5, 1],
    [6.4, 15.2, 0.85],
    [16.4, 2.6, 0.9],
    [16.4, 5.6, 0.8],
    [9.9, 1.2, 0.85],
  ],
};

export const Government: React.FC = () => <SegmentScene config={config} />;
