<script setup>
/**
 * Planet — ogl full-screen fragment shader for the homepage "space" hero:
 * a slowly spinning dot-matrix planet (continents as bright brand-lime
 * dots from fbm noise, "city lights" on the night side, a scanning band,
 * an atmospheric rim), wrapped in a tilted particle ring with a small
 * orbiting moon. Analytic ray/sphere and ray/plane hits only — no
 * marching, no textures, no extra dependency (ogl, see WaveBackground.vue).
 *
 * Same lifecycle as WaveBackground.vue: render loop paused off-screen via
 * IntersectionObserver, a single static frame under prefers-reduced-motion,
 * and fail-soft (the container's CSS gradient stays visible) when WebGL is
 * unavailable. The pointer gently tilts/spins the planet.
 */
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Renderer, Program, Mesh, Triangle, Vec2, Vec3 } from "ogl";
import { prefersReducedMotion } from "@/composables/scrollRevealCore";

const props = defineProps({
    // Land dots / atmosphere tint (brand lime by default).
    accentColor: { type: Array, default: () => [0.64, 0.9, 0.21] },
    // Where the planet sits in the viewport (0..1, y from the bottom), desktop vs. narrow screens.
    center: { type: Array, default: () => [0.7, 0.5] },
    centerMobile: { type: Array, default: () => [0.55, 0.76] },
    // Apparent size: smaller = smaller planet on screen.
    scale: { type: Number, default: 0.55 },
});

const container = ref(null);
const canvasEl = ref(null);

let renderer = null;
let program = null;
let mesh = null;
let rafId = null;
let io = null;
let ro = null;
let isVisible = false;
let initialized = false;
let startTime = 0;
const mouse = { x: 0, y: 0, tx: 0, ty: 0 };
const reducedMotion = prefersReducedMotion();

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

function currentCenter() {
    return window.innerWidth < 768 ? props.centerMobile : props.center;
}

function resize() {
    if (!renderer || !container.value) return;
    const { clientWidth, clientHeight } = container.value;
    if (!clientWidth || !clientHeight) return;
    renderer.setSize(clientWidth, clientHeight);
    const gl = renderer.gl;
    program.uniforms.uResolution.value.set(gl.canvas.width, gl.canvas.height);
    const [cx, cy] = currentCenter();
    program.uniforms.uCenter.value.set(cx, cy);
    if (reducedMotion) renderFrame();
}

function renderFrame() {
    if (!renderer) return;
    mouse.x += (mouse.tx - mouse.x) * 0.04;
    mouse.y += (mouse.ty - mouse.y) * 0.04;
    program.uniforms.uMouse.value.set(mouse.x, mouse.y);
    program.uniforms.uTime.value = reducedMotion ? 4.0 : (performance.now() - startTime) / 1000;
    renderer.render({ scene: mesh });
}

function loop() {
    renderFrame();
    rafId = isVisible && !reducedMotion ? requestAnimationFrame(loop) : null;
}

function startLoop() {
    if (rafId || reducedMotion) return;
    rafId = requestAnimationFrame(loop);
}

function stopLoop() {
    if (rafId) {
        cancelAnimationFrame(rafId);
        rafId = null;
    }
}

function onPointerMove(e) {
    mouse.tx = (e.clientX / window.innerWidth) * 2 - 1;
    mouse.ty = (e.clientY / window.innerHeight) * 2 - 1;
}

function initScene() {
    if (initialized || !canvasEl.value || !container.value) return;
    initialized = true;

    // Per-pixel ray march: cap the internal resolution, lower on small screens.
    const dpr = window.innerWidth < 768 ? 0.6 : Math.min(window.devicePixelRatio || 1, 1) * 0.8;

    try {
        renderer = new Renderer({ canvas: canvasEl.value, alpha: false, antialias: false, dpr });
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
            uAccent: { value: new Vec3(...props.accentColor) },
            uScale: { value: props.scale },
            uTime: { value: 0 },
        },
    });
    mesh = new Mesh(gl, { geometry: new Triangle(gl), program });

    startTime = performance.now();
    resize();
    ro = new ResizeObserver(resize);
    ro.observe(container.value);
    if (!reducedMotion) window.addEventListener("pointermove", onPointerMove, { passive: true });

    if (reducedMotion) requestAnimationFrame(renderFrame);
    else startLoop();
}

function teardown() {
    stopLoop();
    window.removeEventListener("pointermove", onPointerMove);
    if (ro) {
        ro.disconnect();
        ro = null;
    }
    if (renderer) {
        const gl = renderer.gl;
        const ext = gl.getExtension("WEBGL_lose_context");
        if (ext) ext.loseContext();
        gl.canvas.width = 1;
        gl.canvas.height = 1;
    }
    renderer = null;
    program = null;
    mesh = null;
    initialized = false;
}

onMounted(() => {
    io = new IntersectionObserver(
        (entries) => {
            isVisible = entries[0].isIntersecting;
            if (isVisible) {
                if (!initialized) initScene();
                else startLoop();
            } else {
                stopLoop();
            }
        },
        { root: null, rootMargin: "100px 0px", threshold: 0.01 }
    );
    io.observe(container.value);
});

onBeforeUnmount(() => {
    if (io) {
        io.disconnect();
        io = null;
    }
    teardown();
});
</script>

<template>
    <div ref="container" class="planet-bg" aria-hidden="true">
        <canvas ref="canvasEl" class="planet-bg__canvas"></canvas>
    </div>
</template>

<style scoped>
.planet-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
    /* Fallback (and pre-init) look when WebGL is unavailable. */
    background:
        radial-gradient(circle at 70% 50%, #0b0d14 0 14%, rgba(163, 230, 53, 0.18) 15%, transparent 24%),
        #05060a;
}
.planet-bg__canvas {
    display: block;
    width: 100%;
    height: 100%;
}
</style>
