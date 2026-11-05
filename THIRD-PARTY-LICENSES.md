# Third-Party Design/Behavior References

This file documents third-party assets whose *behavior, API shape, or visual
structure* informed a from-scratch reimplementation in this codebase, even
where no source code was copied. Kept for internal license due-diligence.

## confirm-dialog (Isaac Hernandez, 2023) — MIT License

- Source: `confirm-dialog` (confirm-dialog.js / confirm-dialog.css)
- Author: Isaac Hernandez
- Year: 2023
- License: MIT

Used as a behavioral/API reference for `resources/js/Components/ConfirmDialog.vue`
and `resources/js/composables/useConfirmDialog.js` — specifically the idea of a
promise-based `confirm()` replacing `window.confirm()`, with configurable
title/message/button labels. The implementation itself is an original Vue 3
Composition API component (reactive singleton state + Teleport-rendered modal,
focus trap, Escape-to-cancel); no code from the original vanilla-JS/CSS files
was copied. Visual styling (rounded corners, backdrop + centered modal,
pop-in animation) takes loose inspiration from the original CSS but is
implemented with this project's existing Tailwind design system.

MIT License text (reproduced for reference):

```
MIT License

Copyright (c) 2023 Isaac Hernandez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
```

## Self-hosted web fonts (public/fonts)

Raleway, Unbounded, JetBrains Mono and Instrument Serif (italic), latin-subset WOFF2 files as distributed
by fonts.bunny.net (mirroring Google Fonts). All are licensed under the
SIL Open Font License, Version 1.1 — https://openfontlicense.org

## Foto artikel blog dan layanan Software Custom (public/images/blog) — Pexels License

Foto berikut berasal dari Pexels (https://www.pexels.com/license/), bebas dipakai
secara komersial dan boleh dimodifikasi, tanpa wajib atribusi. Semua sudah diedit
(crop, overlay warna, logo ExodineWeb). Dipilih yang tanpa wajah dan tanpa merek
menonjol, karena lisensi melarang kesan endorsement. Jangan dijual ulang sebagai
stok foto. Infografik `*-infografik.webp` dibuat sendiri tanpa foto pihak ketiga.

| Gambar | Sumber |
| --- | --- |
| website-company-profile-perusahaan | https://www.pexels.com/photo/5230087/ |
| biaya-website-perusahaan | https://www.pexels.com/photo/33175667/ |
| keamanan-website-perusahaan | https://www.pexels.com/photo/36740854/ |
| kecepatan-website-bisnis | https://www.pexels.com/photo/18096282/ |
| seo-b2b-perusahaan | https://www.pexels.com/photo/577210/ |
| memilih-vendor-website | https://www.pexels.com/photo/261621/ |
| redesign-website-tanpa-kehilangan-trafik | https://www.pexels.com/photo/196645/ |
| website-multibahasa-perusahaan | https://www.pexels.com/photo/7634990/ |
| maintenance-website-perusahaan | https://www.pexels.com/photo/1921326/ |
| contoh-software-custom-perusahaan | https://www.pexels.com/photo/4508751/ |
| biaya-pembuatan-software-custom | https://www.pexels.com/photo/33175656/ |
| pembuatan-aplikasi-perusahaan | https://www.pexels.com/photo/196644/ |
| jasa-software-custom | https://www.pexels.com/photo/37730211/ |
