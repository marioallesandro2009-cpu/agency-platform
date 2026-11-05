import "../css/public.css";
import { mountStarField } from "./space/starfield";
import { canRunPlanet } from "./space/capability";

// Entry point for the hybrid-Blade public pages (Home, /harga, jasa,
// blog) — everything here is vanilla JS / small standalone Vue mounts,
// deliberately NOT going through createInertiaApp. These pages render
// their full text content server-side in Blade; this file only adds the
// same visual motion (scroll-reveal) and the genuinely interactive bits
// (nav toggle, pricing tabs, maintenance billing toggle, lead form) on
// top of already-complete HTML.

const EASING = "cubic-bezier(0.16, 1, 0.3, 1)";

function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

// ---- v-scroll-reveal / v-image-reveal vanilla port -------------------
// Matches resources/js/directives/scrollReveal.js + imageReveal.js
// exactly (same durations/easing/thresholds) so pages ported to Blade
// keep the identical entrance animation.
const OFFSETS = {
    up: [0, 28],
    down: [0, -28],
    left: [-28, 0],
    right: [28, 0],
    none: [0, 0],
};

function initScrollReveal() {
    // Corporate pages show content straight away: blocks waiting to fade in
    // read as empty space while scrolling.
    if (prefersReducedMotion() || document.body.classList.contains("theme-blue")) return;

    document.querySelectorAll("[data-reveal]").forEach((el) => {
        const direction = el.dataset.reveal || "up";
        const delay = Number(el.dataset.revealDelay || 0);
        const [x, y] = OFFSETS[direction] || OFFSETS.up;

        el.style.opacity = "0";
        el.style.transform = `translate(${x}px, ${y}px)`;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) return;
                el.style.transition = `opacity 600ms ${EASING}, transform 600ms ${EASING}`;
                el.style.transitionDelay = `${delay}ms`;
                el.style.opacity = "1";
                el.style.transform = "translate(0, 0)";
                observer.disconnect();
            },
            { threshold: 0.2, rootMargin: "0px 0px -10% 0px" }
        );
        observer.observe(el);
    });

    document.querySelectorAll("[data-reveal-image]").forEach((el) => {
        const delay = Number(el.dataset.revealDelay || 0);
        el.style.opacity = "0";
        el.style.transform = "scale(1.04)";

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (!entry.isIntersecting) return;
                el.style.transition = `opacity 900ms ${EASING}, transform 900ms ${EASING}`;
                el.style.transitionDelay = `${delay}ms`;
                el.style.opacity = "1";
                el.style.transform = "scale(1)";
                observer.disconnect();
            },
            { threshold: 0.2, rootMargin: "0px 0px -10% 0px" }
        );
        observer.observe(el);
    });
}

// ---- Navbar: mobile menu + services accordion -------------------------
// Desktop dropdown uses pure CSS (:hover/:focus-within, see navbar.blade.php)
// — no JS needed there. Only the mobile hamburger panel and its nested
// services accordion are click-toggled.
function initNavbar() {
    const menuToggle = document.querySelector("[data-nav-toggle]");
    const menuPanel = document.querySelector("[data-nav-panel]");
    if (menuToggle && menuPanel) {
        menuToggle.addEventListener("click", () => {
            const open = menuPanel.classList.toggle("hidden") === false;
            menuToggle.setAttribute("aria-expanded", String(open));
            menuToggle.querySelectorAll("[data-icon-open],[data-icon-close]").forEach((icon) => {
                icon.classList.toggle("hidden");
            });
        });
    }

    const servicesToggle = document.querySelector("[data-mobile-services-toggle]");
    const servicesPanel = document.querySelector("[data-mobile-services-panel]");
    if (servicesToggle && servicesPanel) {
        servicesToggle.addEventListener("click", () => {
            const open = servicesPanel.classList.toggle("hidden") === false;
            servicesToggle.setAttribute("aria-expanded", String(open));
            const caret = servicesToggle.querySelector("[data-caret]");
            if (caret) caret.textContent = open ? "^" : "v";
        });
    }
}

