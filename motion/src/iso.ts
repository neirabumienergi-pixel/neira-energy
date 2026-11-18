// Isometric projection helpers. World units: x to the right-down, y to the
// left-down, z up. One world unit = U pixels.
export const U = 30;
const C = Math.cos(Math.PI / 6);

export type P3 = [number, number, number];

export const iso = ([x, y, z]: P3): [number, number] => [
  (x - y) * C * U,
  ((x + y) * 0.5 - z) * U,
];

export const pts = (ps: P3[]) =>
  ps
    .map(iso)
    .map(([a, b]) => `${a.toFixed(1)},${b.toFixed(1)}`)
    .join(" ");

export type Box = {
  x: number;
  y: number;
  z: number;
  w: number; // along x
  d: number; // along y
  h: number; // along z
};

export const topFace = (b: Box): P3[] => [
  [b.x, b.y, b.z + b.h],
  [b.x + b.w, b.y, b.z + b.h],
  [b.x + b.w, b.y + b.d, b.z + b.h],
  [b.x, b.y + b.d, b.z + b.h],
];

// Face pointing +x (front-right on screen)
export const rightFace = (b: Box): P3[] => [
  [b.x + b.w, b.y, b.z],
  [b.x + b.w, b.y + b.d, b.z],
  [b.x + b.w, b.y + b.d, b.z + b.h],
  [b.x + b.w, b.y, b.z + b.h],
];

// Face pointing +y (front-left on screen)
export const leftFace = (b: Box): P3[] => [
  [b.x, b.y + b.d, b.z],
  [b.x + b.w, b.y + b.d, b.z],
  [b.x + b.w, b.y + b.d, b.z + b.h],
  [b.x, b.y + b.d, b.z + b.h],
];

// A rectangle on the +x face, u along y (0..1), v along z (0..1)
export const onRight = (b: Box, u0: number, u1: number, v0: number, v1: number): P3[] => [
  [b.x + b.w, b.y + b.d * u0, b.z + b.h * v0],
  [b.x + b.w, b.y + b.d * u1, b.z + b.h * v0],
  [b.x + b.w, b.y + b.d * u1, b.z + b.h * v1],
  [b.x + b.w, b.y + b.d * u0, b.z + b.h * v1],
];

// A rectangle on the +y face, u along x (0..1), v along z (0..1)
export const onLeft = (b: Box, u0: number, u1: number, v0: number, v1: number): P3[] => [
  [b.x + b.w * u0, b.y + b.d, b.z + b.h * v0],
  [b.x + b.w * u1, b.y + b.d, b.z + b.h * v0],
  [b.x + b.w * u1, b.y + b.d, b.z + b.h * v1],
  [b.x + b.w * u0, b.y + b.d, b.z + b.h * v1],
];
