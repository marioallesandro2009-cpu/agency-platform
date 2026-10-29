/**
 * Planet hero (framework-free): ogl full-screen fragment shader of a
 * dot-matrix planet with a particle ring and an orbiting moon. Shared by the
 * Blade homepage (blade-app.js) and the Inertia Planet.vue wrapper, so the
 * Blade pages never have to load Vue just for this.
 *
 * Performance:
 * - render loop paused while off-screen (IntersectionObserver) and while
 *   the tab is hidden;
 * - internal resolution starts low on small screens and adapts: if the
 *   frames are consistently slow (two samples in a row), resolution steps
 *   down, never below 0.5x;
 * - only animates on capable devices (>= 768px wide, more than 4 CPU
 *   cores, no Save-Data / reduced-motion); everywhere else the
 *   pre-rendered poster image (CSS background of .planet-bg) is the hero;
 * - the canvas fades in over that poster once its first frame is drawn;
 * - fail-soft: without WebGL the container's CSS fallback stays visible.
 */
import { Renderer, Program, Mesh, Triangle, Vec2, Vec3 } from "ogl";
import { canRunPlanet } from "./capability";

const vertex = `
    attribute vec2 uv;
    attribute vec2 position;
    void main() {
        gl_Position = vec4(position, 0.0, 1.0);
    }
`;