// ---- Pricing category tabs (/harga) -----------------------------------
function initPricingTabs() {
    const triggers = document.querySelectorAll("[data-tab-trigger]");
    if (!triggers.length) return;

    triggers.forEach((trigger) => {
        trigger.addEventListener("click", () => {
            const value = trigger.dataset.tabTrigger;

            triggers.forEach((t) => {
                const active = t.dataset.tabTrigger === value;
                t.setAttribute("aria-selected", String(active));
                // Space-theme tabs style themselves off aria-selected (see .space-tab in app.css).
                if ("ariaStyled" in t.dataset) return;
                t.classList.toggle("bg-brand-600", active);
                t.classList.toggle("text-white", active);
                t.classList.toggle("bg-white", !active);
                t.classList.toggle("text-gray-600", !active);
                t.classList.toggle("border", !active);
                t.classList.toggle("border-gray-200", !active);
            });

            document.querySelectorAll("[data-tab-panel]").forEach((panel) => {
                panel.classList.toggle("hidden", panel.dataset.tabPanel !== value);
            });

            const url = new URL(window.location.href);
            url.searchParams.set("kategori", value);
            window.history.replaceState({}, "", url);
        });
    });
}

// ---- Maintenance plan billing toggle (/harga) --------------------------
function initBillingToggle() {
    document.querySelectorAll("[data-billing-trigger]").forEach((trigger) => {
        trigger.addEventListener("click", () => {
            const [planId, period] = trigger.dataset.billingTrigger.split(":");

            document.querySelectorAll(`[data-billing-trigger^="${planId}:"]`).forEach((btn) => {
                const active = btn.dataset.billingTrigger === `${planId}:${period}`;
                btn.setAttribute("aria-pressed", String(active));
                if ("ariaStyled" in btn.dataset) return;
                btn.classList.toggle("bg-white", active);
                btn.classList.toggle("text-gray-900", active);
                btn.classList.toggle("shadow-sm", active);
                btn.classList.toggle("text-gray-500", !active);
            });

            document.querySelectorAll(`[data-billing-panel^="${planId}:"]`).forEach((panel) => {
                panel.classList.toggle("hidden", panel.dataset.billingPanel !== `${planId}:${period}`);
            });
        });
    });
}

// ---- FAQ accordion -------------------------------------------------------
function initFaqAccordion() {
    document.querySelectorAll("[data-faq-toggle]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const panel = document.getElementById(btn.getAttribute("aria-controls"));
            const open = btn.getAttribute("aria-expanded") === "true";
            btn.setAttribute("aria-expanded", String(!open));
            if (panel) panel.classList.toggle("hidden", open);
            const plus = btn.querySelector("[data-faq-icon]");
            if (plus) plus.classList.toggle("rotate-45", !open);
        });
    });
}

// ---- Software Custom: yearly cost of a manual process -------------------
function initRoiCalculator() {
    document.querySelectorAll("[data-roi-calc]").forEach((root) => {
        const read = (name) => Math.max(0, Number(root.querySelector(`[data-roi-${name}]`).value) || 0);
        const WORK_DAYS_PER_MONTH = 22;
        const update = () => {
            const monthlyHours = read("staff") * read("hours") * WORK_DAYS_PER_MONTH;
            root.querySelector("[data-roi-yearly]").textContent = `Rp${(monthlyHours * read("rate") * 12).toLocaleString("id-ID")}`;
            root.querySelector("[data-roi-monthly-hours]").textContent = monthlyHours.toLocaleString("id-ID");
            root.querySelector("[data-roi-monthly]").textContent = `Rp${(monthlyHours * read("rate")).toLocaleString("id-ID")}`;
        };
        root.addEventListener("input", update);
        update();
    });
}

