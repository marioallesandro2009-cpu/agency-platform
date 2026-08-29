import{_ as B,D as T,G as F,o as M,b as W,e as E,x as h}from"./app-DCr5P116.js";import{_ as N}from"./_plugin-vue_export-helper-DlAUqK2U.js";import{R as X,T as Y,P as z,M as A,V as D}from"./Triangle-g2KuZf4y.js";const I=`
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
`,P={__name:"WaveBackground",props:{xScale:{type:Number,default:1},yScale:{type:Number,default:.5},distortion:{type:Number,default:.05},speed:{type:Number,default:.01}},setup(y){const u=y,a=h(null),c=h(null);let t=null,l=null,f=null,o=null,i=null,r=null,d=0,m=!1,s=!1;const v=B();function S(){return{uResolution:{value:new D(1,1)},uTime:{value:0},uXScale:{value:u.xScale},uYScale:{value:u.yScale},uDistortion:{value:u.distortion}}}function p(){if(!t||!a.value)return;const{clientWidth:e,clientHeight:n}=a.value;!e||!n||(t.setSize(e,n),l.uniforms.uResolution.value.set(e,n))}function g(){d+=u.speed,l.uniforms.uTime.value=d,t.render({scene:f})}function x(){g(),m&&!v?o=requestAnimationFrame(x):o=null}function b(){o||(o=requestAnimationFrame(x))}function _(){o&&(cancelAnimationFrame(o),o=null)}function w(){if(s||!c.value||!a.value)return;s=!0;try{t=new X({canvas:c.value,alpha:!1,antialias:!0,dpr:Math.min(window.devicePixelRatio||1,2)})}catch(k){console.warn("WaveBackground: WebGL unavailable, skipping effect.",k),t=null;return}const e=t.gl,n=new Y(e);l=new z(e,{vertex:I,fragment:L,uniforms:S()}),f=new A(e,{geometry:n,program:l}),p(),r=new ResizeObserver(p),r.observe(a.value),v?requestAnimationFrame(g):b()}function R(){if(_(),r&&(r.disconnect(),r=null),t){const e=t.gl,n=e.getExtension("WEBGL_lose_context");n&&n.loseContext(),e.canvas.width=1,e.canvas.height=1}t=null,l=null,f=null,s=!1}return T(()=>{i=new IntersectionObserver(e=>{m=e[0].isIntersecting,m?s?b():w():_()},{root:null,rootMargin:"200px 0px",threshold:.01}),i.observe(a.value)}),F(()=>{i&&(i.disconnect(),i=null),R()}),(e,n)=>(M(),W("div",{ref_key:"container",ref:a,class:"wave-bg","aria-hidden":"true"},[E("canvas",{ref_key:"canvasEl",ref:c,class:"wave-bg__canvas"},null,512)],512))}},G=N(P,[["__scopeId","data-v-1470e8fd"]]);export{G as default};
