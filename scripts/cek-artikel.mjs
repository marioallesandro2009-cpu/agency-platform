#!/usr/bin/env node
// Pemeriksa artikel blog mingguan. Hanya memakai Node bawaan, tanpa npm install.
//   node scripts/cek-artikel.mjs                    periksa semua berkas di database/seeders/data/blog-mingguan
//   node scripts/cek-artikel.mjs --file <json>      periksa satu berkas
//   node scripts/cek-artikel.mjs --expect 5         wajib tepat 5 artikel per berkas
//   node scripts/cek-artikel.mjs --selftest         uji aturan pemeriksa itu sendiri
// Keluar dengan kode 1 jika ada pelanggaran: artikel itu TIDAK boleh diterbitkan.
import { readFileSync, readdirSync, existsSync, statSync } from 'node:fs';
import { join, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const DATA_DIR = join(ROOT, 'database/seeders/data/blog-mingguan');
const IMAGE_DIR = join(ROOT, 'public/images/blog');
const ALLOWED_LICENSES = ['CC0', 'PDM', 'Public Domain', 'Karya sendiri', 'Unsplash License', 'Pexels License', 'Pixabay License'];
const CATEGORIES = ['tips-website', 'seo', 'umkm', 'studi-kasus', 'custom-software', 'website-korporat'];

// ---- Daftar larangan (huruf kecil, dicocokkan sebagai kata utuh) ----
const BANNED = {
  'toko online / e-commerce (topik dihentikan)': ['toko online', 'e-commerce', 'ecommerce', 'marketplace', 'olshop', 'dropship', 'dropshipping', 'jualan online', 'belanja online', 'keranjang belanja', 'online store'],
  'hukum (dilarang membahas UU)': ['undang-undang', 'pasal', 'perpres', 'permen', 'peraturan pemerintah', 'peraturan menteri', 'kuhp', 'perda', 'uu'],
  'klaim mutlak atau tidak terbukti': ['pasti', '100%', 'dijamin', 'terjamin', 'garansi', 'terbaik', 'nomor 1', 'nomor satu', 'satu-satunya', 'tercepat', 'termurah', 'tanpa risiko', 'terbukti', 'rahasia', 'cepat kaya', 'instan'],
  'provokatif atau menakut-nakuti': ['bangkrut', 'hancur', 'tipu', 'penipu', 'penipuan', 'bodoh', 'tolol', 'goblok', 'idiot', 'sampah', 'kuno', 'ketinggalan zaman', 'sebelum terlambat', 'jangan sampai menyesal', 'bahaya besar', 'mematikan bisnis'],
  'SARA, politik, atau pelecehan': ['agama', 'islam', 'kristen', 'katolik', 'hindu', 'buddha', 'budha', 'yahudi', 'kafir', 'pribumi', 'cina', 'tionghoa', 'ras', 'etnis', 'suku', 'partai', 'pemilu', 'pilpres', 'pilkada', 'capres', 'presiden', 'seksi', 'sexy', 'porno', 'mesum', 'cabul', 'bokep', 'bangsat', 'anjing', 'babi', 'bajingan', 'kontol', 'memek', 'jancuk'],
  'merek atau lembaga (aturan penulisan blog)': ['exodine', 'exodineweb', 'whatsapp', 'google', 'facebook', 'instagram', 'tiktok', 'youtube', 'meta', 'shopee', 'tokopedia', 'lazada', 'bukalapak', 'blibli', 'gojek', 'grab', 'microsoft', 'excel', 'wordpress', 'shopify', 'woocommerce', 'laravel', 'who', 'bps', 'bpom', 'ojk', 'kominfo', 'komdigi', 'kemenkeu', 'mckinsey', 'gartner', 'statista', 'wikipedia'],
  'cerita klien karangan': ['klien kami', 'pelanggan kami', 'testimoni', 'studi kasus kami', 'sudah dipercaya', 'telah dipercaya'],
};
const BANNED_H2 = /rangkuman|ringkasan|kesimpulan|masalah|solusi|fakta/i;
const NUMERIC_CLAIM = /\d+\s?%|\bpersen\b|\brp\s?\d|\b\d+\s?(juta|miliar|triliun)\b/i;

const words = (text) => text.toLowerCase().match(/[a-z0-9àâçéèêëîïôûùüÿñæœ'-]+/g) ?? [];
const stripTags = (html) => html.replace(/<[^>]+>/g, ' ').replace(/&nbsp;|&amp;|&[a-z]+;/g, ' ').replace(/\s+/g, ' ').trim();
const visibleText = (html) => stripTags(html);
const escapeRe = (s) => s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
const hasPhrase = (text, phrase) => new RegExp(`(^|[^a-z0-9-])${escapeRe(phrase)}($|[^a-z0-9-])`, 'i').test(text);
const count = (text, phrase) => (text.toLowerCase().match(new RegExp(`(^|[^a-z0-9-])${escapeRe(phrase)}(?=$|[^a-z0-9-])`, 'g')) ?? []).length;

function jpegSize(path) {
  const b = readFileSync(path);
  if (b[0] !== 0xff || b[1] !== 0xd8) return null;
  let i = 2;
  while (i < b.length) {
    if (b[i] !== 0xff) { i++; continue; }
    const marker = b[i + 1];
    if (marker >= 0xc0 && marker <= 0xcf && ![0xc4, 0xc8, 0xcc].includes(marker)) {
      return { height: b.readUInt16BE(i + 5), width: b.readUInt16BE(i + 7) };
    }
    i += 2 + b.readUInt16BE(i + 2);
  }
  return null;
}

function existingSlugs() {
  const found = new Set();
  const seederDir = join(ROOT, 'database/seeders');
  const files = [join(seederDir, 'BlogPostSeeder.php'), ...['blog-artikel', 'blog-website-posts', 'blog-software-posts'].map((n) => join(seederDir, `data/${n}.php`))];
  for (const f of files) {
    if (!existsSync(f)) continue;
    for (const m of readFileSync(f, 'utf8').matchAll(/["']slug["']\s*=>\s*["']([a-z0-9-]+)["']/g)) found.add(m[1]);
  }
  return found;
}

function existingTitles() {
  const found = [];
  const seederDir = join(ROOT, 'database/seeders');
  const files = [join(seederDir, 'BlogPostSeeder.php'), ...['blog-artikel', 'blog-website-posts', 'blog-software-posts'].map((n) => join(seederDir, `data/${n}.php`))];
  for (const f of files) {
    if (!existsSync(f)) continue;
    for (const m of readFileSync(f, 'utf8').matchAll(/["']title["']\s*=>\s*["']([^"']+)["']/g)) found.push(m[1]);
  }
  return found;
}

function licenseRows() {
  const file = join(IMAGE_DIR, 'LISENSI.csv');
  const rows = {};
  if (!existsSync(file)) return rows;
  for (const line of readFileSync(file, 'utf8').split(/\r?\n/).slice(1)) {
    if (!line.trim()) continue;
    const [berkas, sumber, url, penulis, lisensi] = line.split(',').map((s) => s.trim());
    rows[berkas] = { sumber, url, penulis, lisensi };
  }
  return rows;
}

const similarity = (a, b) => {
  const A = new Set(words(a)), B = new Set(words(b));
  const inter = [...A].filter((w) => B.has(w)).length;
  return inter / Math.max(1, new Set([...A, ...B]).size);
};

export function checkPost(post, ctx) {
  const errors = [];
  const fail = (msg) => errors.push(msg);

  for (const f of ['title', 'seo_title', 'slug', 'category', 'focus_keyword', 'excerpt', 'seo_description', 'published_at', 'image', 'image_alt', 'content']) {
    if (!post[f] || typeof post[f] !== 'string') fail(`kolom ${f} kosong`);
  }
  if (errors.length) return errors;

  const { title, seo_title, slug, category, focus_keyword: kw, excerpt, seo_description, content, image, image_alt } = post;

  if (!/^[a-z0-9]+(-[a-z0-9]+)*$/.test(slug)) fail('slug harus huruf kecil dan tanda hubung');
  if (ctx.slugs.has(slug)) fail(`slug "${slug}" sudah dipakai artikel lain`);
  if (!CATEGORIES.includes(category)) fail(`kategori "${category}" tidak dikenal (${CATEGORIES.join(', ')})`);
  if (!/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(post.published_at)) fail('published_at harus YYYY-MM-DD HH:MM:SS');

  // Judul dan SEO
  if (!/\d/.test(title)) fail('judul harus memuat angka');
  if (title.length < 30 || title.length > 75) fail(`judul ${title.length} karakter, harus 30-75`);
  if (seo_title.length > 62) fail(`seo_title ${seo_title.length} karakter, maksimal 62`);
  if (seo_description.length < 120 || seo_description.length > 160) fail(`seo_description ${seo_description.length} karakter, harus 120-160`);
  if (excerpt.length < 80 || excerpt.length > 200) fail(`excerpt ${excerpt.length} karakter, harus 80-200`);
  if (kw !== kw.toLowerCase() || kw.split(' ').length < 2 || kw.split(' ').length > 5) fail('focus_keyword huruf kecil, 2-5 kata');
  if (!hasPhrase(title, kw)) fail('judul harus memuat focus_keyword');
  if (!hasPhrase(seo_title, kw)) fail('seo_title harus memuat focus_keyword');
  if (!hasPhrase(seo_description, kw)) fail('seo_description harus memuat focus_keyword');

  // Isi
  const text = visibleText(content);
  const total = words(text).length;
  if (total < 1000) fail(`isi ${total} kata, minimal 1000`);
  const firstP = (content.match(/<p>([\s\S]*?)<\/p>/) ?? [])[1] ?? '';
  if (!hasPhrase(stripTags(firstP), kw)) fail('paragraf pertama harus memuat focus_keyword');
  const h2s = [...content.matchAll(/<h2[^>]*>([\s\S]*?)<\/h2>/g)].map((m) => stripTags(m[1]));
  if (h2s.length < 5) fail(`hanya ${h2s.length} subjudul h2, minimal 5`);
  if (h2s.filter((h) => hasPhrase(h, kw)).length < 2) fail('focus_keyword harus ada di minimal 2 subjudul h2');
  for (const h of h2s) if (BANNED_H2.test(h)) fail(`subjudul "${h}" memakai label terlarang (masalah/solusi/fakta/rangkuman)`);
  if (/<h1/i.test(content)) fail('isi tidak boleh memuat h1');
  const kwCount = count(text, kw);
  const density = (kwCount * kw.split(' ').length * 100) / Math.max(1, total);
  if (density < 0.6 || density > 2.2) fail(`kepadatan kata kunci ${density.toFixed(2)}% (${kwCount}x), harus 0.6-2.2% agar wajar`);

  // Tautan
  const links = [...content.matchAll(/<a\s+[^>]*href="([^"]+)"[^>]*>/g)].map((m) => ({ href: m[1], tag: m[0] }));
  const internal = links.filter((l) => l.href.startsWith('/'));
  const external = links.filter((l) => /^https?:\/\//.test(l.href));
  if (internal.length < 2) fail('minimal 2 tautan internal');
  if (!internal.some((l) => /^\/(konsultasi|jasa-)/.test(l.href))) fail('harus ada tautan ke /konsultasi atau halaman /jasa-*');
  if (external.length < 1) fail('minimal 1 tautan eksternal ke sumber primer');
  for (const l of external) {
    if (!l.href.startsWith('https://')) fail(`tautan eksternal harus https: ${l.href}`);
    if (!/target="_blank"/.test(l.tag) || !/rel="[^"]*noopener/.test(l.tag)) fail(`tautan eksternal perlu target="_blank" rel="noopener": ${l.href}`);
  }
  for (const l of internal) {
    if (/toko-online/.test(l.href)) fail(`tautan ke topik toko online: ${l.href}`);
    const blog = l.href.match(/^\/blog\/([a-z0-9-]+)$/);
    if (blog && !ctx.slugs.has(blog[1]) && !ctx.batchSlugs.has(blog[1])) fail(`tautan blog tidak ada: ${l.href}`);
  }
  if (NUMERIC_CLAIM.test(text) && external.length < 1) fail('ada angka tapi tidak ada tautan ke sumber primer');

  // Larangan kata
  const all = `${title} ${seo_title} ${excerpt} ${seo_description} ${kw} ${image_alt} ${text}`;
  for (const [label, list] of Object.entries(BANNED)) {
    const hit = list.filter((w) => hasPhrase(all, w));
    if (hit.length) fail(`kata terlarang [${label}]: ${hit.join(', ')}`);
  }

  // Gambar dan lisensi
  const imgFile = join(IMAGE_DIR, `${image}.jpg`);
  if (!existsSync(imgFile)) fail(`gambar public/images/blog/${image}.jpg tidak ada`);
  else {
    const size = statSync(imgFile).size;
    if (size > 600 * 1024) fail(`gambar ${(size / 1024).toFixed(0)} KB, maksimal 600 KB`);
    const dim = jpegSize(imgFile);
    if (!dim) fail('gambar bukan JPEG yang valid');
    else if (dim.width < 1000) fail(`lebar gambar ${dim.width}px, minimal 1000px`);
    const lic = ctx.licenses[`${image}.jpg`];
    if (!lic) fail(`gambar ${image}.jpg belum tercatat di public/images/blog/LISENSI.csv`);
    else if (!ALLOWED_LICENSES.includes(lic.lisensi)) fail(`lisensi gambar "${lic.lisensi}" tidak diizinkan (${ALLOWED_LICENSES.join(', ')})`);
    else if (lic.lisensi !== 'Karya sendiri' && !/^https:\/\//.test(lic.url ?? '')) fail('gambar berlisensi luar wajib mencantumkan url sumber (https)');
  }
  if (image_alt.length < 40 || image_alt.length > 160) fail(`image_alt ${image_alt.length} karakter, harus 40-160`);
  if (image_alt.trim().toLowerCase() === title.trim().toLowerCase()) fail('image_alt tidak boleh sama dengan judul');

  // Duplikasi topik
  for (const t of ctx.titles) if (similarity(title, t) > 0.6) fail(`judul terlalu mirip artikel yang ada: "${t}"`);

  return errors;
}

export function checkFile(file, { expect = null } = {}) {
  const result = { file, errors: [], posts: [] };
  let posts;
  try {
    posts = JSON.parse(readFileSync(file, 'utf8'));
  } catch (e) {
    result.errors.push(`JSON tidak valid: ${e.message}`);
    return result;
  }
  if (!Array.isArray(posts)) {
    result.errors.push('isi berkas harus berupa array artikel');
    return result;
  }
  if (expect && posts.length !== expect) result.errors.push(`berisi ${posts.length} artikel, seharusnya ${expect}`);

  const slugs = existingSlugs();
  const titles = existingTitles();
  const licenses = licenseRows();
  const seen = new Set();
  const batchSlugs = new Set(posts.map((p) => p.slug));

  for (const post of posts) {
    const slug = post.slug ?? '(tanpa slug)';
    const errs = [];
    if (seen.has(slug)) errs.push('slug ganda dalam berkas yang sama');
    seen.add(slug);
    // Artikel yang sudah diterbitkan dari berkas lain diperiksa lagi tanpa dihitung ganda.
    const ctx = { slugs: new Set([...slugs].filter((s) => s !== slug)), batchSlugs, titles: titles.filter((t) => t !== post.title), licenses };
    errs.push(...checkPost(post, ctx));
    // Judul antar artikel dalam satu batch juga tidak boleh nyaris sama.
    for (const other of posts) if (other !== post && other.title && post.title && similarity(post.title, other.title) > 0.6) errs.push(`judul terlalu mirip artikel lain di batch: "${other.title}"`);
    result.posts.push({ slug, errors: errs });
  }
  return result;
}

function report(results) {
  let bad = 0;
  for (const r of results) {
    console.log(`\n${r.file}`);
    for (const e of r.errors) { console.log(`  [BERKAS] ${e}`); bad++; }
    for (const p of r.posts) {
      if (p.errors.length === 0) console.log(`  OK    ${p.slug}`);
      else { bad += p.errors.length; console.log(`  GAGAL ${p.slug}`); p.errors.forEach((e) => console.log(`        - ${e}`)); }
    }
  }
  console.log(bad === 0 ? '\nSemua artikel lolos pemeriksaan.' : `\n${bad} pelanggaran. Artikel yang gagal TIDAK boleh diterbitkan.`);
  return bad;
}

function selftest() {
  const ctx = { slugs: new Set(), batchSlugs: new Set(), titles: [], licenses: {} };
  const p = (over = {}) => ({ title: 'Contoh', ...over });
  const cases = [
    ['judul tanpa angka ditolak', { title: 'Cara Membuat Sistem Informasi untuk Perusahaan Anda Hari Ini' }, /judul harus memuat angka/],
    ['kata toko online ditolak', { content: '<p>toko online</p>' }, /toko online/],
    ['kutipan hukum ditolak', { content: '<p>sesuai pasal 5</p>' }, /pasal/],
    ['klaim mutlak ditolak', { content: '<p>hasil pasti</p>' }, /pasti/],
    ['nama merek ditolak', { content: '<p>lewat whatsapp</p>' }, /whatsapp/],
    ['SARA ditolak', { content: '<p>soal agama</p>' }, /agama/],
  ];
  let bad = 0;
  for (const [name, over, expectRe] of cases) {
    const errs = checkPost({ title: 'Panduan 5 Langkah Memilih Sistem Informasi', seo_title: 'x', slug: 'a', category: 'seo', focus_keyword: 'sistem informasi', excerpt: 'x', seo_description: 'x', published_at: '2026-11-23 07:00:00', image: 'tidak-ada', image_alt: 'x', content: '<p>x</p>', ...over }, ctx);
    const ok = errs.some((e) => expectRe.test(e));
    console.log(`${ok ? 'OK  ' : 'GAGAL'} ${name}`);
    if (!ok) bad++;
  }
  process.exit(bad ? 1 : 0);
}

const args = process.argv.slice(2);
if (import.meta.url === `file://${process.argv[1].replace(/\\/g, '/').replace(/^([A-Za-z]):/, '/$1:')}` || process.argv[1]?.endsWith('cek-artikel.mjs')) {
  if (args.includes('--selftest')) selftest();
  const fileIdx = args.indexOf('--file');
  const expectIdx = args.indexOf('--expect');
  const expect = expectIdx >= 0 ? Number(args[expectIdx + 1]) : null;
  const files = fileIdx >= 0
    ? [resolve(args[fileIdx + 1])]
    : (existsSync(DATA_DIR) ? readdirSync(DATA_DIR).filter((f) => f.endsWith('.json')).map((f) => join(DATA_DIR, f)) : []);
  if (files.length === 0) { console.log('Tidak ada berkas artikel untuk diperiksa.'); process.exit(0); }
  process.exit(report(files.map((f) => checkFile(f, { expect }))) ? 1 : 0);
}
