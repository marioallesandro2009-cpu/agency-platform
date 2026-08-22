function stripHtml(html) {
    return (html || "").replace(/<[^>]*>/g, " ").replace(/\s+/g, " ").trim();
}

function countOccurrences(haystack, needle) {
    if (!needle) return 0;
    return haystack.split(needle).length - 1;
}

function countWords(text) {
    return text ? text.split(" ").filter(Boolean).length : 0;
}

/**
 * Words-per-minute used for the public-facing "estimasi waktu baca" badge.
 * Shared here so the admin analyzer and the public blog pages can never
 * drift apart on the number.
 */
const READING_WPM = 200;

export function readingTimeMinutes(content) {
    const words = countWords(stripHtml(content));
    return Math.max(1, Math.ceil(words / READING_WPM));
}

function currentOrigin() {
    return typeof window !== "undefined" ? window.location.origin : "";
}

/**
 * Lightweight Yoast-style on-page SEO checklist for a single blog post.
 * Runs entirely client-side so it can update live as the admin types, with
 * no server round-trip.
 *
 * General checks (title/description length, slug quality, heading
 * structure, image alt text, internal/external links, readability) run
 * regardless of focus keyword. Keyword-specific checks (title/description/
 * slug/intro/heading placement, density) only run once a focus keyword is
 * set — `hasKeyword` tells the caller whether those ran.
 */