// ---- Software Custom: self-diagnosis checklist ---------------------------
function initNeedCheck() {
    document.querySelectorAll("[data-need-check]").forEach((root) => {
        const boxes = [...root.querySelectorAll("input[type=checkbox]")];
        root.addEventListener("change", () => {
            const count = boxes.filter((box) => box.checked).length;
            const level = count >= 4 ? 2 : count >= 2 ? 1 : 0;
            root.querySelector("[data-need-count]").textContent = count;
            root.querySelector("[data-need-bar]").style.width = `${(count / boxes.length) * 100}%`;
            root.querySelectorAll("[data-need-verdict]").forEach((el) => {
                el.classList.toggle("hidden", Number(el.dataset.needVerdict) !== level);
            });
        });
    });
}

// ---- Live activity ticker ----------------------------------------------
function initLiveTicker() {
    const el = document.querySelector("[data-live-ticker]");
    if (!el) return;

    const activities = JSON.parse(el.dataset.liveTicker || "[]");
    if (!activities.length) return;
    if (sessionStorage.getItem("liveTickerDismissed") === "1") return;

    el.classList.remove("hidden");
    const textEl = el.querySelector("[data-live-ticker-text]");
    let index = 0;

    function render() {
        const item = activities[index];
        if (textEl) textEl.innerHTML = `<span class="font-semibold">${item.name}</span> baru saja memesan ${item.service}`;
    }
    render();

    const timer = setInterval(() => {
        index = (index + 1) % activities.length;
        render();
    }, 4500);

    const closeBtn = el.querySelector("[data-live-ticker-close]");
    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            sessionStorage.setItem("liveTickerDismissed", "1");
            clearInterval(timer);
            el.classList.add("hidden");
        });
    }
}

// ---- Lead capture form (ContactCtaForm equivalent) ---------------------
function getCookie(name) {
    const match = document.cookie.match(new RegExp("(^| )" + name + "=([^;]+)"));
    return match ? decodeURIComponent(match[2]) : null;
}

function initLeadForms() {
    document.querySelectorAll("[data-lead-form]").forEach((form) => {
        const submitBtn = form.querySelector("[data-lead-submit]");
        const successBox = document.querySelector(form.dataset.leadForm + "-success");

        form.addEventListener("submit", async (event) => {
            event.preventDefault();
            form.querySelectorAll("[data-field-error]").forEach((el) => (el.textContent = ""));
            form.querySelectorAll(".field-input--error").forEach((el) => el.classList.remove("field-input--error"));

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.dataset.originalText || submitBtn.textContent;
                submitBtn.textContent = "Mengirim...";
            }

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    credentials: "same-origin",
                    headers: {
                        Accept: "application/json",
                        "X-XSRF-TOKEN": getCookie("XSRF-TOKEN"),
                    },
                    body: new FormData(form),
                });

                if (response.status === 422) {
                    const data = await response.json();
                    Object.entries(data.errors || {}).forEach(([field, messages]) => {
                        const input = form.querySelector(`[name="${field}"]`);
                        const errorEl = form.querySelector(`[data-field-error="${field}"]`);
                        if (input) input.classList.add("field-input--error");
                        if (errorEl) errorEl.textContent = messages[0];
                    });
                    return;
                }

                if (!response.ok) throw new Error("Request failed");

                form.classList.add("hidden");
                if (successBox) successBox.classList.remove("hidden");
            } catch (err) {
                // eslint-disable-next-line no-console
                console.error("Lead form submit failed", err);
                alert("Gagal mengirim pesan. Silakan coba lagi atau hubungi kami lewat WhatsApp.");
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = submitBtn.dataset.originalText;
                }
            }
        });
    });
}

// ---- Decorative WebGL islands (ScannerBackground / WaveBackground) -----
// These two components have zero Inertia coupling (no usePage/Link/router),
// so the exact same Vue SFCs used on the Inertia pages are reused here
// as small standalone Vue apps — guarantees pixel/behavior parity instead
// of hand-porting a WebGL shader effect to vanilla JS.
async function initWebglIslands() {
    const mounts = document.querySelectorAll("[data-vue-island]");
    if (!mounts.length) return;

    const { createApp } = await import("vue");
    const components = {
        ScannerBackground: () => import("./Components/Public/ScannerBackground.vue"),
        WaveBackground: () => import("./Components/Public/WaveBackground.vue"),
    };

    mounts.forEach(async (el) => {
        const name = el.dataset.vueIsland;
        const loader = components[name];
        if (!loader) return;
        const props = el.dataset.props ? JSON.parse(el.dataset.props) : {};
        const mod = await loader();
        createApp(mod.default, props).mount(el);
    });
}

