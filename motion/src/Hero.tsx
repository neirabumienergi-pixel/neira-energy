import React from "react";
import { Box, onLeft, onRight } from "./iso";
import { BRAND, Building, SegmentConfig, SegmentScene, grid } from "./scene/SegmentScene";
import { BOAT, Boat } from "./Community";

/**
 * Universal hero loop: one isometric district that touches every segment
 * (residential, C&I, government, community) — no text, seamless 10 s loop.
 */

const house = (
  id: string,
  x: number,
  y: number,
  roof: { front: string; back: string },
  palette: Building["palette"],
  delay: number,
): Building => ({
  id,
  box: { x, y, z: 0, w: 3, d: 2.4, h: 1.6 },
  palette,
  delay,
  gable: { h: 1.1, ...roof },
  windows: (b: Box) => [onRight(b, 0.3, 0.7, 0.35, 0.75), onLeft(b, 0.6, 0.86, 0.4, 0.75)],
  accents: (b: Box) => [{ poly: onLeft(b, 0.16, 0.36, 0, 0.72), fill: "#8a5d38" }],
});

const TERRACOTTA = { front: "#c8643b", back: "#a9522f" };
const SLATE = { front: "#5b6670", back: "#47515a" };
const CREAM = { top: "#f6efe2", right: "#fffaf2", left: "#e3d6c0" };
const MINT = { top: "#eaf3ec", right: "#f8fcf9", left: "#d0e2d6" };

const config: SegmentConfig = {
  title: ["", ""],
  captions: ["", ""],
  loop: true,
  hideText: true,
  originY: 290,
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
      id: "office",
      box: { x: 1, y: 1, z: 0, w: 6.5, d: 4, h: 3 },
      palette: { top: "#f1eee6", right: "#ffffff", left: "#dbe0e6" },
      delay: 0,
      windows: (b) => [
        ...grid(onLeft, b, 3, 2, [0.04, 0.34], [0.12, 0.82], 0.35, 0.4),
        ...grid(onLeft, b, 3, 2, [0.66, 0.96], [0.12, 0.82], 0.35, 0.4),
        ...grid(onRight, b, 3, 2, [0.08, 0.92], [0.12, 0.82], 0.35, 0.4),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0, 1, 0.9, 1), fill: BRAND.green500 },
        { poly: onRight(b, 0, 1, 0.9, 1), fill: BRAND.green600 },
      ],
    },
    {
      id: "portico",
      box: { x: 3.2, y: 5, z: 0, w: 2.1, d: 0.9, h: 2.7 },
      palette: { top: "#e9e4d8", right: "#fbfaf6", left: "#f4f1ea" },
      delay: 0,
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
      id: "factory",
      box: { x: 10.2, y: 1, z: 0, w: 7, d: 5.6, h: 2.6 },
      palette: { top: "#e4ebe7", right: "#f3f6f4", left: "#c6d2cc" },
      delay: 0,
      windows: (b) => [
        ...grid(onRight, b, 5, 1, [0.06, 0.94], [0.58, 0.82], 0.3, 0),
        ...grid(onLeft, b, 6, 1, [0.05, 0.6], [0.6, 0.82], 0.3, 0),
      ],
      accents: (b) => [
        { poly: onLeft(b, 0.66, 0.8, 0, 0.55), fill: "#8fa39a" },
        { poly: onLeft(b, 0.84, 0.97, 0, 0.55), fill: "#8fa39a" },
        { poly: onRight(b, 0, 1, 0.92, 1), fill: BRAND.orange500 },
        { poly: onLeft(b, 0, 1, 0.92, 1), fill: BRAND.orange400 },
      ],
    },
    house("house1", 1, 9, TERRACOTTA, CREAM, 0),
    house("house2", 1, 12.3, SLATE, MINT, 0),
    house("house3", 4.8, 12.4, TERRACOTTA, MINT, 0),
  ],
  fields: [{ id: "field", area: { x: 10.2, y: 9.4, w: 7.4, d: 5.4 } }],
  lamps: [
    { x: 1.8, y: 8.4, arm: [0, -0.6] },
    { x: 6.2, y: 8.4, arm: [0, -0.6] },
    { x: 12.2, y: 8.4, arm: [0, -0.6] },
    { x: 15.8, y: 8.4, arm: [0, -0.6] },
  ],
  flags: [{ x: 2, y: 6.4 }],
  extras: [
    {
      id: "boat",
      fp: { x: BOAT.x, y: BOAT.y, w: BOAT.L, d: BOAT.W },
      delay: 0,
      render: (ctx) => <Boat {...ctx} />,
    },
  ],
  hub: { x: 6, y: 10, z: 0, w: 1.6, d: 1.6, h: 1.3 },
  routes: {
    office: [
      [6.8, 10],
      [6.8, 5],
    ],
    factory: [
      [7.6, 10.4],
      [8.5, 10.4],
      [8.5, 8.6],
      [13, 8.6],
      [13, 6.6],
    ],
    field: [
      [7.6, 11.2],
      [10.2, 11.2],
    ],
    house1: [
      [6, 10.8],
      [4, 10.8],
    ],
    house2: [
      [6, 11.5],
      [4.4, 11.5],
      [4.4, 13.5],
      [4, 13.5],
    ],
    house3: [
      [7.2, 11.6],
      [7.2, 12.4],
    ],
  },
  trees: [
    [8.2, 1.2, 0.9],
    [8.2, 5.6, 0.8],
    [17.6, 7.4, 0.8],
    [0.6, 7.6, 0.8],
    [5.2, 9.2, 0.8],
    [8.4, 13.6, 0.9],
    [4.4, 14.8, 0.8],
  ],
};

export const Hero: React.FC = () => <SegmentScene config={config} />;
