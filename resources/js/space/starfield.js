/**
 * Starfield backdrop (framework-free): a fixed 2D canvas of twinkling points
 * that drift slightly with scroll. Shared by the Blade pages (blade-app.js)
 * and the Inertia StarField.vue wrapper.
 *
 * Performance: capped at ~30fps (it's ambient), star count scales with the
 * viewport area, paused while the tab is hidden, a single static frame
 * under prefers-reduced-motion / Save-Data. In the light theme the stars
 * become faint ink "dust" so they stay visible on the paper background.
 */
const FRAME_MS = 1000 / 30;

export function mountStarField(canvas) {
    const ctx = canvas.getContext("2d");
    if (!ctx) return () => {};

    const staticOnly =
        window.matchMedia?.("(prefers-reduced-motion: reduce)").matches || navigator.connection?.saveData === true;

    let stars = [];
    let rafId = null;
    let lastFrame = 0;
    let w = 0;
    let h = 0;

    function seed() {
        const count = Math.min(Math.round((w * h) / 7000), 320);
        stars = Array.from({ length: count }, () => ({
            x: Math.random() * w,
            y: Math.random() * h,
            r: Math.random() * 1.1 + 0.2,
            depth: Math.random() * 0.6 + 0.1,
            phase: Math.random() * Math.PI * 2,
            speed: Math.random() * 1.5 + 0.5,
            warm: Math.random() < 0.18,
        }));
    }

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 2);
        w = window.innerWidth;
        h = window.innerHeight;
        canvas.width = w * dpr;
        canvas.height = h * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        seed();
        if (staticOnly) draw(0);
    }

    function draw(t) {
        ctx.clearRect(0, 0, w, h);
        const scroll = window.scrollY || 0;
        const light = document.documentElement.getAttribute("data-theme") === "light";
        for (const s of stars) {
            const y = (((s.y - scroll * s.depth * 0.15) % h) + h) % h;
            const a = 0.35 + 0.65 * (0.5 + 0.5 * Math.sin(t * 0.001 * s.speed + s.phase));
            if (light) {
                ctx.fillStyle = s.warm ? `rgba(77, 124, 15, ${a * 0.55})` : `rgba(14, 16, 22, ${a * 0.35})`;
            } else {
                ctx.fillStyle = s.warm ? `rgba(255, 214, 170, ${a})` : `rgba(220, 230, 255, ${a})`;
            }
            ctx.beginPath();
            ctx.arc(s.x, y, s.r, 0, Math.PI * 2);
            ctx.fill();
        }
    }

    function loop(t) {
        if (t - lastFrame >= FRAME_MS) {
            lastFrame = t;
            draw(t);
        }
        rafId = requestAnimationFrame(loop);
    }

    function onVisibility() {
        if (document.hidden) {
            cancelAnimationFrame(rafId);
            rafId = null;
        } else if (!staticOnly && !rafId) {
            rafId = requestAnimationFrame(loop);
        }
    }

    resize();
    window.addEventListener("resize", resize);
    document.addEventListener("visibilitychange", onVisibility);
    if (!staticOnly) rafId = requestAnimationFrame(loop);

    return function destroy() {
        cancelAnimationFrame(rafId);
        window.removeEventListener("resize", resize);
        document.removeEventListener("visibilitychange", onVisibility);
    };
}
