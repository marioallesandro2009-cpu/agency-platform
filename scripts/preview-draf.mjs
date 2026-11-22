#!/usr/bin/env node
// Membuat SATU halaman HTML mandiri (gambar tertanam) untuk meninjau draf artikel di HP.
//   node scripts/preview-draf.mjs database/seeders/data/blog-mingguan/2026-11-23.json /tmp/pratinjau.html
// Hanya Node bawaan. Hasilnya aman diterbitkan sebagai Artifact: tanpa skrip, tanpa sumber luar.
import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { join, dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { checkFile } from './cek-artikel.mjs';

const ROOT = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const [, , input, output = '/tmp/pratinjau.html'] = process.argv;
if (!input) { console.error('Pakai: node scripts/preview-draf.mjs <berkas.json> [keluaran.html]'); process.exit(2); }

const esc = (s) => String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
// Isi artikel sudah HTML; buang apa pun yang bisa dijalankan sebelum ditampilkan.
const safe = (html) => html.replace(/<script[\s\S]*?<\/script>/gi, '').replace(/\son\w+="[^"]*"/gi, '').replace(/javascript:/gi, '');
const words = (html) => (html.replace(/<[^>]+>/g, ' ').match(/\S+/g) ?? []).length;
const dataUri = (slug) => {
  const f = join(ROOT, 'public/images/blog', `${slug}.jpg`);
  return existsSync(f) ? `data:image/jpeg;base64,${readFileSync(f).toString('base64')}` : null;
};

const posts = JSON.parse(readFileSync(resolve(input), 'utf8'));
const check = checkFile(resolve(input));
const status = Object.fromEntries(check.posts.map((p) => [p.slug, p.errors]));
const date = (posts[0]?.published_at ?? '').slice(0, 10);
const okCount = posts.filter((p) => (status[p.slug] ?? ['?']).length === 0).length;

const lenTag = (label, value, min, max) => {
  const n = String(value ?? '').length;
  return `<span class="chip ${n >= min && n <= max ? 'ok' : 'bad'}">${label} ${n}</span>`;
};

const cards = posts.map((p, i) => {
  const errs = status[p.slug] ?? ['tidak terperiksa'];
  const img = dataUri(p.image);
  const sources = [...(p.content ?? '').matchAll(/<a\s+[^>]*href="(https?:\/\/[^"]+)"[^>]*>([\s\S]*?)<\/a>/g)].map((m) => `<li>${esc(m[2].replace(/<[^>]+>/g, ''))} <small>${esc(m[1])}</small></li>`).join('');
  return `<article id="a${i + 1}">
  <p class="eyebrow">Artikel ${i + 1} dari ${posts.length} &middot; ${esc(p.category)} &middot; ${words(p.content ?? '')} kata</p>
  <h2>${esc(p.title)}</h2>
  <p class="status ${errs.length ? 'bad' : 'ok'}">${errs.length ? `Pemeriksa menemukan ${errs.length} pelanggaran` : 'Lolos pemeriksa otomatis'}</p>
  ${errs.length ? `<ul class="errs">${errs.map((e) => `<li>${esc(e)}</li>`).join('')}</ul>` : ''}
  ${img ? `<figure><img src="${img}" alt="${esc(p.image_alt)}"><figcaption>${esc(p.image_alt)}</figcaption></figure>` : '<p class="status bad">Gambar tidak ditemukan</p>'}
  <div class="meta">
    <p><strong>Kata kunci:</strong> ${esc(p.focus_keyword)}</p>
    <p><strong>Judul SEO:</strong> ${esc(p.seo_title)} ${lenTag('', p.seo_title, 1, 62)}</p>
    <p><strong>Deskripsi SEO:</strong> ${esc(p.seo_description)} ${lenTag('', p.seo_description, 120, 160)}</p>
    <p><strong>Ringkasan:</strong> ${esc(p.excerpt)}</p>
    <p><strong>Alamat:</strong> /blog/${esc(p.slug)} &middot; terbit ${esc(p.published_at)}</p>
  </div>
  <div class="body">${safe(p.content ?? '')}</div>
  ${sources ? `<details><summary>Sumber di artikel ini</summary><ul>${sources}</ul></details>` : ''}
</article>`;
}).join('\n');

const toc = posts.map((p, i) => `<li><a href="#a${i + 1}">${esc(p.title)}</a> <span class="chip ${(status[p.slug] ?? ['?']).length ? 'bad' : 'ok'}">${(status[p.slug] ?? ['?']).length ? 'perlu perbaikan' : 'lolos'}</span></li>`).join('');

const html = `<title>Pratinjau draf blog ${esc(date)}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root { --bg:#f7f6f2; --card:#ffffff; --fg:#1d1c1a; --muted:#5c5a55; --line:#e4e1d9; --ok:#1d7a46; --okbg:#e3f3ea; --bad:#b3261e; --badbg:#fbe9e7; --accent:#1f4fd8; color-scheme: light; }
@media (prefers-color-scheme: dark) { :root:not([data-theme="light"]) { --bg:#161513; --card:#1f1e1b; --fg:#ece9e2; --muted:#a8a59d; --line:#34322d; --ok:#6fcf97; --okbg:#173a28; --bad:#ff8a80; --badbg:#3d1f1c; --accent:#8fb0ff; color-scheme: dark; } }
:root[data-theme="dark"] { --bg:#161513; --card:#1f1e1b; --fg:#ece9e2; --muted:#a8a59d; --line:#34322d; --ok:#6fcf97; --okbg:#173a28; --bad:#ff8a80; --badbg:#3d1f1c; --accent:#8fb0ff; color-scheme: dark; }
* { box-sizing: border-box; }
body { margin:0; background:var(--bg); color:var(--fg); font:16px/1.65 system-ui,-apple-system,"Segoe UI",sans-serif; }
main { max-width: 760px; margin: 0 auto; padding: 24px 16px 64px; }
h1 { font-size: 1.5rem; line-height: 1.25; margin: 0 0 6px; }
.sub { color: var(--muted); margin: 0 0 20px; }
.toc { background: var(--card); border: 1px solid var(--line); border-radius: 12px; padding: 14px 18px; margin: 0 0 28px; }
.toc ol { margin: 8px 0 0; padding-left: 20px; } .toc li { margin: 8px 0; }
a { color: var(--accent); }
article { background: var(--card); border: 1px solid var(--line); border-radius: 14px; padding: 20px 18px; margin: 0 0 28px; overflow-wrap: anywhere; }
article h2 { font-size: 1.35rem; line-height: 1.3; margin: 4px 0 12px; }
.eyebrow { color: var(--muted); font-size: .8rem; letter-spacing: .04em; text-transform: uppercase; margin: 0; }
.status { display:inline-block; font-size:.85rem; font-weight:600; padding: 4px 10px; border-radius: 999px; margin: 0 0 12px; }
.status.ok, .chip.ok { color: var(--ok); background: var(--okbg); } .status.bad, .chip.bad { color: var(--bad); background: var(--badbg); }
.chip { font-size: .75rem; font-weight: 600; padding: 2px 8px; border-radius: 999px; white-space: nowrap; }
.errs { color: var(--bad); font-size: .9rem; margin: 0 0 14px; padding-left: 20px; }
figure { margin: 0 0 16px; } figure img { width: 100%; height: auto; border-radius: 10px; display: block; } figcaption { color: var(--muted); font-size: .8rem; margin-top: 6px; }
.meta { background: var(--bg); border-radius: 10px; padding: 10px 14px; margin: 0 0 18px; font-size: .88rem; } .meta p { margin: 6px 0; }
.body h2 { font-size: 1.2rem; margin: 1.6em 0 .5em; } .body h3 { font-size: 1.05rem; margin: 1.3em 0 .4em; } .body p, .body li { margin: .7em 0; }
details { margin-top: 18px; font-size: .88rem; } summary { cursor: pointer; font-weight: 600; } details li { margin: 8px 0; } small { color: var(--muted); word-break: break-all; }
.foot { color: var(--muted); font-size: .85rem; text-align: center; margin-top: 8px; }
</style>
<main>
  <h1>Pratinjau draf blog ${esc(date)}</h1>
  <p class="sub">${posts.length} artikel &middot; ${okCount} lolos pemeriksa otomatis. Ini draf: belum tayang di situs.</p>
  <nav class="toc"><strong>Isi</strong><ol>${toc}</ol></nav>
  ${cards}
  <p class="foot">Dibuat otomatis dari berkas JSON draf. Untuk menerbitkan, jalankan routine "Terbitkan draf blog terbaru".</p>
</main>
`;

writeFileSync(resolve(output), html);
console.log(`Pratinjau ditulis ke ${output} (${(Buffer.byteLength(html) / 1024).toFixed(0)} KB, ${posts.length} artikel, ${okCount} lolos).`);
