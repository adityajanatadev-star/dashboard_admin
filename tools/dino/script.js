// ── CONFIG ──────────────────────────────────────
const CFG = {
  groundY:      63,       // px from bottom
  dinoLeft:     80,
  dinoW:        44,
  dinoH:        52,
  baseSpeed:    4,        // px per frame
  speedStep:    0.8,      // added every 100pts
  scorePerPass: 10,
  jumpDuration: 550,      // ms
  minObstGap:   900,      // ms min between spawns
  maxObstGap:   1800,
};

// ── STATE ────────────────────────────────────────
let state = {
  running:    false,
  score:      0,
  best:       0,
  speed:      CFG.baseSpeed,
  jumping:    false,
  dinoY:      0,           // 0 = ground, positive = in air (px)
  obstacles:  [],          // { el, x, w, passed }
  raf:        null,
  lastObst:   0,
  nextObstIn: 1200,
  frame:      0,
  legToggle:  false,
};

// ── PIXEL ART HELPERS ────────────────────────────
// Draw dino on a canvas ctx.
// scale: 2 = default game size, 4 = menu size
function drawDino(ctx, scale = 2, legFrame = 0, dead = false) {
  ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);

  const c1 = dead ? '#f66' : '#0ff';   // body
  const c2 = dead ? '#c33' : '#088';   // shadow/detail
  const cE = dead ? '#ff0' : '#fff';   // eye
  const s  = scale;

  // pixel grid (each cell = s×s px)
  // 0 = empty, 1 = body, 2 = dark, 3 = eye
  const body = [
    [0,0,0,0,0,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,1,1,2,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,1,1,3,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0],
    [0,0,0,0,1,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0],
    [0,0,0,0,0,1,1,1,1,1,1,1,1,1,1,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,1,1,1,1,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,1,1,0,0,1,1,0,0,0,0,0,0,0,0,0,0],
    [0,0,0,0,0,0,1,1,0,0,1,1,0,0,0,0,0,0,0,0,0,0],
  ];

  // Leg frames
  const legs = [
    // frame 0: standing / left forward
    [[9,1,1],[9,2,1],[9,3,1],[10,2,1],[10,3,1]],
    // frame 1: right forward
    [[10,1,1],[10,2,1],[10,3,1],[9,2,1],[9,3,1]],
    // dead: both feet flat
    [[9,1,1],[9,2,1],[10,1,1],[10,2,1]],
  ];

  const legIdx = dead ? 2 : legFrame;

  for (let r = 0; r < body.length; r++) {
    for (let col = 0; col < body[r].length; col++) {
      const v = body[r][col];
      if (!v) continue;
      ctx.fillStyle = v === 3 ? cE : v === 2 ? c2 : c1;
      ctx.fillRect(col * s, r * s, s, s);
    }
  }

  legs[legIdx].forEach(([row, col, _]) => {
    const baseRow = 8;
    ctx.fillStyle = c1;
    ctx.fillRect(col * s, (baseRow + row) * s, s, s);
  });
}

// Draw cactus on canvas
function makeCactusEl(variant) {
  const canvas = document.createElement('canvas');
  const configs = [
    { w: 30, h: 52 },
    { w: 44, h: 60 },
    { w: 22, h: 44 },
  ];
  const cfg = configs[variant % 3];
  canvas.width  = cfg.w;
  canvas.height = cfg.h;
  const ctx = canvas.getContext('2d');
  const s = 2;
  const g = '#0f0';
  const d = '#080';

  // Simple pixel cactus
  const cW = Math.floor(cfg.w / s);
  const cH = Math.floor(cfg.h / s);
  const mid = Math.floor(cW / 2);

  ctx.fillStyle = d;
  // trunk
  for (let r = 0; r < cH; r++) {
    ctx.fillStyle = r % 3 === 0 ? d : g;
    ctx.fillRect(mid * s, r * s, s * 2, s);
  }
  // arms
  const armR = Math.floor(cH * .45);
  // left arm
  ctx.fillStyle = g;
  for (let c = mid - 3; c < mid; c++) ctx.fillRect(c * s, armR * s, s, s);
  for (let r = armR - 2; r < armR + 1; r++) ctx.fillRect((mid - 3) * s, r * s, s, s);
  // right arm
  for (let c = mid + 2; c < mid + 5; c++) ctx.fillRect(c * s, (armR + 2) * s, s, s);
  for (let r = armR; r < armR + 3; r++) ctx.fillRect((mid + 4) * s, r * s, s, s);

  canvas.style.position = 'absolute';
  canvas.style.bottom   = CFG.groundY + 'px';
  return canvas;
}