// ---- Space theme visuals (no Vue needed) --------------------------------
// The starfield is tiny and mounts right away. The planet hero shows a
// pre-rendered poster (CSS) first; the WebGL version (ogl chunk) is only
// fetched after the page has fully loaded and the browser is idle, and
// planet.js itself skips it on phones / low-end devices.
function initSpaceVisuals() {
    const stars = document.querySelector("[data-starfield]");
    if (stars) mountStarField(stars);

    const planet = document.querySelector("[data-planet]");
    if (planet && canRunPlanet()) {
        const start = () => import("./space/planet").then(({ mountPlanet }) => mountPlanet(planet));
        const whenIdle = () =>
            "requestIdleCallback" in window ? requestIdleCallback(start, { timeout: 2000 }) : setTimeout(start, 500);
        if (document.readyState === "complete") whenIdle();
        else window.addEventListener("load", whenIdle, { once: true });
    }
}

// ---- Space theme: header glass-on-scroll + live HUD clock ---------------
// The space header starts transparent over the full-screen hero and turns
// into frosted glass once the page scrolls (or the mobile menu opens).
function initSpaceHeader() {
    const header = document.querySelector("[data-space-header]");
    if (!header) return;
    const panel = header.querySelector("[data-nav-panel]");
    const update = () => {
        const solid = window.scrollY > 40 || (panel && !panel.classList.contains("hidden"));
        header.classList.toggle("is-solid", Boolean(solid));
    };
    // First check on the next frame: reading scrollY during DOMContentLoaded
    // forces the page's first full layout inside this script task.
    requestAnimationFrame(update);
    window.addEventListener("scroll", update, { passive: true });
    header.querySelector("[data-nav-toggle]")?.addEventListener("click", () => requestAnimationFrame(update));
}

// Light/dark toggle for the space theme. The initial data-theme is set
// before paint by the inline script in layouts/public.blade.php; this only
// flips it, remembers the choice, and briefly enables colour transitions.
function initThemeToggle() {
    document.querySelectorAll("[data-theme-toggle]").forEach((btn) => {
        btn.addEventListener("click", () => {
            const root = document.documentElement;
            const next = root.getAttribute("data-theme") === "light" ? "dark" : "light";
            root.classList.add("theme-switching");
            root.setAttribute("data-theme", next);
            try {
                localStorage.setItem("theme", next);
            } catch (e) {}
            setTimeout(() => root.classList.remove("theme-switching"), 450);
        });
    });
}

// Cursor-following glow on the homepage service panels ([data-glow]).
function initGlowCards() {
    document.querySelectorAll("[data-glow]").forEach((card) => {
        card.addEventListener("pointermove", (e) => {
            const rect = card.getBoundingClientRect();
            card.style.setProperty("--gx", `${e.clientX - rect.left}px`);
            card.style.setProperty("--gy", `${e.clientY - rect.top}px`);
        });
    });
}

// Home hero: as it scrolls away, the planet "camera" pulls back (shrinks,
// lifts, dims) and the copy drifts slower than the page. Driven by one CSS
// variable (--hero-p, 0..1) updated at most once per frame.
function initHeroScroll() {
    const hero = document.querySelector(".space-hero");
    if (!hero || prefersReducedMotion()) return;
    let ticking = false;
    const update = () => {
        ticking = false;
        const h = hero.offsetHeight || 1;
        const p = Math.min(Math.max(window.scrollY / h, 0), 1);
        hero.style.setProperty("--hero-p", p.toFixed(3));
    };
    window.addEventListener(
        "scroll",
        () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        },
        { passive: true }
    );
    update();
}

