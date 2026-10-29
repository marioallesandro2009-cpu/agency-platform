/**
 * Starfield backdrop (framework-free): a fixed 2D canvas of points that
 * drift slightly with scroll. Shared by the Blade pages (blade-app.js)
 * and the Inertia StarField.vue wrapper.
 *
 * Performance: the canvas sits under every page, so any redraw makes the
 * browser recomposite the whole screen. It is therefore drawn on demand
 * only — once per frame while scrolling, on resize and on theme change —
 * and stays completely idle otherwise (no ambient animation loop). Star
 * count scales with the viewport area and the backing store is capped at
 * 1.5x DPR (the points are sub-pixel anyway). In the light theme the
 * stars become faint ink "dust" so they stay visible on the paper
 * background.
 */
export function mountStarField(canvas) {
    const ctx = canvas.getContext("2d");
    if (!ctx) return () => {};

    const staticOnly =
        window.matchMedia?.("(prefers-reduced-motion: reduce)").matches || navigator.connection?.saveData === true;

    let stars = [];
    let rafId = null;
    let w = 0;
    let h = 0;

    function seed() {
        const count = Math.min(Math.round((w * h) / 7000), 320);
        stars = Array.from({ length: count }, () => ({
            x: Math.random() * w,
            y: Math.random() * h,
            r: Math.random() * 1.1 + 0.2,
            depth: Math.random() * 0.6 + 0.1,
            a: 0.35 + Math.random() * 0.65,
            warm: Math.random() < 0.18,
        }));
    }

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        seed();
        draw();
    }

    function draw() {
        rafId = null;
        ctx.clearRect(0, 0, w, h);
        const scroll = staticOnly ? 0 : window.scrollY || 0;
        const light = document.documentElement.getAttribute("data-theme") === "light";
        for (const s of stars) {
            const y = (((s.y - scroll * s.depth * 0.15) % h) + h) % h;
            if (light) {
                ctx.fillStyle = s.warm ? `rgba(77, 124, 15, ${s.a * 0.55})` : `rgba(14, 16, 22, ${s.a * 0.35})`;
            } else {
                ctx.fillStyle = s.warm ? `rgba(255, 214, 170, ${s.a})` : `rgba(220, 230, 255, ${s.a})`;
            }
            ctx.beginPath();
            ctx.arc(s.x, y, s.r, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function schedule() {
        if (!rafId) rafId = requestAnimationFrame(draw);
    }

    const themeObserver = new MutationObserver(schedule);
    themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ["data-theme"] });

    resize();
    window.addEventListener("resize", resize);
    if (!staticOnly) window.addEventListener("scroll", schedule, { passive: true });

    return function destroy() {
        cancelAnimationFrame(rafId);
        themeObserver.disconnect();
        window.removeEventListener("resize", resize);
        window.removeEventListener("scroll", schedule);
    };
}
