// Whether this device should run the animated WebGL planet at all. Checked
// *before* the planet/ogl chunk is imported, so phones and low-end devices
// never download it (they keep the pre-rendered poster).
export function canRunPlanet() {
    return (
        window.innerWidth >= 768 &&
        !window.matchMedia?.("(prefers-reduced-motion: reduce)").matches &&
        navigator.connection?.saveData !== true &&
        (navigator.hardwareConcurrency || 8) > 4
    );
}