// Desktop cursor ring: a soft lime ring trails the pointer and swells over
// links/buttons. Purely decorative: the native cursor stays visible, and it
// is skipped on touch devices and under reduced motion.
function initCursorRing() {
    if (prefersReducedMotion() || !window.matchMedia("(hover: hover) and (pointer: fine)").matches) return;
    if (!document.body.classList.contains("theme-space")) return;
    const ring = document.createElement("div");
    ring.className = "cursor-ring";
    ring.setAttribute("aria-hidden", "true");
    document.body.appendChild(ring);

    let x = -100;
    let y = -100;
    let rx = x;
    let ry = y;
    let raf = null;
    const loop = () => {
        rx += (x - rx) * 0.2;
        ry += (y - ry) * 0.2;
        ring.style.transform = `translate3d(${rx}px, ${ry}px, 0)`;
        raf = Math.abs(x - rx) + Math.abs(y - ry) > 0.3 ? requestAnimationFrame(loop) : null;
    };
    window.addEventListener(
        "pointermove",
        (e) => {
            x = e.clientX;
            y = e.clientY;
            ring.classList.add("is-visible");
            ring.classList.toggle("is-hover", Boolean(e.target.closest("a, button, [role='tab'], [data-tilt]")));
            if (!raf) raf = requestAnimationFrame(loop);
        },
        { passive: true }
    );
    document.addEventListener("pointerleave", () => ring.classList.remove("is-visible"));
}

// Headlines (x-public.headline): words rise out of a mask, staggered, when
// the headline scrolls into view. Arming (.split-ready) happens here, so
// without JS or with reduced motion the text is simply shown.
function initSplitHeadlines() {
    if (prefersReducedMotion()) return;
    const io = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add("is-in");
                io.unobserve(entry.target);
            });
        },
        { threshold: 0.3, rootMargin: "0px 0px -8% 0px" }
    );
    document.querySelectorAll('[data-split="scroll"]').forEach((el) => {
        el.classList.add("split-ready");
        io.observe(el);
    });
}

// /konsultasi + /kontak: 4-step lead wizard. Client-side checks gate steps
// 1-2 (same rules as LeadController::store), step 4 shows a summary, and the
// submit goes through fetch; a 422 jumps back to the step holding the first
// invalid field.
const WHATSAPP_PATTERN = /^[0-9+\-\s()]{8,30}$/;