// ── STARS ────────────────────────────────────────
function initStars() {
  const bg = document.getElementById('starsBg');
  bg.innerHTML = '';
  for (let i = 0; i < 80; i++) {
    const s = document.createElement('div');
    const size = Math.random() < .2 ? 2 : 1;
    s.className = 'star';
    s.style.cssText = `
      width:${size}px; height:${size}px;
      left:${Math.random()*100}%;
      top:${Math.random()*65}%;
      animation-delay:${Math.random()*3}s;
      animation-duration:${1.5 + Math.random()*2}s;
    `;
    bg.appendChild(s);
  }
}

// ── MENU DINO ────────────────────────────────────
function initMenuDino() {
  const c = document.getElementById('menuDino');
  if (!c) return;
  const ctx = c.getContext('2d');
  let f = 0;
  setInterval(() => {
    drawDino(ctx, 4, f % 2);
    f++;
  }, 200);
}

// ── SCREENS ──────────────────────────────────────
function showMenu() {
  stopGame();
  document.getElementById('menuScreen').classList.remove('hidden');
  document.getElementById('gameScreen').classList.add('hidden');
  document.getElementById('gameOverScreen').classList.add('hidden');
}

function showGameOver() {
  state.running = false;
  cancelAnimationFrame(state.raf);

  const fc = document.getElementById('finalScore');
  fc.textContent = String(state.score).padStart(5, '0');

  const nb = document.getElementById('newBestLabel');
  if (state.score > state.best) {
    state.best = state.score;
    nb.classList.remove('hidden');
  } else {
    nb.classList.add('hidden');
  }

  document.getElementById('bestDisplay').textContent = String(state.best).padStart(5, '0');
  document.getElementById('gameOverScreen').classList.remove('hidden');
}

// ── START GAME ───────────────────────────────────
function startGame() {
  // reset state
  state.score      = 0;
  state.speed      = CFG.baseSpeed;
  state.jumping    = false;
  state.dinoY      = 0;
  state.obstacles  = [];
  state.lastObst   = 0;
  state.nextObstIn = 1200;
  state.frame      = 0;
  state.legToggle  = false;

  // clear obstacles
  const oc = document.getElementById('obstacleContainer');
  oc.innerHTML = '';

  // screens
  document.getElementById('menuScreen').classList.add('hidden');
  document.getElementById('gameOverScreen').classList.add('hidden');
  document.getElementById('gameScreen').classList.remove('hidden');

  // init dino canvas
  const dc = document.getElementById('dinoCanvas');
  dc.style.bottom = CFG.groundY + 'px';
  const ctx = dc.getContext('2d');
  drawDino(ctx, 2, 0);

  updateScoreDisplay();

  state.running = true;
  state.raf = requestAnimationFrame(gameLoop);
}

function stopGame() {
  state.running = false;
  if (state.raf) cancelAnimationFrame(state.raf);

  // remove all obstacle elements
  const oc = document.getElementById('obstacleContainer');
  if (oc) oc.innerHTML = '';
  state.obstacles = [];
}

// ── JUMP ─────────────────────────────────────────
let jumpStart = 0;

function doJump() {
  if (!state.running || state.jumping) return;
  state.jumping = true;
  jumpStart = performance.now();
}

// ── OBSTACLE SPAWN ───────────────────────────────
let obstVariant = 0;

function spawnObstacle(now) {
  state.lastObst   = now;
  state.nextObstIn = CFG.minObstGap + Math.random() * (CFG.maxObstGap - CFG.minObstGap);

  const el = makeCactusEl(obstVariant++);
  const oc = document.getElementById('obstacleContainer');
  const x  = 820;
  el.style.right = 'auto';
  el.style.left  = x + 'px';
  oc.appendChild(el);

  state.obstacles.push({ el, x, w: el.width, passed: false });
}

