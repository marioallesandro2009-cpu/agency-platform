import{p as G}from"./scrollRevealCore-Der74UyE.js";import{_ as L}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{R as k,T as A,P as q,M as E,V as R}from"./Triangle-BVHsv2qP.js";import{q as W,s as P,k as U,a as V,d as O,i as M,o as z}from"./runtime-core.esm-bundler-Dmkt5go8.js";const N={black:"#000000",white:"#ffffff",red:"#ff0000",green:"#00ff00",blue:"#0000ff",fuchsia:"#ff00ff",cyan:"#00ffff",yellow:"#ffff00",orange:"#ff8000"};function B(t){t.length===4&&(t=t[0]+t[1]+t[1]+t[2]+t[2]+t[3]+t[3]);const e=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(t);return e||console.warn(`Unable to convert hex string ${t} to rgb values`),[parseInt(e[1],16)/255,parseInt(e[2],16)/255,parseInt(e[3],16)/255]}function $(t){return t=parseInt(t),[(t>>16&255)/255,(t>>8&255)/255,(t&255)/255]}function I(t){return t===void 0?[0,0,0]:arguments.length===3?arguments:isNaN(t)?t[0]==="#"?B(t):N[t.toLowerCase()]?B(N[t.toLowerCase()]):(console.warn("Color format not recognised"),[0,0,0]):$(t)}class l extends Array{constructor(e){return Array.isArray(e)?super(...e):super(...I(...arguments))}get r(){return this[0]}get g(){return this[1]}get b(){return this[2]}set r(e){this[0]=e}set g(e){this[1]=e}set b(e){this[2]=e}set(e){return Array.isArray(e)?this.copy(e):this.copy(I(...arguments))}copy(e){return this[0]=e[0],this[1]=e[1],this[2]=e[2],this}}const H=`
    attribute vec2 uv;
    attribute vec2 position;
    varying vec2 vUv;
    void main() {
        vUv = uv;
        gl_Position = vec4(position, 0.0, 1.0);
    }
`,X=`
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
`,Y={__name:"ScannerBackground",props:{color1:{type:String,default:"#4d7c0f"},color2:{type:String,default:"#a3e635"},color3:{type:String,default:"#1a1a1a"},speed:{type:Number,default:.5},sweepSpeed:{type:Number,default:.25},sweepWidth:{type:Number,default:1.6},sweepFalloff:{type:Number,default:6},scale:{type:Number,default:1.5},frequency:{type:Number,default:2},ripple:{type:Number,default:.22},bandDensity:{type:Number,default:11},lineSharpness:{type:Number,default:5.5},glow:{type:Number,default:.22},scanDirection:{type:String,default:"vertical"},colorSpread:{type:Number,default:.7},brightness:{type:Number,default:1},contrast:{type:Number,default:1.15},softness:{type:Number,default:1.4},vignette:{type:Number,default:.45},scanline:{type:Boolean,default:!0},grain:{type:Boolean,default:!0},grainIntensity:{type:Number,default:.05},opacity:{type:Number,default:1},mouseInteraction:{type:Boolean,default:!0},mouseRadius:{type:Number,default:.5},mouseStrength:{type:Number,default:.5}},setup(t){const e=t,a=M(null),p=M(null);let u=null,r=null,m=null,i=null,s=null,f=null,d=0,v=!1,c=!1;const g=G();function _(){return{uTime:{value:0},uResolution:{value:new R(1,1)},uColor1:{value:new l(e.color1)},uColor2:{value:new l(e.color2)},uColor3:{value:new l(e.color3)},uSpeed:{value:e.speed},uSweepSpeed:{value:e.sweepSpeed},uSweepWidth:{value:e.sweepWidth},uSweepFalloff:{value:e.sweepFalloff},uScale:{value:e.scale},uFrequency:{value:e.frequency},uRipple:{value:e.ripple},uBandDensity:{value:e.bandDensity},uLineSharpness:{value:e.lineSharpness},uGlow:{value:e.glow},uScanDirection:{value:e.scanDirection==="vertical"?1:0},uColorSpread:{value:e.colorSpread},uBrightness:{value:e.brightness},uContrast:{value:e.contrast},uSoftness:{value:e.softness},uVignette:{value:e.vignette},uScanline:{value:e.scanline?1:0},uGrain:{value:e.grain?1:0},uGrainIntensity:{value:e.grainIntensity},uOpacity:{value:e.opacity},uMouse:{value:new R(.5,.5)},uMouseInteraction:{value:e.mouseInteraction?1:0},uMouseRadius:{value:e.mouseRadius},uMouseStrength:{value:e.mouseStrength}}}function w(){if(!u||!a.value)return;const{clientWidth:n,clientHeight:o}=a.value;!n||!o||(u.setSize(n,o),r.uniforms.uResolution.value.set(n,o))}function h(n){if(!r||!a.value)return;const o=a.value.getBoundingClientRect(),y=(n.clientX-o.left)/o.width,T=1-(n.clientY-o.top)/o.height;r.uniforms.uMouse.value.set(y,T)}function b(n){d||(d=n),r.uniforms.uTime.value=(n-d)*.001,u.render({scene:m})}function S(n){b(n),v&&!g?i=requestAnimationFrame(S):i=null}function C(){i||(i=requestAnimationFrame(S))}function x(){i&&(cancelAnimationFrame(i),i=null)}function D(){if(c||!p.value||!a.value)return;c=!0;try{u=new k({canvas:p.value,alpha:!0,antialias:!0,dpr:Math.min(window.devicePixelRatio||1,2)})}catch(y){console.warn("ScannerBackground: WebGL unavailable, skipping effect.",y),u=null;return}const n=u.gl;n.clearColor(0,0,0,0);const o=new A(n);r=new q(n,{vertex:H,fragment:X,transparent:!0,uniforms:_()}),m=new E(n,{geometry:o,program:r}),w(),f=new ResizeObserver(w),f.observe(a.value),e.mouseInteraction&&window.addEventListener("pointermove",h,{passive:!0}),g?requestAnimationFrame(b):C()}function F(){if(x(),f&&(f.disconnect(),f=null),window.removeEventListener("pointermove",h),u){const n=u.gl,o=n.getExtension("WEBGL_lose_context");o&&o.loseContext(),n.canvas.width=1,n.canvas.height=1}u=null,r=null,m=null,c=!1}return W(()=>{s=new IntersectionObserver(n=>{v=n[0].isIntersecting,v?c?C():D():x()},{root:null,rootMargin:"200px 0px",threshold:.01}),s.observe(a.value)}),P(()=>{s&&(s.disconnect(),s=null),F()}),U(()=>[e.color1,e.color2,e.color3],()=>{r&&(r.uniforms.uColor1.value=new l(e.color1),r.uniforms.uColor2.value=new l(e.color2),r.uniforms.uColor3.value=new l(e.color3))}),(n,o)=>(z(),V("div",{ref_key:"container",ref:a,class:"scanner-bg","aria-hidden":"true"},[O("canvas",{ref_key:"canvasEl",ref:p,class:"scanner-bg__canvas"},null,512)],512))}},Z=L(Y,[["__scopeId","data-v-b0aaef3f"]]);export{Z as default};
