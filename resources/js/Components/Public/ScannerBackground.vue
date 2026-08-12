<script setup>
/**
 * ScannerBackground — Vue/OGL port of a WebGL "scanner" shader effect:
 * moving color bands + a sweeping beam, with noise ripple, vignette,
 * scanline and grain post-processing. Ported from a React+OGL reference
 * implementation (see props below, same names/semantics as the original).
 *
 * Usage: mount this as an absolutely-positioned background layer inside a
 * `position: relative` section, with your text content in a sibling layer
 * on top (see PageHeaderScanner.vue). Do NOT wrap the canvas and the text
 * in a shared element that has a `filter`/`backdrop-filter` applied — that
 * previously caused a dithering/banding artifact on the homepage hero.
 */
import { ref, onMounted, onBeforeUnmount, watch } from "vue";
import { Renderer, Program, Mesh, Triangle, Color, Vec2 } from "ogl";
import { prefersReducedMotion } from "@/composables/scrollRevealCore";

const props = defineProps({
    color1: { type: String, default: "#4d7c0f" }, // brand-600
    color2: { type: String, default: "#a3e635" }, // brand-400
    color3: { type: String, default: "#1a1a1a" }, // charcoal
    speed: { type: Number, default: 0.5 },
    sweepSpeed: { type: Number, default: 0.25 },
    sweepWidth: { type: Number, default: 1.6 },
    sweepFalloff: { type: Number, default: 6 },
    scale: { type: Number, default: 1.5 },
    frequency: { type: Number, default: 2 },
    ripple: { type: Number, default: 0.22 },
    bandDensity: { type: Number, default: 11 },
    lineSharpness: { type: Number, default: 5.5 },
    glow: { type: Number, default: 0.22 },
    scanDirection: { type: String, default: "vertical" }, // 'vertical' | 'horizontal'
    colorSpread: { type: Number, default: 0.7 },
    brightness: { type: Number, default: 1 },
    contrast: { type: Number, default: 1.15 },
    softness: { type: Number, default: 1.4 },
    vignette: { type: Number, default: 0.45 },
    scanline: { type: Boolean, default: true },
    grain: { type: Boolean, default: true },
    grainIntensity: { type: Number, default: 0.05 },
    opacity: { type: Number, default: 1 },
    mouseInteraction: { type: Boolean, default: true },
    mouseRadius: { type: Number, default: 0.5 },
    mouseStrength: { type: Number, default: 0.5 },
});

const container = ref(null);
const canvasEl = ref(null);

let renderer = null;
let program = null;
let mesh = null;
let rafId = null;
let io = null;
let ro = null;
let startTime = 0;
let isVisible = false;
let initialized = false;
const reducedMotion = prefersReducedMotion();

const vertex = `
    attribute vec2 uv;
    attribute vec2 position;
    varying vec2 vUv;
    void main() {
        vUv = uv;
        gl_Position = vec4(position, 0.0, 1.0);
    }
`;

const fragment = `
    precision highp float;

    uniform float uTime;
    uniform vec2 uResolution;
    uniform vec3 uColor1;
    uniform vec3 uColor2;
    uniform vec3 uColor3;
    uniform float uSpeed;
    uniform float uSweepSpeed;
    uniform float uSweepWidth;
    uniform float uSweepFalloff;
    uniform float uScale;
    uniform float uFrequency;
    uniform float uRipple;
    uniform float uBandDensity;
    uniform float uLineSharpness;
    uniform float uGlow;
    uniform float uScanDirection;
    uniform float uColorSpread;
    uniform float uBrightness;
    uniform float uContrast;
    uniform float uSoftness;
    uniform float uVignette;
    uniform float uScanline;
    uniform float uGrain;
    uniform float uGrainIntensity;
    uniform float uOpacity;
    uniform vec2 uMouse;
    uniform float uMouseInteraction;
    uniform float uMouseRadius;
    uniform float uMouseStrength;

    varying vec2 vUv;

    float hash(vec2 p) {
        p = fract(p * vec2(123.34, 456.21));
        p += dot(p, p + 45.32);
        return fract(p.x * p.y);
    }

    float noise(vec2 p) {
        vec2 i = floor(p);
        vec2 f = fract(p);
        float a = hash(i);
        float b = hash(i + vec2(1.0, 0.0));
        float c = hash(i + vec2(0.0, 1.0));
        float d = hash(i + vec2(1.0, 1.0));
        vec2 u = f * f * (3.0 - 2.0 * f);
        return mix(a, b, u.x) + (c - a) * u.y * (1.0 - u.x) + (d - b) * u.x * u.y;
    }

    void main() {
        vec2 uv = vUv;
        vec2 aspect = vec2(uResolution.x / max(uResolution.y, 1.0), 1.0);
        vec2 p = (uv - 0.5) * aspect * uScale;

        float t = uTime * uSpeed;

        float n = noise(p * uFrequency + t * 0.5);
        p += (n - 0.5) * uRipple;

        float axis = mix(p.x, p.y, uScanDirection);

        float bandCoord = axis * uBandDensity + t * 4.0;
        float band = abs(fract(bandCoord) - 0.5) * 2.0;
        band = pow(clamp(1.0 - band, 0.0, 1.0), uLineSharpness);

        float sweepPos = fract(t * uSweepSpeed);
        float sweepCoord = fract(axis * 0.5 + 0.5);
        float sweepDist = abs(sweepCoord - sweepPos);
        sweepDist = min(sweepDist, 1.0 - sweepDist);
        float sweep = exp(-sweepDist * sweepDist * uSweepFalloff * 10.0) * uSweepWidth;

        float intensity = clamp(band * uGlow + sweep, 0.0, 2.0);

        float mixT = fract(axis * uColorSpread * 0.5 + t * 0.15 + 0.5);
        vec3 col;
        if (mixT < 0.5) {
            col = mix(uColor1, uColor2, mixT * 2.0);
        } else {
            col = mix(uColor2, uColor3, (mixT - 0.5) * 2.0);
        }

        if (uMouseInteraction > 0.5 && uMouseRadius > 0.0001) {
            float mdist = distance(uv, uMouse);
            float influence = (1.0 - smoothstep(0.0, uMouseRadius, mdist)) * uMouseStrength;
            intensity += influence;
        }

        vec3 color = col * intensity;

        color *= uBrightness;
        color = (color - 0.5) * uContrast + 0.5;
        color = mix(color, smoothstep(0.0, 1.0, color), clamp(uSoftness - 1.0, 0.0, 1.0));

        float vig = 1.0 - uVignette * dot(uv - 0.5, uv - 0.5) * 2.5;
        color *= clamp(vig, 0.0, 1.0);
        intensity *= clamp(vig, 0.0, 1.0);

        if (uScanline > 0.5) {
            float sl = sin(uv.y * uResolution.y * 1.3) * 0.5 + 0.5;
            color *= mix(1.0, 0.85 + 0.15 * sl, 0.5);
        }

        if (uGrain > 0.5) {
            float g = hash(uv * uResolution.xy + uTime * 60.0) - 0.5;
            color += g * uGrainIntensity;
        }

        color = max(color, vec3(0.0));

        gl_FragColor = vec4(color, clamp(intensity, 0.0, 1.0) * uOpacity);
    }
`;

