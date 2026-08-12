// Fixed taxonomy for the pricing catalogue: 4 service categories, each with
// 3 tiers (Seed/Growth/Scale). Shared between the admin form (Admin/Pricing/
// Index.vue) and the public pricing UI (PricingSection.vue) so both stay in
// sync with the same values/labels/order.
export const PRICING_CATEGORIES = [
    { value: "landing_page", label: "Landing Page" },
    { value: "company_profile", label: "Company Profile" },
    { value: "ecommerce", label: "E-commerce" },
    { value: "sistem_informasi", label: "Sistem Informasi" },
];

export const PRICING_TIERS = [
    { value: "seed", label: "Seed" },
    { value: "growth", label: "Growth" },
    { value: "scale", label: "Scale" },
];

export const TIER_ORDER = { seed: 0, growth: 1, scale: 2 };