export function analyzeSeo({ title, slug, content, seoTitle, seoDescription, focusKeyword }) {
    const keyword = (focusKeyword || "").trim().toLowerCase();
    const hasKeyword = keyword.length > 0;

    const rawContent = content || "";
    const plainContent = stripHtml(rawContent);
    const lowerContent = plainContent.toLowerCase();
    const wordCount = countWords(plainContent);
    const effectiveTitle = (seoTitle || title || "").trim();
    const effectiveDescription = (seoDescription || "").trim();
    const contains = (text) => text.toLowerCase().includes(keyword);

    const checks = [];

    // --- Title & meta description length (general) ---
    const titleLength = effectiveTitle.length;
    checks.push({
        id: "title_length",
        status: titleLength >= 50 && titleLength <= 60 ? "good" : titleLength > 0 ? "ok" : "bad",
        message: `Panjang SEO title: ${titleLength} karakter (ideal 50-60).`,
    });

    const descriptionLength = effectiveDescription.length;
    checks.push({
        id: "description_length",
        status: descriptionLength >= 150 && descriptionLength <= 160 ? "good" : descriptionLength > 0 ? "ok" : "bad",
        message: `Panjang meta description: ${descriptionLength} karakter (ideal 150-160).`,
    });

    // --- Slug quality (general) ---
    const slugValue = (slug || "").trim();
    const slugPattern = /^[a-z0-9]+(-[a-z0-9]+)*$/;
    if (!slugValue) {
        checks.push({ id: "slug_quality", status: "bad", message: "Slug belum diisi." });
    } else if (!slugPattern.test(slugValue)) {
        checks.push({
            id: "slug_quality",
            status: "bad",
            message: "Slug mengandung karakter tidak SEO-friendly (pakai huruf kecil, angka, dan tanda hubung saja).",
        });
    } else if (slugValue.length > 75) {
        checks.push({
            id: "slug_quality",
            status: "ok",
            message: `Slug agak panjang (${slugValue.length} karakter) — pertimbangkan mempersingkat.`,
        });
    } else {
        checks.push({ id: "slug_quality", status: "good", message: "Slug pendek, rapi, dan deskriptif." });
    }

    // --- Heading structure (general) ---
    const headingLevels = [...rawContent.matchAll(/<h([234])[^>]*>/gi)].map((m) => Number(m[1]));
    const hasSubheadings = headingLevels.length > 0;
    checks.push({
        id: "heading_presence",
        status: hasSubheadings ? "good" : "ok",
        message: hasSubheadings
            ? `Ditemukan ${headingLevels.length} subheading (H2-H4) untuk memecah konten.`
            : "Belum ada subheading (H2/H3) — tambahkan untuk memecah konten yang panjang.",
    });

    if (hasSubheadings) {
        let seenH2 = false;
        let jumped = false;
        for (const level of headingLevels) {
            if (level === 2) seenH2 = true;
            else if (!seenH2) {
                jumped = true;
                break;
            }
        }
        checks.push({
            id: "heading_hierarchy",
            status: jumped ? "bad" : "good",
            message: jumped
                ? "Urutan heading loncat — ada H3/H4 sebelum H2 manapun."
                : "Urutan heading rapi (H2 mendahului H3/H4).",
        });
    }

    // --- Image alt text (general) ---
    const imgTags = [...rawContent.matchAll(/<img\b[^>]*>/gi)].map((m) => m[0]);
    if (imgTags.length > 0) {
        const missingAlt = imgTags.filter((tag) => {
            const altMatch = tag.match(/alt="([^"]*)"/i);
            return !altMatch || !altMatch[1].trim();
        }).length;
        checks.push({
            id: "image_alt",
            status: missingAlt === 0 ? "good" : "bad",
            message:
                missingAlt === 0
                    ? `Semua ${imgTags.length} gambar di konten sudah punya alt text.`
                    : `${missingAlt} dari ${imgTags.length} gambar belum punya alt text.`,
        });
    } else {
        checks.push({
            id: "image_alt",
            status: "ok",
            message: "Tidak ada gambar di konten — opsional, tapi gambar dengan alt text membantu SEO & aksesibilitas.",
        });
    }

    // --- Internal / external links (general) ---
    const hrefs = [...rawContent.matchAll(/<a\b[^>]*href="([^"]+)"[^>]*>/gi)].map((m) => m[1]);
    const origin = currentOrigin();
    let internalCount = 0;
    let externalCount = 0;
    for (const href of hrefs) {
        if (/^(mailto:|tel:|#)/i.test(href)) continue;
        if (/^https?:\/\//i.test(href)) {
            if (origin && href.startsWith(origin)) internalCount++;
            else externalCount++;
        } else {
            internalCount++;
        }
    }
    checks.push({
        id: "internal_links",
        status: internalCount >= 2 ? "good" : internalCount >= 1 ? "ok" : "bad",
        message: `${internalCount} internal link ditemukan (rekomendasi minimal 2).`,
    });
    checks.push({
        id: "external_links",
        status: externalCount >= 1 ? "good" : "bad",
        message: `${externalCount} external link ditemukan (rekomendasi minimal 1).`,
    });

    // --- Readability (general) ---
    const paragraphs = [...rawContent.matchAll(/<p[^>]*>([\s\S]*?)<\/p>/gi)]
        .map((m) => stripHtml(m[1]))
        .filter(Boolean);
    const sentences = plainContent.split(/(?<=[.!?])\s+/).map((s) => s.trim()).filter(Boolean);
    const avgWordsPerParagraph = paragraphs.length
        ? paragraphs.reduce((sum, p) => sum + countWords(p), 0) / paragraphs.length
        : 0;
    const avgWordsPerSentence = sentences.length ? wordCount / sentences.length : 0;
    const longParagraphs = paragraphs.filter((p) => countWords(p) > 120).length;
    const longSentences = avgWordsPerSentence > 25;

    let readabilityStatus = "ok";
    if (paragraphs.length > 0) {
        readabilityStatus = longParagraphs === 0 && !longSentences ? "good" : longParagraphs > 0 && longSentences ? "bad" : "ok";
    }
    checks.push({
        id: "readability",
        status: readabilityStatus,
        message:
            paragraphs.length === 0
                ? "Belum ada paragraf terdeteksi di konten."
                : `Rata-rata ${Math.round(avgWordsPerParagraph)} kata/paragraf, ${Math.round(avgWordsPerSentence)} kata/kalimat.`
                  + (longParagraphs > 0 ? ` ${longParagraphs} paragraf terlalu panjang (>120 kata).` : "")
                  + (longSentences ? " Kalimat rata-rata cukup panjang, coba pecah jadi lebih pendek." : ""),
    });

    // --- Content length (general) ---
    checks.push({
        id: "content_length",
        status: wordCount >= 300 ? "good" : wordCount >= 150 ? "ok" : "bad",
        message: `Panjang konten: ${wordCount} kata (minimal disarankan 300 kata).`,
    });

    // --- Focus-keyword-specific checks ---
    if (hasKeyword) {
        checks.push({
            id: "keyword_title",
            status: contains(effectiveTitle) ? "good" : "bad",
            message: contains(effectiveTitle)
                ? "Focus keyword muncul di SEO title."
                : "Focus keyword belum muncul di SEO title.",
        });

        checks.push({
            id: "keyword_description",
            status: !effectiveDescription ? "bad" : contains(effectiveDescription) ? "good" : "bad",
            message: !effectiveDescription
                ? "Meta description belum diisi."
                : contains(effectiveDescription)
                ? "Focus keyword muncul di meta description."
                : "Focus keyword belum muncul di meta description.",
        });

        const slugifiedKeyword = keyword.replace(/\s+/g, "-");
        const slugHasKeyword = slugValue.toLowerCase().includes(slugifiedKeyword);
        checks.push({
            id: "keyword_slug",
            status: slugHasKeyword ? "good" : "ok",
            message: slugHasKeyword
                ? "Focus keyword muncul di slug/URL."
                : "Pertimbangkan menambahkan focus keyword ke slug/URL.",
        });

        const intro = lowerContent.slice(0, 150);
        checks.push({
            id: "keyword_intro",
            status: intro.includes(keyword) ? "good" : "bad",
            message: intro.includes(keyword)
                ? "Focus keyword muncul di paragraf pertama."
                : "Focus keyword sebaiknya muncul di 150 karakter pertama konten.",
        });

        const headingTextMatches = (rawContent.match(/<h[234][^>]*>(.*?)<\/h[234]>/gi) || []);
        const keywordInHeading = headingTextMatches.some((h) => h.toLowerCase().includes(keyword));
        checks.push({
            id: "keyword_heading",
            status: !hasSubheadings ? "ok" : keywordInHeading ? "good" : "bad",
            message: !hasSubheadings
                ? "Tidak ada subheading untuk dicek — tambahkan H2/H3 yang memuat focus keyword."
                : keywordInHeading
                ? "Focus keyword muncul di salah satu subheading."
                : "Focus keyword belum muncul di subheading manapun.",
        });

        const keywordWordCount = keyword.split(/\s+/).length;
        const occurrences = countOccurrences(lowerContent, keyword);
        const density = wordCount > 0 ? ((occurrences * keywordWordCount) / wordCount) * 100 : 0;
        let densityStatus = "bad";
        if (density >= 0.5 && density <= 2.5) densityStatus = "good";
        else if (density > 0) densityStatus = "ok";
        checks.push({
            id: "keyword_density",
            status: densityStatus,
            message:
                `Kepadatan keyword: ${density.toFixed(1)}%. `
                + (density === 0
                    ? "Focus keyword tidak muncul di konten."
                    : densityStatus === "good"
                    ? "Sudah ideal (0.5%-2.5%)."
                    : density < 0.5
                    ? "Agak jarang, coba tambahkan beberapa kali lagi."
                    : "Terlalu sering, hindari keyword stuffing."),
        });
    }

    const weight = { good: 1, ok: 0.5, bad: 0 };
    const total = checks.reduce((sum, check) => sum + weight[check.status], 0);
    const score = checks.length ? Math.round((total / checks.length) * 100) : 0;

    return { score, hasKeyword, wordCount, readingTime: readingTimeMinutes(content), checks };
}

export function seoScoreLabel(score) {
    if (score >= 80) return "Baik";
    if (score >= 50) return "Cukup";
    return "Kurang";
}

export function scoreDotColor(score) {
    if (score >= 80) return "bg-green-500";
    if (score >= 50) return "bg-yellow-500";
    return "bg-red-500";
}

export function scorePanelColor(score) {
    if (score >= 80) return "bg-green-50 border-green-200";
    if (score >= 50) return "bg-yellow-50 border-yellow-200";
    return "bg-red-50 border-red-200";
}

export function checkDotColor(status) {
    return { good: "bg-green-500", ok: "bg-yellow-500", bad: "bg-red-500" }[status];
}