function initLeadWizard() {
    const form = document.querySelector("[data-lead-wizard]");
    if (!form) return;

    const steps = [...form.querySelectorAll("[data-step]")];
    const nodes = [...document.querySelectorAll("[data-step-node]")];
    const counter = form.querySelector("[data-step-counter]");
    const errorBox = form.querySelector("[data-step-error]");
    const back = form.querySelector("[data-step-back]");
    const next = form.querySelector("[data-step-next]");
    const submit = form.querySelector("[data-step-submit]");
    const budgetInput = form.querySelector("[data-budget-input]");
    const field = (name) => form.elements.namedItem(name);
    const total = steps.length;
    let current = 1;

    const fieldStep = { name: 1, whatsapp: 1, email: 1, service_interested: 2, budget_estimate: 2, address: 3, message: 3 };

    function fillSummary() {
        const v = (n) => (field(n)?.value || "").trim();
        const set = (key, text) => {
            const el = form.querySelector(`[data-summary="${key}"]`);
            if (el) el.textContent = text;
        };
        set("identity", [v("name"), v("email"), v("whatsapp")].filter(Boolean).join(" · "));
        set("needs", [v("service_interested"), v("budget_estimate")].filter(Boolean).join(" · "));
        set("details", [v("address"), v("message")].filter(Boolean).join(" · ") || "Tidak diisi");
    }

    function show(step) {
        current = step;
        steps.forEach((s) => s.classList.toggle("hidden", Number(s.dataset.step) !== step));
        nodes.forEach((n) => {
            const i = Number(n.dataset.stepNode);
            n.dataset.state = i < step ? "done" : i === step ? "active" : "upcoming";
            if (i === step) n.setAttribute("aria-current", "step");
            else n.removeAttribute("aria-current");
        });
        if (counter) counter.textContent = String(step).padStart(2, "0");
        back.classList.toggle("invisible", step === 1);
        next.classList.toggle("hidden", step === total);
        submit.classList.toggle("hidden", step !== total);
        if (step === total) fillSummary();
        errorBox.textContent = "";
    }

    function stepValid(step) {
        if (step === 1) {
            if (!field("name").value.trim() || !WHATSAPP_PATTERN.test(field("whatsapp").value.trim())) {
                errorBox.textContent = "Lengkapi Nama Perusahaan dan Nomor WA yang valid terlebih dahulu.";
                return false;
            }
        }
        if (step === 2 && !field("service_interested").value.trim()) {
            errorBox.textContent = "Pilih Jenis Layanan terlebih dahulu.";
            return false;
        }
        return true;
    }

    next.addEventListener("click", () => {
        if (stepValid(current)) show(Math.min(current + 1, total));
    });
    back.addEventListener("click", () => show(Math.max(current - 1, 1)));
    form.querySelectorAll("[data-step-goto]").forEach((btn) =>
        btn.addEventListener("click", () => show(Number(btn.dataset.stepGoto)))
    );
    // Enter in a text field advances instead of submitting early.
    form.addEventListener("keydown", (e) => {
        if (e.key === "Enter" && e.target.tagName === "INPUT" && current < total) {
            e.preventDefault();
            next.click();
        }
    });

    form.querySelectorAll("[data-budget]").forEach((btn) =>
        btn.addEventListener("click", () => {
            budgetInput.value = btn.dataset.budget;
            form.querySelectorAll("[data-budget]").forEach((b) => b.setAttribute("aria-pressed", String(b === btn)));
        })
    );

    form.addEventListener("submit", async (event) => {
        event.preventDefault();
        if (current !== total) return;
        form.querySelectorAll("[data-field-error]").forEach((el) => (el.textContent = ""));
        submit.disabled = true;
        const label = submit.innerHTML;
        submit.textContent = "Mengirim...";

        try {
            const response = await fetch(form.action, {
                method: "POST",
                credentials: "same-origin",
                headers: { Accept: "application/json", "X-XSRF-TOKEN": getCookie("XSRF-TOKEN") },
                body: new FormData(form),
            });

            if (response.status === 422) {
                const data = await response.json();
                const entries = Object.entries(data.errors || {});
                entries.forEach(([name, messages]) => {
                    const el = form.querySelector(`[data-field-error="${name}"]`);
                    if (el) el.textContent = messages[0];
                });
                const firstStep = Math.min(...entries.map(([name]) => fieldStep[name] || total));
                show(firstStep);
                errorBox.textContent = "Periksa kembali isian yang ditandai.";
                return;
            }
            if (!response.ok) throw new Error("Request failed");

            const data = await response.json().catch(() => ({}));
            const message = document.querySelector("[data-lead-success-message]");
            if (message && data.message) message.textContent = data.message;
            document.querySelector("[data-lead-body]")?.classList.add("hidden");
            document.querySelector("[data-lead-success]")?.classList.remove("hidden");
            window.scrollTo({ top: 0, behavior: prefersReducedMotion() ? "auto" : "smooth" });
        } catch (err) {
            // eslint-disable-next-line no-console
            console.error("Lead wizard submit failed", err);
            errorBox.textContent = "Gagal mengirim. Silakan coba lagi atau hubungi kami lewat WhatsApp.";
        } finally {
            submit.disabled = false;
            submit.innerHTML = label;
        }
    });

    show(1);
}

