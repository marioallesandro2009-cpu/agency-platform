<script setup>
/**
 * WaveBackground — ogl port of a classic WebGL "neon wave lines" shader
 * (screen-space sine waves with per-channel chromatic-aberration offset).
 * Ported from a user-supplied Three.js/RawShaderMaterial reference
 * implementation to ogl — already a project dependency, see
 * ScannerBackground.vue — instead of adding three.js as a second WebGL
 * library for one effect.
 *
 * Shares ScannerBackground.vue's exact mount/resize/visibility/
 * reduced-motion lifecycle (pause the render loop off-screen via
 * IntersectionObserver, draw a single static frame instead of looping
 * under prefers-reduced-motion, fail soft — no canvas — if WebGL is
 * unavailable) since that pattern is already proven in this codebase.
 *
 * Renders fully opaque (matches the reference: solid clear color, alpha
 * always 1.0 in the shader) — mount it as the bottom-most absolute layer
 * in a `position: relative` section and put a scrim + your content above
 * it, the same layering PageHeaderScanner.vue uses for ScannerBackground.
 */
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Renderer, Program, Mesh, Triangle, Vec2 } from "ogl";
import { prefersReducedMotion } from "@/composables/scrollRevealCore";

const props = defineProps({
    xScale: { type: Number, default: 1.0 },
    yScale: { type: Number, default: 0.5 },
    distortion: { type: Number, default: 0.05 },
    speed: { type: Number, default: 0.01 }, // per-frame time increment, matches the reference
});

const container = ref(null);
const canvasEl = ref(null);

let renderer = null;
let program = null;
let mesh = null;
let rafId = null;
let io = null;
let ro = null;
let time = 0;
let isVisible = false;
let initialized = false;
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
    uniform float uTime;
    uniform float uXScale;
    uniform float uYScale;
    uniform float uDistortion;

    void main() {
        vec2 p = (gl_FragCoord.xy * 2.0 - uResolution) / min(uResolution.x, uResolution.y);

        float d = length(p) * uDistortion;

        float rx = p.x * (1.0 + d);
        float gx = p.x;
        float bx = p.x * (1.0 - d);

        float r = 0.05 / abs(p.y + sin((rx + uTime) * uXScale) * uYScale);
        float g = 0.05 / abs(p.y + sin((gx + uTime) * uXScale) * uYScale);
        float b = 0.05 / abs(p.y + sin((bx + uTime) * uXScale) * uYScale);

        gl_FragColor = vec4(r, g, b, 1.0);
    }
`;

function buildUniforms() {
    return {
        uResolution: { value: new Vec2(1, 1) },
        uTime: { value: 0 },
        uXScale: { value: props.xScale },
        uYScale: { value: props.yScale },
        uDistortion: { value: props.distortion },
    };
}

function resize() {
    if (!renderer || !container.value) return;
    const { clientWidth, clientHeight } = container.value;
    if (!clientWidth || !clientHeight) return;
    renderer.setSize(clientWidth, clientHeight);
    program.uniforms.uResolution.value.set(clientWidth, clientHeight);
}

function renderFrame() {
    time += props.speed;
    program.uniforms.uTime.value = time;
    renderer.render({ scene: mesh });
}

function loop() {
    renderFrame();
    if (isVisible && !reducedMotion) {
        rafId = requestAnimationFrame(loop);
    } else {
        rafId = null;
    }
}

function startLoop() {
    if (rafId) return;
    rafId = requestAnimationFrame(loop);
}

function stopLoop() {
    if (rafId) {
        cancelAnimationFrame(rafId);
        rafId = null;
    }
}

function initScene() {
    if (initialized || !canvasEl.value || !container.value) return;
    initialized = true;

    try {
        renderer = new Renderer({
            canvas: canvasEl.value,
            alpha: false,
            antialias: true,
            dpr: Math.min(window.devicePixelRatio || 1, 2),
        });
    } catch (err) {
        console.warn("WaveBackground: WebGL unavailable, skipping effect.", err);
        renderer = null;
        return;
    }

    const gl = renderer.gl;
    const geometry = new Triangle(gl);
    program = new Program(gl, {
        vertex,
        fragment,
        uniforms: buildUniforms(),
    });
    mesh = new Mesh(gl, { geometry, program });

    resize();
    ro = new ResizeObserver(resize);
    ro.observe(container.value);

    if (reducedMotion) {
        requestAnimationFrame(renderFrame);
    } else {
        startLoop();
    }
}

function teardown() {
    stopLoop();
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
            const entry = entries[0];
            isVisible = entry.isIntersecting;
            if (isVisible) {
                if (!initialized) initScene();
                else startLoop();
            } else {
                stopLoop();
            }
        },
        { root: null, rootMargin: "200px 0px", threshold: 0.01 }
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
    <div ref="container" class="wave-bg" aria-hidden="true">
        <canvas ref="canvasEl" class="wave-bg__canvas"></canvas>
    </div>
</template>

<style scoped>
.wave-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}
.wave-bg__canvas {
    display: block;
    width: 100%;
    height: 100%;
}
</style>
