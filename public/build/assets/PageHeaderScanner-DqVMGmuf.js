import{_ as H,D as X,G as J,z as Y,o,b as i,e as d,s as G,h as E,w as N,c as M,M as Z,a as B,j as b,t as m,X as K,n as g,F as A,r as Q,W as ee,d as h,k as te,m as ae,Z as ne,g as x,S as re}from"./app-CFolsjRH.js";import{_ as W}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{R as oe,T as le,P as ue,M as ie,V as P}from"./Triangle-g2KuZf4y.js";const q={black:"#000000",white:"#ffffff",red:"#ff0000",green:"#00ff00",blue:"#0000ff",fuchsia:"#ff00ff",cyan:"#00ffff",yellow:"#ffff00",orange:"#ff8000"};function V(t){t.length===4&&(t=t[0]+t[1]+t[1]+t[2]+t[2]+t[3]+t[3]);const e=/^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(t);return e||console.warn(`Unable to convert hex string ${t} to rgb values`),[parseInt(e[1],16)/255,parseInt(e[2],16)/255,parseInt(e[3],16)/255]}function se(t){return t=parseInt(t),[(t>>16&255)/255,(t>>8&255)/255,(t&255)/255]}function z(t){return t===void 0?[0,0,0]:arguments.length===3?arguments:isNaN(t)?t[0]==="#"?V(t):q[t.toLowerCase()]?V(q[t.toLowerCase()]):(console.warn("Color format not recognised"),[0,0,0]):se(t)}class p extends Array{constructor(e){return Array.isArray(e)?super(...e):super(...z(...arguments))}get r(){return this[0]}get g(){return this[1]}get b(){return this[2]}set r(e){this[0]=e}set g(e){this[1]=e}set b(e){this[2]=e}set(e){return Array.isArray(e)?this.copy(e):this.copy(z(...arguments))}copy(e){return this[0]=e[0],this[1]=e[1],this[2]=e[2],this}}const ce=`
    attribute vec2 uv;
    attribute vec2 position;
    varying vec2 vUv;
    void main() {
        vUv = uv;
        gl_Position = vec4(position, 0.0, 1.0);
    }
`,fe=`
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
`,de={__name:"ScannerBackground",props:{color1:{type:String,default:"#4d7c0f"},color2:{type:String,default:"#a3e635"},color3:{type:String,default:"#1a1a1a"},speed:{type:Number,default:.5},sweepSpeed:{type:Number,default:.25},sweepWidth:{type:Number,default:1.6},sweepFalloff:{type:Number,default:6},scale:{type:Number,default:1.5},frequency:{type:Number,default:2},ripple:{type:Number,default:.22},bandDensity:{type:Number,default:11},lineSharpness:{type:Number,default:5.5},glow:{type:Number,default:.22},scanDirection:{type:String,default:"vertical"},colorSpread:{type:Number,default:.7},brightness:{type:Number,default:1},contrast:{type:Number,default:1.15},softness:{type:Number,default:1.4},vignette:{type:Number,default:.45},scanline:{type:Boolean,default:!0},grain:{type:Boolean,default:!0},grainIntensity:{type:Number,default:.05},opacity:{type:Number,default:1},mouseInteraction:{type:Boolean,default:!0},mouseRadius:{type:Number,default:.5},mouseStrength:{type:Number,default:.5}},setup(t){const e=t,l=G(null),s=G(null);let u=null,n=null,c=null,f=null,v=null,y=null,S=0,C=!1,w=!1;const I=H();function U(){return{uTime:{value:0},uResolution:{value:new P(1,1)},uColor1:{value:new p(e.color1)},uColor2:{value:new p(e.color2)},uColor3:{value:new p(e.color3)},uSpeed:{value:e.speed},uSweepSpeed:{value:e.sweepSpeed},uSweepWidth:{value:e.sweepWidth},uSweepFalloff:{value:e.sweepFalloff},uScale:{value:e.scale},uFrequency:{value:e.frequency},uRipple:{value:e.ripple},uBandDensity:{value:e.bandDensity},uLineSharpness:{value:e.lineSharpness},uGlow:{value:e.glow},uScanDirection:{value:e.scanDirection==="vertical"?1:0},uColorSpread:{value:e.colorSpread},uBrightness:{value:e.brightness},uContrast:{value:e.contrast},uSoftness:{value:e.softness},uVignette:{value:e.vignette},uScanline:{value:e.scanline?1:0},uGrain:{value:e.grain?1:0},uGrainIntensity:{value:e.grainIntensity},uOpacity:{value:e.opacity},uMouse:{value:new P(.5,.5)},uMouseInteraction:{value:e.mouseInteraction?1:0},uMouseRadius:{value:e.mouseRadius},uMouseStrength:{value:e.mouseStrength}}}function R(){if(!u||!l.value)return;const{clientWidth:a,clientHeight:r}=l.value;!a||!r||(u.setSize(a,r),n.uniforms.uResolution.value.set(a,r))}function _(a){if(!n||!l.value)return;const r=l.value.getBoundingClientRect(),k=(a.clientX-r.left)/r.width,j=1-(a.clientY-r.top)/r.height;n.uniforms.uMouse.value.set(k,j)}function D(a){S||(S=a),n.uniforms.uTime.value=(a-S)*.001,u.render({scene:c})}function F(a){D(a),C&&!I?f=requestAnimationFrame(F):f=null}function L(){f||(f=requestAnimationFrame(F))}function T(){f&&(cancelAnimationFrame(f),f=null)}function O(){if(w||!s.value||!l.value)return;w=!0;try{u=new oe({canvas:s.value,alpha:!0,antialias:!0,dpr:Math.min(window.devicePixelRatio||1,2)})}catch(k){console.warn("ScannerBackground: WebGL unavailable, skipping effect.",k),u=null;return}const a=u.gl;a.clearColor(0,0,0,0);const r=new le(a);n=new ue(a,{vertex:ce,fragment:fe,transparent:!0,uniforms:U()}),c=new ie(a,{geometry:r,program:n}),R(),y=new ResizeObserver(R),y.observe(l.value),e.mouseInteraction&&window.addEventListener("pointermove",_,{passive:!0}),I?requestAnimationFrame(D):L()}function $(){if(T(),y&&(y.disconnect(),y=null),window.removeEventListener("pointermove",_),u){const a=u.gl,r=a.getExtension("WEBGL_lose_context");r&&r.loseContext(),a.canvas.width=1,a.canvas.height=1}u=null,n=null,c=null,w=!1}return X(()=>{v=new IntersectionObserver(a=>{C=a[0].isIntersecting,C?w?L():O():T()},{root:null,rootMargin:"200px 0px",threshold:.01}),v.observe(l.value)}),J(()=>{v&&(v.disconnect(),v=null),$()}),Y(()=>[e.color1,e.color2,e.color3],()=>{n&&(n.uniforms.uColor1.value=new p(e.color1),n.uniforms.uColor2.value=new p(e.color2),n.uniforms.uColor3.value=new p(e.color3))}),(a,r)=>(o(),i("div",{ref_key:"container",ref:l,class:"scanner-bg","aria-hidden":"true"},[d("canvas",{ref_key:"canvasEl",ref:s,class:"scanner-bg__canvas"},null,512)],512))}},pe=W(de,[["__scopeId","data-v-b0aaef3f"]]),me={__name:"Breadcrumbs",props:{items:{type:Array,required:!0},variant:{type:String,default:"light"}},setup(t){const e=te(),l=t,s=ae(()=>{const u=typeof window<"u"?window.location.origin:"";return JSON.stringify({"@context":"https://schema.org","@type":"BreadcrumbList",itemListElement:l.items.map((n,c)=>({"@type":"ListItem",position:c+1,name:n.label,item:n.href?u+n.href:void 0}))}).replace(/</g,"\\u003c")});return(u,n)=>(o(),i(A,null,[E(B(K),null,{default:N(()=>[(o(),M(Z("script"),{type:"application/ld+json",nonce:B(e).props.cspNonce},{default:N(()=>[b(m(s.value),1)]),_:1},8,["nonce"]))]),_:1}),d("nav",{"aria-label":"Breadcrumb",class:g(t.variant==="dark"?"mb-6":"max-w-6xl mx-auto px-4 pt-6")},[d("ol",{class:g(["flex flex-wrap items-center gap-1.5 text-sm",t.variant==="dark"?"text-gray-300":"text-gray-500"])},[(o(!0),i(A,null,Q(t.items,(c,f)=>(o(),i("li",{key:f,class:"flex items-center gap-1.5"},[c.href?(o(),M(B(ee),{key:0,href:c.href,class:g(t.variant==="dark"?"hover:text-white":"hover:text-brand-600")},{default:N(()=>[b(m(c.label),1)]),_:2},1032,["href","class"])):(o(),i("span",{key:1,class:g(["font-medium",t.variant==="dark"?"text-brand-400":"text-gray-700"]),"aria-current":"page"},m(c.label),3)),f<t.items.length-1?(o(),i("svg",{key:2,class:g(["w-3.5 h-3.5 flex-shrink-0",t.variant==="dark"?"text-gray-500":"text-gray-300"]),fill:"none",viewBox:"0 0 24 24",stroke:"currentColor","stroke-width":"2","aria-hidden":"true"},[...n[0]||(n[0]=[d("path",{"stroke-linecap":"round","stroke-linejoin":"round",d:"M9 5l7 7-7 7"},null,-1)])],2)):h("",!0)]))),128))],2)],2)],64))}},ve={class:"page-header relative overflow-hidden bg-charcoal text-white py-14 sm:py-20 md:py-28"},ye={class:"page-header__content relative z-20 max-w-7xl mx-auto px-4"},ge={class:"max-w-3xl"},he={key:0,class:"text-brand-400 font-semibold text-xs uppercase tracking-widest mb-4"},be={class:"text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold leading-[1.05] tracking-tight mb-4 sm:mb-6 text-left [text-wrap:balance]"},we={key:1,class:"text-gray-300 text-lg md:text-xl text-left max-w-2xl"},xe={key:2,class:"text-left mt-6"},Se={__name:"PageHeaderScanner",props:{eyebrow:{type:String,default:""},title:{type:String,required:!0},subtitle:{type:String,default:""},color1:{type:String,default:"#4d7c0f"},color2:{type:String,default:"#a3e635"},color3:{type:String,default:"#1a1a1a"},breadcrumbItems:{type:Array,default:null}},setup(t){return(e,l)=>{const s=ne("scroll-reveal");return o(),i("section",ve,[E(pe,{class:"z-0",color1:t.color1,color2:t.color2,color3:t.color3},null,8,["color1","color2","color3"]),l[0]||(l[0]=d("div",{class:"page-header__scrim absolute inset-0 z-10","aria-hidden":"true"},null,-1)),d("div",ye,[t.breadcrumbItems?.length?(o(),M(me,{key:0,variant:"dark",items:t.breadcrumbItems},null,8,["items"])):h("",!0),d("div",ge,[t.eyebrow?x((o(),i("p",he,[b(m(t.eyebrow),1)])),[[s]]):h("",!0),x((o(),i("h1",be,[b(m(t.title),1)])),[[s,{delay:80}]]),t.subtitle?x((o(),i("p",we,[b(m(t.subtitle),1)])),[[s,{delay:160}]]):h("",!0),e.$slots.default?x((o(),i("div",xe,[re(e.$slots,"default",{},void 0,!0)])),[[s,{delay:240}]]):h("",!0)])])])}}},Be=W(Se,[["__scopeId","data-v-1c1542b4"]]);export{Be as P};