// FAQ page: client-side search + category filter over the server-rendered
// list; visible rows are renumbered and the live count updated.
function initFaqFilter() {
    const root = document.querySelector("[data-faq-filter]");
    if (!root) return;
    const items = [...root.querySelectorAll("[data-faq-item]")];
    const search = root.querySelector("[data-faq-search]");
    const count = root.querySelector("[data-faq-count]");
    const empty = root.querySelector("[data-faq-empty]");
    const cats = [...root.querySelectorAll("[data-faq-category]")];
    let category = "";

    const apply = () => {
        const q = (search?.value || "").trim().toLowerCase();
        let n = 0;
        items.forEach((item) => {
            const show = (!category || item.dataset.category === category) && (!q || item.dataset.search.includes(q));
            item.classList.toggle("hidden", !show);
            if (show) {
                n++;
                const num = item.querySelector("[data-faq-num]");
                if (num) num.textContent = String(n).padStart(2, "0");
            }
            // the per-row category label is redundant once a category is chosen
            item.querySelector("[data-faq-cat-label]")?.classList.toggle("hidden", Boolean(category));
        });
        if (count) count.textContent = String(n).padStart(2, "0");
        empty?.classList.toggle("hidden", n > 0);
    };

    search?.addEventListener("input", apply);
    cats.forEach((btn) =>
        btn.addEventListener("click", () => {
            category = btn.dataset.faqCategory;
            cats.forEach((b) => b.setAttribute("aria-pressed", String(b === btn)));
            apply();
        })
    );
}

// Tentang Kami values console: tabs that auto-advance every VALUE_DWELL ms
// (the CSS progress bar runs for the same time), pause while hovered or
// focused, and jump on click / arrow keys.
const VALUE_DWELL = 4500;

function initValuesConsole() {
    document.querySelectorAll("[data-values-console]").forEach((root) => {
        const tabs = [...root.querySelectorAll("[data-value-tab]")];
        const panels = [...root.querySelectorAll("[data-value-panel]")];
        const icons = [...root.querySelectorAll("[data-value-icon]")];
        const counter = root.querySelector("[data-value-counter]");
        if (!tabs.length) return;

        root.style.setProperty("--value-dwell", `${VALUE_DWELL}ms`);
        let active = 0;
        let paused = false;
        let timer = null;

        const select = (i) => {
            active = i;
            tabs.forEach((t, n) => {
                t.setAttribute("aria-selected", String(n === i));
                t.tabIndex = n === i ? 0 : -1;
            });
            panels.forEach((p, n) => {
                p.classList.toggle("hidden", n !== i);
                p.classList.toggle("is-active", n === i);
            });
            icons.forEach((ic, n) => ic.classList.toggle("hidden", n !== i));
            if (counter) counter.textContent = String(i + 1).padStart(2, "0");
            // restart the progress bar animation on the new tab
            const bar = tabs[i].querySelector(".value-progress");
            if (bar) {
                bar.style.animation = "none";
                void bar.offsetWidth;
                bar.style.animation = "";
            }
        };

        const schedule = () => {
            clearInterval(timer);
            if (prefersReducedMotion()) return;
            timer = setInterval(() => {
                if (!paused) select((active + 1) % tabs.length);
            }, VALUE_DWELL);
        };

        tabs.forEach((tab, i) => {
            tab.addEventListener("click", () => {
                select(i);
                schedule();
            });
            tab.addEventListener("keydown", (e) => {
                if (e.key !== "ArrowDown" && e.key !== "ArrowUp") return;
                e.preventDefault();
                const next = (i + (e.key === "ArrowDown" ? 1 : -1) + tabs.length) % tabs.length;
                select(next);
                tabs[next].focus();
                schedule();
            });
        });
        const pause = (on) => {
            paused = on;
            root.classList.toggle("is-paused", on);
        };
        root.addEventListener("mouseenter", () => pause(true));
        root.addEventListener("mouseleave", () => pause(false));
        root.addEventListener("focusin", () => pause(true));
        root.addEventListener("focusout", () => pause(false));

        select(0);
        schedule();
    });
}

// Subtle 3D tilt toward the pointer on [data-tilt] cards (desktop pointers only).
function initTilt() {
    if (prefersReducedMotion() || !window.matchMedia("(hover: hover)").matches) return;
    document.querySelectorAll("[data-tilt]").forEach((card) => {
        card.addEventListener("pointermove", (e) => {
            const r = card.getBoundingClientRect();
            const x = (e.clientX - r.left) / r.width - 0.5;
            const y = (e.clientY - r.top) / r.height - 0.5;
            card.style.setProperty("--ry", `${x * 8}deg`);
            card.style.setProperty("--rx", `${-y * 8}deg`);
        });
        card.addEventListener("pointerleave", () => {
            card.style.setProperty("--ry", "0deg");
            card.style.setProperty("--rx", "0deg");
        });
    });
}

