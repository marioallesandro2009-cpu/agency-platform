import{p as B}from"./scrollRevealCore-Der74UyE.js";import{_ as T}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{R as F,T as M,P as W,M as E,V as N}from"./Triangle-BVHsv2qP.js";import{q as X,s as Y,a as q,d as z,i as h,o as A}from"./runtime-core.esm-bundler-BNfIVW-h.js";const I=`
    attribute vec2 uv;
    attribute vec2 position;
    void main() {
        gl_Position = vec4(position, 0.0, 1.0);
    }
`,L=`
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
`,P={__name:"WaveBackground",props:{xScale:{type:Number,default:1},yScale:{type:Number,default:.5},distortion:{type:Number,default:.05},speed:{type:Number,default:.01}},setup(y){const u=y,a=h(null),c=h(null);let t=null,l=null,f=null,o=null,i=null,r=null,d=0,m=!1,s=!1;const p=B();function S(){return{uResolution:{value:new N(1,1)},uTime:{value:0},uXScale:{value:u.xScale},uYScale:{value:u.yScale},uDistortion:{value:u.distortion}}}function v(){if(!t||!a.value)return;const{clientWidth:e,clientHeight:n}=a.value;!e||!n||(t.setSize(e,n),l.uniforms.uResolution.value.set(e,n))}function g(){d+=u.speed,l.uniforms.uTime.value=d,t.render({scene:f})}function x(){g(),m&&!p?o=requestAnimationFrame(x):o=null}function b(){o||(o=requestAnimationFrame(x))}function _(){o&&(cancelAnimationFrame(o),o=null)}function w(){if(s||!c.value||!a.value)return;s=!0;try{t=new F({canvas:c.value,alpha:!1,antialias:!0,dpr:Math.min(window.devicePixelRatio||1,2)})}catch(k){console.warn("WaveBackground: WebGL unavailable, skipping effect.",k),t=null;return}const e=t.gl,n=new M(e);l=new W(e,{vertex:I,fragment:L,uniforms:S()}),f=new E(e,{geometry:n,program:l}),v(),r=new ResizeObserver(v),r.observe(a.value),p?requestAnimationFrame(g):b()}function R(){if(_(),r&&(r.disconnect(),r=null),t){const e=t.gl,n=e.getExtension("WEBGL_lose_context");n&&n.loseContext(),e.canvas.width=1,e.canvas.height=1}t=null,l=null,f=null,s=!1}return X(()=>{i=new IntersectionObserver(e=>{m=e[0].isIntersecting,m?s?b():w():_()},{root:null,rootMargin:"200px 0px",threshold:.01}),i.observe(a.value)}),Y(()=>{i&&(i.disconnect(),i=null),R()}),(e,n)=>(A(),q("div",{ref_key:"container",ref:a,class:"wave-bg","aria-hidden":"true"},[z("canvas",{ref_key:"canvasEl",ref:c,class:"wave-bg__canvas"},null,512)],512))}},O=T(P,[["__scopeId","data-v-1470e8fd"]]);export{O as default};