const fragment = `
    precision highp float;
    uniform vec2 uResolution;
    uniform vec2 uCenter;
    uniform vec2 uMouse;
    uniform vec3 uAccent;
    uniform float uScale;
    uniform float uTime;

    const float PI = 3.14159265;

    float hash(vec2 p) {
        return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453);
    }

    float hash3(vec3 p) {
        return fract(sin(dot(p, vec3(127.1, 311.7, 74.7))) * 43758.5453);
    }

    float noise3(vec3 p) {
        vec3 i = floor(p);
        vec3 f = fract(p);
        f = f * f * (3.0 - 2.0 * f);
        return mix(
            mix(mix(hash3(i), hash3(i + vec3(1.0, 0.0, 0.0)), f.x),
                mix(hash3(i + vec3(0.0, 1.0, 0.0)), hash3(i + vec3(1.0, 1.0, 0.0)), f.x), f.y),
            mix(mix(hash3(i + vec3(0.0, 0.0, 1.0)), hash3(i + vec3(1.0, 0.0, 1.0)), f.x),
                mix(hash3(i + vec3(0.0, 1.0, 1.0)), hash3(i + vec3(1.0, 1.0, 1.0)), f.x), f.y),
            f.z
        );
    }

    float fbm3(vec3 p) {
        float v = 0.0;
        float a = 0.5;
        for (int i = 0; i < 4; i++) {
            v += a * noise3(p);
            p *= 2.05;
            a *= 0.5;
        }
        return v;
    }

    mat3 rotX(float a) {
        float c = cos(a);
        float s = sin(a);
        return mat3(1.0, 0.0, 0.0, 0.0, c, s, 0.0, -s, c);
    }

    mat3 rotY(float a) {
        float c = cos(a);
        float s = sin(a);
        return mat3(c, 0.0, -s, 0.0, 1.0, 0.0, s, 0.0, c);
    }

    float sphereHit(vec3 ro, vec3 rd, vec3 c, float r) {
        vec3 oc = ro - c;
        float b = dot(oc, rd);
        float h = b * b - dot(oc, oc) + r * r;
        if (h < 0.0) return -1.0;
        return -b - sqrt(h);
    }

    vec3 stars(vec2 p) {
        vec3 col = vec3(0.0);
        for (int layer = 0; layer < 2; layer++) {
            float density = layer == 0 ? 22.0 : 48.0;
            vec2 g = p * density;
            vec2 id = floor(g);
            vec2 f = fract(g) - 0.5;
            float h = hash(id + float(layer) * 31.0);
            if (h > 0.965) {
                vec2 off = vec2(hash(id + 1.7), hash(id + 9.3)) - 0.5;
                float d = length(f - off * 0.6);
                float tw = 0.5 + 0.5 * sin(uTime * (0.8 + h * 2.5) + h * 60.0);
                col += mix(vec3(0.7, 0.8, 1.0), vec3(0.85, 1.0, 0.75), hash(id + 3.1)) * smoothstep(0.08, 0.0, d) * tw * (layer == 0 ? 1.0 : 0.55);
            }
        }
        return col;
    }

    void main() {
        float unit = min(uResolution.x, uResolution.y) * uScale;
        vec2 uv = (gl_FragCoord.xy - uCenter * uResolution) / unit;

        vec3 ro = vec3(0.0, 0.0, 4.2);
        vec3 rd = normalize(vec3(uv, -1.9));
        vec3 lightDir = normalize(vec3(-0.65, 0.4, 0.65));

        float spin = uTime * 0.06 + uMouse.x * 0.6;
        float tilt = 0.35 + uMouse.y * 0.2;

        vec3 col = stars(uv * 0.5);

        // atmosphere halo around the limb
        float tc = -dot(ro, rd);
        float dmin = length(ro + rd * tc);
        float halo = exp(-max(dmin - 1.0, 0.0) * 5.5);
        col += uAccent * halo * 0.28 + vec3(0.2, 0.6, 1.0) * halo * 0.06;

        // ring plane (tilted toward the camera) + its basis
        vec3 nr = normalize(vec3(-0.32, 1.0, 0.5));
        vec3 e1 = normalize(cross(nr, vec3(0.0, 0.0, 1.0)));
        vec3 e2 = cross(nr, e1);

        float tS = sphereHit(ro, rd, vec3(0.0), 1.0);

        vec4 ring = vec4(0.0);
        float tR = -dot(ro, nr) / dot(rd, nr);
        if (tR > 0.0) {
            vec3 pr = ro + rd * tR;
            float r = length(pr);
            if (r > 1.45 && r < 2.15) {
                float ang = atan(dot(pr, e2), dot(pr, e1)) + uTime * 0.04;
                float edge = smoothstep(1.45, 1.55, r) * smoothstep(2.15, 2.0, r);
                float gap = smoothstep(0.02, 0.07, abs(r - 1.78));
                float bands = (0.35 + 0.65 * pow(0.5 + 0.5 * sin(r * 42.0), 3.0)) * edge * gap;
                vec2 cell = vec2(ang * 70.0, r * 26.0);
                vec2 id = floor(cell);
                vec2 f = fract(cell) - 0.5;
                float h = hash(id);
                float sparkle = step(0.82, h) * smoothstep(0.32, 0.0, length(f)) * (0.6 + 0.4 * sin(uTime * 2.0 + h * 40.0));
                float a = clamp(bands * 0.16 + sparkle * edge * 0.85, 0.0, 1.0);
                vec3 c = mix(vec3(0.7, 0.78, 0.9), uAccent, 0.55 + 0.45 * sin(ang * 2.0));
                float shadow = sphereHit(pr, lightDir, vec3(0.0), 1.0) > 0.0 ? 0.2 : 1.0;
                ring = vec4(c * a * shadow, a);
            }
        }

        if (tS > 0.0) {
            vec3 p = ro + rd * tS;
            vec3 n = normalize(p);
            vec3 q = rotY(spin) * rotX(tilt) * n;

            float lat = asin(clamp(q.y, -1.0, 1.0));
            float lon = atan(q.z, q.x);
            float rows = 46.0;
            float latGrid = (lat / PI + 0.5) * rows;
            float latId = floor(latGrid);
            float latC = ((latId + 0.5) / rows - 0.5) * PI;
            float cols = max(floor(rows * 2.0 * cos(latC)), 1.0);
            float lonGrid = (lon / (2.0 * PI) + 0.5) * cols;
            float lonId = floor(lonGrid);
            float lonC = ((lonId + 0.5) / cols - 0.5) * 2.0 * PI;
            vec2 f = vec2(fract(lonGrid), fract(latGrid)) - 0.5;

            vec3 qc = vec3(cos(latC) * cos(lonC), sin(latC), cos(latC) * sin(lonC));
            float land = smoothstep(0.5, 0.56, fbm3(qc * 1.9 + 3.0));

            float diff = max(dot(n, lightDir), 0.0);
            float dotR = mix(0.17, 0.33, land);
            float dotMask = smoothstep(dotR, dotR - 0.09, length(f));

            vec3 base = vec3(0.02, 0.025, 0.04) + vec3(0.03, 0.05, 0.08) * diff;
            vec3 dayDots = mix(vec3(0.12, 0.15, 0.22), uAccent * 1.15, land) * (0.2 + 0.95 * diff);
            float night = smoothstep(0.1, -0.3, dot(n, lightDir));
            float city = land * step(0.6, hash(vec2(lonId, latId))) * night;
            vec3 c = base + dotMask * (dayDots + vec3(1.0, 0.72, 0.38) * city * 0.9);

            // scanning band sweeping pole to pole
            float scan = exp(-pow((q.y - sin(uTime * 0.45) * 0.95) * 16.0, 2.0));
            c += uAccent * scan * (0.35 * dotMask + 0.05);

            float fres = pow(1.0 - max(dot(n, -rd), 0.0), 3.0);
            c += uAccent * fres * 0.75 * (0.25 + 0.75 * diff) + vec3(0.3, 0.7, 1.0) * fres * 0.12;

            col = c;
        }

        // ring in front of the planet (or with no planet behind it)
        if (ring.a > 0.0 && (tS < 0.0 || tR < tS)) {
            col = col * (1.0 - ring.a * 0.55) + ring.rgb;
        }

        // small moon on the ring plane
        float ma = uTime * 0.22 + 1.2;
        vec3 mc = (e1 * cos(ma) + e2 * sin(ma)) * 2.8;
        float tM = sphereHit(ro, rd, mc, 0.12);
        if (tM > 0.0 && (tS < 0.0 || tM < tS)) {
            vec3 nm = normalize(ro + rd * tM - mc);
            float dm = max(dot(nm, lightDir), 0.0);
            float fm = pow(1.0 - max(dot(nm, -rd), 0.0), 2.0);
            col = vec3(0.6, 0.63, 0.68) * (0.06 + 0.9 * dm) + uAccent * fm * 0.45;
        }

        vec2 qv = gl_FragCoord.xy / uResolution;
        col *= 0.6 + 0.4 * pow(16.0 * qv.x * qv.y * (1.0 - qv.x) * (1.0 - qv.y), 0.2);
        col = 1.0 - exp(-col * 1.2);
        gl_FragColor = vec4(col, 1.0);
    }
`;