// Home "why us" radar: hovering/focusing a reason row locks the radar onto
// its blip; while idle the radar cycles through them on its own.
function initRadar() {
    document.querySelectorAll("[data-radar]").forEach((root) => {
        const rows = [...root.querySelectorAll("[data-radar-item]")];
        const blips = [...root.querySelectorAll("[data-radar-blip]")];
        if (!rows.length) return;

        let active = 0;
        let paused = false;
        const setActive = (i) => {
            active = i;
            rows.forEach((r, n) => r.classList.toggle("is-active", n === i));
            blips.forEach((b, n) => b.classList.toggle("is-active", n === i));
        };

        rows.forEach((row, i) => {
            const lock = () => {
                paused = true;
                setActive(i);
            };
            row.addEventListener("mouseenter", lock);
            row.addEventListener("focus", lock);
            row.addEventListener("mouseleave", () => (paused = false));
            row.addEventListener("blur", () => (paused = false));
        });

        if (!prefersReducedMotion()) {
            setInterval(() => {
                if (!paused) setActive((active + 1) % rows.length);
            }, 3200);
        }
    });
}

function initHudClock() {
    const clocks = document.querySelectorAll("[data-hud-clock]");
    if (!clocks.length) return;
    const tick = () => {
        const time = new Date()
            .toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit", second: "2-digit", timeZone: "Asia/Jakarta" })
            .replace(/\./g, ":");
        clocks.forEach((el) => (el.textContent = time + " WIB"));
    };
    tick();
    setInterval(tick, 1000);
}

// Boot in two phases. Everything the visitor can read or use (navigation,
// forms, tabs, reveals) is wired immediately; purely decorative effects
// (starfield, radar, tilt, cursor, scroll parallax, clock...) wait until the
// page has loaded and the browser is idle, so they never compete with first
// paint or block input on slower phones.
function whenIdle(fn) {
    const run = () =>
        "requestIdleCallback" in window ? requestIdleCallback(fn, { timeout: 2000 }) : setTimeout(fn, 200);
    if (document.readyState === "complete") run();
    else window.addEventListener("load", run, { once: true });
}

// ---- Responsive-image safety net ---------------------------------------
// /img/{w}/{path}.webp is generated on demand; if a host can't (say a stale
// route cache), fall back to the original upload at /storage/{path} instead
// of leaving a broken image. Images that already failed before this module
// ran are caught by the sweep; later (lazy) ones by the capture listener.
const RESIZED_IMG = /\/img\/\d+\/(.+)\.webp(?:[?#].*)?$/;

function fallbackToOriginal(img) {
    const match = (img.currentSrc || img.src || "").match(RESIZED_IMG);
    if (!match || img.dataset.imgFallback) return;
    img.dataset.imgFallback = "1";
    img.removeAttribute("srcset");
    img.removeAttribute("sizes");
    img.src = "/storage/" + match[1];
}

document.addEventListener(
    "error",
    (event) => {
        if (event.target instanceof HTMLImageElement) fallbackToOriginal(event.target);
    },
    true,
);

function initImageFallback() {
    document.querySelectorAll("img").forEach((img) => {
        if (img.complete && img.naturalWidth === 0) fallbackToOriginal(img);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // essential
    initImageFallback();
    initNavbar();
    initSpaceHeader();
    initThemeToggle();
    initScrollReveal();
    initSplitHeadlines();
    initFaqAccordion();
    initFaqFilter();
    initRoiCalculator();
    initNeedCheck();
    initPricingTabs();
    initBillingToggle();
    initLeadForms();
    initLeadWizard();
    initValuesConsole();

    // decorative
    whenIdle(() => {
        initSpaceVisuals();
        initHudClock();
        initLiveTicker();
        initRadar();
        initGlowCards();
        initTilt();
        initHeroScroll();
        initCursorRing();
        initWebglIslands();
    });
});
