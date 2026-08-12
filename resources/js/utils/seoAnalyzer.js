function stripHtml(html) {
    return (html || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
}

function countOccurrences(haystack, needle) {
    if (!needle) return 0;
    return haystack.split(needle).length - 1;
}

/**
 * Lightweight Yoast-style on-page SEO checklist for a single blog post,
 * scored against its focus keyword. Runs entirely client-side so it can
 * update live as the admin types, with no server round-trip.
 */
export function analyzeSeo({ title, slug, content, seoTitle, seoDescription, focusKeyword }) {
    const keyword = (focusKeyword || "").trim().toLowerCase();

    if (!keyword) {
        return { score: 0, hasKeyword: false, checks: [] };
    }

    const plainContent = stripHtml(content);
    const lowerContent = plainContent.toLowerCase();
    const wordCount = plainContent ? plainContent.split(" ").filter(Boolean).length : 0;
    const effectiveTitle = (seoTitle || title || "").trim();
    const effectiveDescription = (seoDescription || "").trim();
    const contains = (text) => text.toLowerCase().includes(keyword);

    const checks = [];

    checks.push({
        id: "title",
        status: contains(effectiveTitle) ? "good" : "bad",
        message: contains(effectiveTitle)
            ? "Focus keyword muncul di SEO title."
            : "Focus keyword belum muncul di SEO title.",
    });

    checks.push({
        id: "description",
        status: !effectiveDescription ? "bad" : contains(effectiveDescription) ? "good" : "bad",
        message: !effectiveDescription
            ? "Meta description belum diisi."
            : contains(effectiveDescription)
            ? "Focus keyword muncul di meta description."
            : "Focus keyword belum muncul di meta description.",
    });

    const slugifiedKeyword = keyword.replace(/\s+/g, "-");
    const slugHasKeyword = (slug || "").toLowerCase().includes(slugifiedKeyword);
    checks.push({
        id: "slug",
        status: slugHasKeyword ? "good" : "ok",
        message: slugHasKeyword
            ? "Focus keyword muncul di slug/URL."
            : "Pertimbangkan menambahkan focus keyword ke slug/URL.",
    });

    const intro = lowerContent.slice(0, 150);
    checks.push({
        id: "intro",
        status: intro.includes(keyword) ? "good" : "bad",
        message: intro.includes(keyword)
            ? "Focus keyword muncul di paragraf pertama."
            : "Focus keyword sebaiknya muncul di 150 karakter pertama konten.",
    });

    const keywordWordCount = keyword.split(/\s+/).length;
    const occurrences = countOccurrences(lowerContent, keyword);
    const density = wordCount > 0 ? (occurrences * keywordWordCount / wordCount) * 100 : 0;
    let densityStatus = "bad";
    if (density >= 0.5 && density <= 2.5) densityStatus = "good";
    else if (density > 0) densityStatus = "ok";
    checks.push({
        id: "density",
        status: densityStatus,
        message: `Kepadatan keyword: ${density.toFixed(1)}%. `
            + (density === 0
                ? "Focus keyword tidak muncul di konten."
                : densityStatus === "good"
                ? "Sudah ideal (0.5%-2.5%)."
                : density < 0.5
                ? "Agak jarang, coba tambahkan beberapa kali lagi."
                : "Terlalu sering, hindari keyword stuffing."),
    });

    const titleLength = effectiveTitle.length;
    checks.push({
        id: "title_length",
        status: titleLength >= 40 && titleLength <= 60 ? "good" : titleLength > 0 ? "ok" : "bad",
        message: `Panjang SEO title: ${titleLength} karakter (ideal 40-60).`,
    });

    const descriptionLength = effectiveDescription.length;
    checks.push({
        id: "description_length",
        status: descriptionLength >= 120 && descriptionLength <= 160 ? "good" : descriptionLength > 0 ? "ok" : "bad",
        message: `Panjang meta description: ${descriptionLength} karakter (ideal 120-160).`,
    });

    checks.push({
        id: "content_length",
        status: wordCount >= 300 ? "good" : wordCount >= 150 ? "ok" : "bad",
        message: `Panjang konten: ${wordCount} kata (minimal disarankan 300 kata).`,
    });

    const headingMatches = (content || "").match(/<h[23][^>]*>(.*?)<\/h[23]>/gi) || [];
    const hasHeadings = headingMatches.length > 0;
    const keywordInHeading = headingMatches.some((h) => h.toLowerCase().includes(keyword));
    checks.push({
        id: "headings",
        status: !hasHeadings ? "ok" : keywordInHeading ? "good" : "bad",
        message: !hasHeadings
            ? "Tidak ada subheading (H2/H3) terdeteksi di konten — opsional, tapi membantu SEO."
            : keywordInHeading
            ? "Focus keyword muncul di salah satu subheading."
            : "Focus keyword belum muncul di subheading manapun.",
    });

    const weight = { good: 1, ok: 0.5, bad: 0 };
    const total = checks.reduce((sum, check) => sum + weight[check.status], 0);
    const score = Math.round((total / checks.length) * 100);

    return { score, hasKeyword: true, checks };
}

export function seoScoreLabel(score) {
    if (score >= 80) return "Baik";
    if (score >= 50) return "Cukup";
    return "Kurang";
}
