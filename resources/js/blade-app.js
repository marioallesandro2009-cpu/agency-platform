import "../css/app.css";
import "./bootstrap";

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
    if (prefersReducedMotion()) return;

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
        // Space theme (homepage): same no-Inertia-coupling rule applies.
        Planet: () => import("./Components/Public/Planet.vue"),
        StarField: () => import("./Components/Public/StarField.vue"),
        SpacePreloader: () => import("./Components/Public/SpacePreloader.vue"),
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
    update();
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

document.addEventListener("DOMContentLoaded", () => {
    initScrollReveal();
    initSpaceHeader();
    initThemeToggle();
    initHudClock();
    initGlowCards();
    initRadar();
    initTilt();
    initNavbar();
    initPricingTabs();
    initBillingToggle();
    initFaqAccordion();
    initLiveTicker();
    initLeadForms();
    initWebglIslands();
});