function buildUniforms() {
    return {
        uTime: { value: 0 },
        uResolution: { value: new Vec2(1, 1) },
        uColor1: { value: new Color(props.color1) },
        uColor2: { value: new Color(props.color2) },
        uColor3: { value: new Color(props.color3) },
        uSpeed: { value: props.speed },
        uSweepSpeed: { value: props.sweepSpeed },
        uSweepWidth: { value: props.sweepWidth },
        uSweepFalloff: { value: props.sweepFalloff },
        uScale: { value: props.scale },
        uFrequency: { value: props.frequency },
        uRipple: { value: props.ripple },
        uBandDensity: { value: props.bandDensity },
        uLineSharpness: { value: props.lineSharpness },
        uGlow: { value: props.glow },
        uScanDirection: { value: props.scanDirection === "vertical" ? 1 : 0 },
        uColorSpread: { value: props.colorSpread },
        uBrightness: { value: props.brightness },
        uContrast: { value: props.contrast },
        uSoftness: { value: props.softness },
        uVignette: { value: props.vignette },
        uScanline: { value: props.scanline ? 1 : 0 },
        uGrain: { value: props.grain ? 1 : 0 },
        uGrainIntensity: { value: props.grainIntensity },
        uOpacity: { value: props.opacity },
        uMouse: { value: new Vec2(0.5, 0.5) },
        uMouseInteraction: { value: props.mouseInteraction ? 1 : 0 },
        uMouseRadius: { value: props.mouseRadius },
        uMouseStrength: { value: props.mouseStrength },
    };
}

function resize() {
    if (!renderer || !container.value) return;
    const { clientWidth, clientHeight } = container.value;
    if (!clientWidth || !clientHeight) return;
    renderer.setSize(clientWidth, clientHeight);
    program.uniforms.uResolution.value.set(clientWidth, clientHeight);
}

function onPointerMove(event) {
    if (!program || !container.value) return;
    const rect = container.value.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = 1 - (event.clientY - rect.top) / rect.height;
    program.uniforms.uMouse.value.set(x, y);
}

function renderFrame(time) {
    if (!startTime) startTime = time;
    program.uniforms.uTime.value = (time - startTime) * 0.001;
    renderer.render({ scene: mesh });
}

function loop(time) {
    renderFrame(time);
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

function initScanner() {
    if (initialized || !canvasEl.value || !container.value) return;
    initialized = true;

    renderer = new Renderer({
        canvas: canvasEl.value,
        alpha: true,
        antialias: true,
        dpr: Math.min(window.devicePixelRatio || 1, 2),
    });
    const gl = renderer.gl;
    gl.clearColor(0, 0, 0, 0);

    const geometry = new Triangle(gl);
    program = new Program(gl, {
        vertex,
        fragment,
        transparent: true,
        uniforms: buildUniforms(),
    });
    mesh = new Mesh(gl, { geometry, program });

    resize();
    ro = new ResizeObserver(resize);
    ro.observe(container.value);

    if (props.mouseInteraction) {
        // The container is pointer-events:none (it's a decorative background
        // layer), so hit-testing never reaches it — listen on window instead
        // and project the pointer position onto the container's own rect.
        window.addEventListener("pointermove", onPointerMove, { passive: true });
    }

    if (reducedMotion) {
        // Draw a single static frame instead of a continuous loop.
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
    window.removeEventListener("pointermove", onPointerMove);
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
                if (!initialized) initScanner();
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

watch(
    () => [props.color1, props.color2, props.color3],
    () => {
        if (!program) return;
        program.uniforms.uColor1.value = new Color(props.color1);
        program.uniforms.uColor2.value = new Color(props.color2);
        program.uniforms.uColor3.value = new Color(props.color3);
    }
);
</script>

<template>
    <div ref="container" class="scanner-bg" aria-hidden="true">
        <canvas ref="canvasEl" class="scanner-bg__canvas"></canvas>
    </div>
</template>

<style scoped>
.scanner-bg {
    position: absolute;
    inset: 0;
    overflow: hidden;
    pointer-events: none;
}
.scanner-bg__canvas {
    display: block;
    width: 100%;
    height: 100%;
}
</style>