// ── SCORE DISPLAY ────────────────────────────────
function updateScoreDisplay() {
  const el = document.getElementById('scoreDisplay');
  el.textContent = String(state.score).padStart(5, '0');
  document.getElementById('bestDisplay').textContent =
    String(Math.max(state.best, state.score)).padStart(5, '0');
}

function flashScore() {
  const el = document.getElementById('scoreDisplay');
  el.classList.remove('score-flash');
  void el.offsetWidth;
  el.classList.add('score-flash');
  setTimeout(() => el.classList.remove('score-flash'), 300);
}

// ── COLLISION ────────────────────────────────────
function checkCollision(ob) {
  // dino hitbox
  const dx = CFG.dinoLeft + 6;
  const dRight = dx + CFG.dinoW - 12;
  const dBottom = CFG.groundY + state.dinoY;
  const dTop = dBottom + CFG.dinoH - 6;

  // obstacle hitbox
  const ox = ob.x + 4;
  const oRight = ob.x + ob.w - 4;
  const oTop = CFG.groundY + ob.el.height - 4;

  return dx < oRight && dRight > ox && dBottom < oTop;
}

// ── MAIN LOOP ────────────────────────────────────
let prevTime = 0;
let legTimer = 0;

function gameLoop(now) {
  if (!state.running) return;

  const dt = Math.min(now - prevTime, 50); // cap at 50ms
  prevTime = now;
  state.frame++;

  // ── update leg animation
  legTimer += dt;
  if (legTimer > 140) {
    state.legToggle = !state.legToggle;
    legTimer = 0;
  }

  // ── jump physics
  if (state.jumping) {
    const elapsed = now - jumpStart;
    const t = elapsed / CFG.jumpDuration;
    if (t >= 1) {
      state.jumping = false;
      state.dinoY   = 0;
    } else {
      // parabolic arc
      state.dinoY = Math.sin(t * Math.PI) * 140;
    }
  }

  // ── update dino canvas position
  const dc = document.getElementById('dinoCanvas');
  dc.style.bottom = (CFG.groundY + state.dinoY) + 'px';

  // ── draw dino
  const ctx = dc.getContext('2d');
  const lf  = state.jumping ? 0 : (state.legToggle ? 0 : 1);
  drawDino(ctx, 2, lf);

  // ── spawn obstacles
  if (!state.lastObst || now - state.lastObst > state.nextObstIn) {
    spawnObstacle(now);
  }

  // ── move obstacles
  const toRemove = [];
  for (const ob of state.obstacles) {
    ob.x -= state.speed;
    ob.el.style.left = ob.x + 'px';

    // passed dino → score
    if (!ob.passed && ob.x + ob.w < CFG.dinoLeft) {
      ob.passed = true;
      state.score += CFG.scorePerPass;
      flashScore();

      // speed up every 100
      if (state.score % 100 === 0) {
        state.speed += CFG.speedStep;
        showSpeedBadge();
      }

      updateScoreDisplay();
    }

    // off screen left → remove
    if (ob.x + ob.w < -20) {
      toRemove.push(ob);
    }

    // collision
    if (!ob.passed && checkCollision(ob)) {
      // draw dead dino
      drawDino(ctx, 2, 0, true);
      setTimeout(showGameOver, 300);
      return;
    }
  }

  // cleanup
  for (const ob of toRemove) {
    ob.el.remove();
    state.obstacles.splice(state.obstacles.indexOf(ob), 1);
  }

  state.raf = requestAnimationFrame(gameLoop);
}

// ── SPEED BADGE ──────────────────────────────────
let badgeTimeout;
function showSpeedBadge() {
  const b = document.getElementById('speedBadge');
  b.classList.remove('hidden');
  b.style.animation = 'none';
  void b.offsetWidth;
  b.style.animation = 'badgeIn 2s ease forwards';
  clearTimeout(badgeTimeout);
  badgeTimeout = setTimeout(() => b.classList.add('hidden'), 2000);
}

// ── INPUT ────────────────────────────────────────
document.addEventListener('keydown', e => {
  if (e.code === 'Space' || e.code === 'ArrowUp') {
    e.preventDefault();
    doJump();
  }
});

// Mobile tap
document.getElementById('gameWrapper').addEventListener('pointerdown', () => {
  doJump();
});

// ── INIT ─────────────────────────────────────────
initStars();
initMenuDino();
