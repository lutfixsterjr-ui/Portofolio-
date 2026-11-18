(() => {
  const $ = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => [...c.querySelectorAll(s)];
  const M = window.Motion || {};   // motion.dev
  const A = window.anime;          // anime.js
  if (M.animate) document.documentElement.classList.add('mo');

  /* ---------- anime.js: partikel melayang ---------- */
  const box = $('#particles');
  for (let i = 0; i < 30; i++) {
    const p = document.createElement('i');
    p.style.left = Math.random() * 100 + '%';
    p.style.top = Math.random() * 100 + '%';
    if (i % 3 === 0) { p.style.background = 'var(--c3)'; p.style.boxShadow = '0 0 8px var(--c3)'; }
    box.appendChild(p);
  }
  if (A) {
    A({
      targets: '#particles i',
      translateX: () => A.random(-70, 70),
      translateY: () => A.random(-90, 90),
      opacity: [.15, .8],
      duration: () => A.random(2800, 5500),
      direction: 'alternate', loop: true, easing: 'easeInOutSine'
    });

    /* anime.js: judul nama muncul huruf per huruf (3D flip) */
    const h1 = $('#name');
    h1.innerHTML = h1.textContent.split(' ').map(w => `<span class="w">${[...w].map(c => `<span class="l">${c}</span>`).join('')}</span>`).join(' ');
    A({
      targets: '#name .l',
      translateY: [50, 0], rotateX: [-90, 0], opacity: [0, 1],
      delay: A.stagger(45, { start: 300 }), duration: 1100, easing: 'easeOutExpo'
    });
  }

  /* ---------- Motion: reveal saat di-scroll ---------- */
  if (M.animate && M.inView) {
    $$('.rv').forEach(el => {
      const i = [...el.parentNode.children].indexOf(el);
      M.inView(el, () => {
        M.animate(el, { opacity: [0, 1], y: [34, 0] }, { duration: .7, delay: Math.min(i, 5) * .09, ease: 'easeOut' });
      }, { amount: .15 });
    });
  }

  /* ---------- Name tag menggantung (pendulum + drag) ---------- */
  const hang = $('#hang'), wrap = $('#wrap'), badge = $('#badge');
  let ang = 26, vel = 0, drag = false, mx = 0, my = 0;
  const clamp = (v, a, b) => Math.max(a, Math.min(b, v));

  if (M.animate) M.animate(wrap, { y: [-300, 0] }, { type: 'spring', stiffness: 90, damping: 11 });

  const pivot = () => { const r = wrap.getBoundingClientRect(); return { x: r.left + r.width / 2, y: r.top }; };
  hang.addEventListener('pointerdown', e => { drag = true; hang.setPointerCapture(e.pointerId); });
  hang.addEventListener('pointermove', e => {
    if (!drag) return;
    const p = pivot();
    ang = clamp(Math.atan2(p.x - e.clientX, e.clientY - p.y) * 180 / Math.PI, -55, 55);
    vel = 0;
  });
  const release = () => { drag = false; };
  hang.addEventListener('pointerup', release);
  hang.addEventListener('pointercancel', release);

  // hembusan angin kecil + kursor menyenggol name tag
  window.addEventListener('pointermove', e => {
    mx = e.clientX; my = e.clientY;
    if (drag) return;
    const r = badge.getBoundingClientRect();
    if (mx > r.left && mx < r.right && my > r.top && my < r.bottom) vel += (e.movementX || 0) * .012;
  });

  let last = performance.now();
  (function loop(t) {
    const dt = Math.min((t - last) / 16.67, 3); last = t;
    if (!drag) {
      vel += -0.014 * ang * dt;          // gaya pegas gravitasi
      vel *= Math.pow(0.992, dt);        // redaman
      ang += vel * dt;
    }
    hang.style.transform = `rotate(${ang}deg)`;
    badge.style.transform = `rotateY(${clamp(vel * 9, -40, 40) + Math.sin(t / 1800) * 6}deg)`;
    requestAnimationFrame(loop);
  })(last);

  /* ---------- Tilt 3D untuk kartu ---------- */
  $$('.tilt').forEach(el => {
    el.addEventListener('pointermove', e => {
      if (e.pointerType === 'touch') return;
      const r = el.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - .5, y = (e.clientY - r.top) / r.height - .5;
      el.style.transform = `perspective(800px) rotateY(${x * 14}deg) rotateX(${-y * 14}deg) translateZ(8px)`;
    });
    el.addEventListener('pointerleave', () => { el.style.transform = ''; });
  });

  /* ---------- Slider screenshot: geser (swipe / drag mouse) ---------- */
  $$('[data-slider]').forEach(s => {
    const n = s.children.length, dots = s.nextElementSibling;
    dots.innerHTML = '<i></i>'.repeat(n);
    const set = () => {
      const m = s.scrollWidth - s.clientWidth, k = m > 0 ? Math.round(s.scrollLeft / m * (n - 1)) : 0;
      [...dots.children].forEach((d, i) => d.classList.toggle('on', i === k));
    };
    s.addEventListener('scroll', set, { passive: true }); set();
    let down = false, sx = 0, sl = 0;
    s.addEventListener('pointerdown', e => { if (e.pointerType !== 'mouse') return; down = true; sx = e.clientX; sl = s.scrollLeft; s.style.scrollSnapType = 'none'; });
    addEventListener('pointermove', e => { if (down) s.scrollLeft = sl - (e.clientX - sx); });
    addEventListener('pointerup', () => { if (down) { down = false; s.style.scrollSnapType = ''; } });
  });

  /* ---------- Menu mobile ---------- */
  const bg = $('#burger'), nv = $('#nav');
  const toggle = f => { bg.classList.toggle('open', f); nv.classList.toggle('open', f); };
  bg.addEventListener('click', () => toggle(!nv.classList.contains('open')));
  $$('#nav a').forEach(a => a.addEventListener('click', () => toggle(false)));

  /* ---------- Efek kursor (dot + ring + jejak anime.js + ripple klik) ---------- */
  const dot = $('.cur-dot'), ring = $('.cur-ring');
  const fine = matchMedia('(pointer:fine)').matches;
  const spawn = (cls, x, y, color) => {
    const el = document.createElement('i');
    el.className = cls; el.style.left = x + 'px'; el.style.top = y + 'px';
    if (color) { el.style.background = color; el.style.boxShadow = `0 0 10px ${color}`; }
    document.body.appendChild(el); return el;
  };
  if (fine) {
    let cx = innerWidth / 2, cy = innerHeight / 2, rx = cx, ry = cy, lastT = 0;
    addEventListener('pointermove', e => {
      cx = e.clientX; cy = e.clientY;
      dot.style.transform = `translate3d(${cx}px,${cy}px,0)`;
      const grab = e.target.closest('.hang,.slider'), hov = e.target.closest('a,button,.card,.panel,.proj');
      ring.classList.toggle('grab', !!grab); ring.classList.toggle('big', !!hov && !grab);
      const now = performance.now();
      if (A && now - lastT > 45) {
        lastT = now;
        const t = spawn('trail', cx, cy, Math.random() > .7 ? '#ff3df2' : '');
        A({ targets: t, scale: [1, 0], opacity: [.7, 0], translateY: A.random(-14, 14), duration: 650, easing: 'easeOutQuad', complete: () => t.remove() });
      }
    });
    (function follow() { rx += (cx - rx) * .16; ry += (cy - ry) * .16; ring.style.transform = `translate3d(${rx}px,${ry}px,0)`; requestAnimationFrame(follow); })();
  }
  addEventListener('pointerdown', e => {
    if (!A) return;
    const r = spawn('ripple', e.clientX, e.clientY);
    A({ targets: r, scale: [1, 7], opacity: [.8, 0], duration: 650, easing: 'easeOutExpo', complete: () => r.remove() });
  });
})();