const DEFAULTS = {
    accentColor: [0.64, 0.9, 0.21],
    center: [0.7, 0.5],
    centerMobile: [0.55, 0.76],
    scale: 0.55,
};

export function mountPlanet(container, options = {}) {
    const opts = { ...DEFAULTS, ...options };
    if (!canRunPlanet()) return () => {};
    const staticOnly = false;

    const canvas = document.createElement("canvas");
    canvas.style.cssText = "display:block;width:100%;height:100%;opacity:0;transition:opacity 900ms ease";
    canvas.setAttribute("aria-hidden", "true");
    container.appendChild(canvas);

    const mouse = { x: 0, y: 0, tx: 0, ty: 0 };
    let renderer = null;
    let program = null;
    let mesh = null;
    let rafId = null;
    let visible = false;
    let initialized = false;
    let startTime = 0;
    let dpr = window.innerWidth < 768 ? 0.5 : Math.min(window.devicePixelRatio || 1, 1) * 0.75;
    // adaptive quality sampling
    let sampleFrames = 0;
    let sampleStart = 0;
    let slowSamples = 0;

    const currentCenter = () => (window.innerWidth < 768 ? opts.centerMobile : opts.center);

    function resize() {
        if (!renderer) return;
        const { clientWidth, clientHeight } = container;
        if (!clientWidth || !clientHeight) return;
        renderer.dpr = dpr;
        renderer.setSize(clientWidth, clientHeight);
        program.uniforms.uResolution.value.set(renderer.gl.canvas.width, renderer.gl.canvas.height);
        const [cx, cy] = currentCenter();
        program.uniforms.uCenter.value.set(cx, cy);
        if (staticOnly) renderFrame();
    }

    function renderFrame() {
        if (!renderer) return;
        mouse.x += (mouse.tx - mouse.x) * 0.04;
        mouse.y += (mouse.ty - mouse.y) * 0.04;
        program.uniforms.uMouse.value.set(mouse.x, mouse.y);
        program.uniforms.uTime.value = staticOnly ? 4.0 : (performance.now() - startTime) / 1000;
        renderer.render({ scene: mesh });
        if (canvas.style.opacity === "0") canvas.style.opacity = "1";
    }

    function adaptQuality(now) {
        if (sampleFrames === 0) sampleStart = now;
        sampleFrames++;
        if (sampleFrames < 40) return;
        const avg = (now - sampleStart) / sampleFrames;
        sampleFrames = 0;
        // Consistently slower than ~45fps: step the internal resolution down.
        slowSamples = avg > 22 ? slowSamples + 1 : 0;
        if (slowSamples >= 2 && dpr > 0.5) {
            slowSamples = 0;
            dpr = Math.max(0.5, dpr * 0.8);
            resize();
        }
    }

    function loop(now) {
        renderFrame();
        adaptQuality(now);
        rafId = visible && !document.hidden ? requestAnimationFrame(loop) : null;
    }
    const startLoop = () => {
        if (!rafId && !staticOnly) rafId = requestAnimationFrame(loop);
    };
    const stopLoop = () => {
        if (rafId) cancelAnimationFrame(rafId);
        rafId = null;
    };

    function onPointerMove(e) {
        mouse.tx = (e.clientX / window.innerWidth) * 2 - 1;
        mouse.ty = (e.clientY / window.innerHeight) * 2 - 1;
    }
    function onVisibility() {
        if (document.hidden) stopLoop();
        else if (visible) startLoop();
    }

    function init() {
        if (initialized) return;
        initialized = true;
        try {
            renderer = new Renderer({ canvas, alpha: false, antialias: false, dpr, powerPreference: "low-power" });
        } catch (err) {
            console.warn("Planet: WebGL unavailable, skipping effect.", err);
            renderer = null;
            return;
        }
        const gl = renderer.gl;
        program = new Program(gl, {
            vertex,
            fragment,
            uniforms: {
                uResolution: { value: new Vec2(1, 1) },
                uCenter: { value: new Vec2(0.5, 0.5) },
                uMouse: { value: new Vec2(0, 0) },
                uAccent: { value: new Vec3(...opts.accentColor) },
                uScale: { value: opts.scale },
                uTime: { value: 0 },
            },
        });
        mesh = new Mesh(gl, { geometry: new Triangle(gl), program });
        startTime = performance.now();
        resize();
        ro = new ResizeObserver(resize);
        ro.observe(container);
        if (!staticOnly) {
            window.addEventListener("pointermove", onPointerMove, { passive: true });
            document.addEventListener("visibilitychange", onVisibility);
            startLoop();
        } else {
            requestAnimationFrame(renderFrame);
        }
    }

    let ro = null;
    const io = new IntersectionObserver(
        ([entry]) => {
            visible = entry.isIntersecting;
            if (visible) {
                if (!initialized) init();
                else startLoop();
            } else {
                stopLoop();
            }
        },
        { rootMargin: "100px 0px", threshold: 0.01 }
    );
    io.observe(container);

    return function destroy() {
        io.disconnect();
        stopLoop();
        ro?.disconnect();
        window.removeEventListener("pointermove", onPointerMove);
        document.removeEventListener("visibilitychange", onVisibility);
        if (renderer) {
            renderer.gl.getExtension("WEBGL_lose_context")?.loseContext();
        }
        canvas.remove();
    };
}
