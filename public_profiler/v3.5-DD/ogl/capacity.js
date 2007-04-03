// ogl/capacity.js

// Capacity (carrying weight) info used by 3EProfiler that was released by
// WotC under the Open Gaming License (OGL). This file however, is not
// released under the OGL (see above).

var sizeMultipliers = {
  c:16,  // colossal
  g:8,   // gargantuan
  h:4,   // huge
  l:2,   // large
  m:1,   // medium
  s:.75, // small
  t:.5,  // tiny
  d:.25, // diminutive
  f:.125 // fine
};

var loadLight = [0, 3, 6, 10, 13, 16, 20, 23, 26, 30, // 0-9
  33, 38, 43, 50, 58, 66, 76, 86, 100, 116, // 10-19
  133, 153, 173, 200, 233, 266, 306, 346, 400, 466]; // 20-29

var loadMedium = [0, 6, 13, 20, 26, 33, 40, 46, 53, 60, // 0-9
  66, 76, 86, 100, 116, 133, 153, 173, 200, 233, // 10-19
  266, 306, 346, 400, 466, 533, 613, 693, 800, 933]; // 20-29

var loadHeavy = [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, // 0-9
  100, 115, 130, 150, 175, 200, 230, 260, 300, 350, // 10-19
  400, 460, 520, 600, 700, 800, 920, 1040, 1200, 1400]; // 20-29
