// One-shot image compression for the portfolio.
//
// Strategy:
//   • Portraits (hero/about/contact)  → max 1400px wide, JPEG q82  → ~120–200KB
//   • Project screenshots             → max 1400px wide, JPEG q78  → ~150–300KB
//   • Skip files already under MIN_BYTES so we don't bloat tiny assets.
//   • Skip files we've already processed (marker side-file *.optimized.json).
//
// Originals are kept untouched as *.orig.<ext> so nothing is lost.
// Re-runs are safe (idempotent) thanks to the marker.

import { readdir, stat, readFile, writeFile, rename, mkdir } from "node:fs/promises";
import { existsSync } from "node:fs";
import path from "node:path";
import sharp from "sharp";

const ROOT = path.resolve("public/img");
const PORTRAITS_DIR = path.resolve("public/img");
const PROJECTS_DIR = path.resolve("public/img/portfolio");

const MIN_BYTES = 200 * 1024; // < 200KB → leave alone

function isImage(file) {
  return /\.(png|jpe?g)$/i.test(file);
}

function isBackup(file) {
  return /\.orig\.(png|jpe?g)$/i.test(file) || /\.optimized\.json$/i.test(file);
}

async function listImages(dir) {
  if (!existsSync(dir)) return [];
  const entries = await readdir(dir);
  return entries
    .filter((f) => isImage(f) && !isBackup(f))
    .map((f) => path.join(dir, f));
}

async function compressOne(file, { maxWidth, quality }) {
  const markerPath = file + ".optimized.json";
  if (existsSync(markerPath)) return { file, skipped: "already-optimized" };

  const info = await stat(file);
  if (info.size < MIN_BYTES) {
    await writeFile(
      markerPath,
      JSON.stringify({ original: info.size, kept: true, reason: "small" }, null, 2)
    );
    return { file, skipped: `small (${(info.size / 1024).toFixed(0)}KB)` };
  }

  const ext = path.extname(file);
  const dir = path.dirname(file);
  const base = path.basename(file, ext);
  const backup = path.join(dir, `${base}.orig${ext}`);

  const buf = await readFile(file);
  const meta = await sharp(buf).metadata();

  let pipeline = sharp(buf, { failOn: "none" });
  if (meta.width && meta.width > maxWidth) {
    pipeline = pipeline.resize({ width: maxWidth, withoutEnlargement: true });
  }

  // For .png files that are actually screenshots (most are), JPEG saves much
  // more than PNG re-encoding. We keep the .png filename so existing references
  // continue to work but write JPEG bytes — browsers detect by magic bytes.
  // For PNGs with transparency this would be wrong, but our screenshots are
  // all opaque page captures.
  const out = await pipeline
    .flatten({ background: "#ffffff" })
    .jpeg({ quality, mozjpeg: true, progressive: true })
    .toBuffer();

  if (out.length >= info.size * 0.95) {
    await writeFile(
      markerPath,
      JSON.stringify(
        {
          original: info.size,
          attempted: out.length,
          kept: true,
          reason: "no-gain",
        },
        null,
        2
      )
    );
    return { file, skipped: `no-gain (${(out.length / 1024).toFixed(0)}KB)` };
  }

  if (!existsSync(backup)) await rename(file, backup);
  await writeFile(file, out);
  await writeFile(
    markerPath,
    JSON.stringify(
      {
        original: info.size,
        optimized: out.length,
        savingsBytes: info.size - out.length,
        savingsPct: +(((info.size - out.length) / info.size) * 100).toFixed(1),
        width: meta.width,
        maxWidth,
        quality,
      },
      null,
      2
    )
  );
  return {
    file,
    from: info.size,
    to: out.length,
    saved: info.size - out.length,
  };
}

async function run() {
  if (!existsSync(ROOT)) {
    console.error("public/img does not exist");
    process.exit(1);
  }

  const portraits = (await listImages(PORTRAITS_DIR)).filter(
    (f) => !f.includes(`${path.sep}portfolio${path.sep}`)
  );
  const projects = await listImages(PROJECTS_DIR);

  let totalBefore = 0;
  let totalAfter = 0;
  const rows = [];

  for (const f of portraits) {
    const r = await compressOne(f, { maxWidth: 1400, quality: 82 });
    rows.push(r);
    if (r.from) {
      totalBefore += r.from;
      totalAfter += r.to;
    }
  }
  for (const f of projects) {
    const r = await compressOne(f, { maxWidth: 1400, quality: 78 });
    rows.push(r);
    if (r.from) {
      totalBefore += r.from;
      totalAfter += r.to;
    }
  }

  console.log("\nImage optimization report");
  console.log("─".repeat(70));
  for (const r of rows) {
    if (r.skipped) {
      console.log(`  skip   ${path.basename(r.file).padEnd(46)} (${r.skipped})`);
    } else {
      const fromKB = (r.from / 1024).toFixed(0).padStart(5);
      const toKB = (r.to / 1024).toFixed(0).padStart(5);
      const pct = (((r.from - r.to) / r.from) * 100).toFixed(0).padStart(3);
      console.log(
        `  opt    ${path.basename(r.file).padEnd(46)} ${fromKB}KB → ${toKB}KB  (-${pct}%)`
      );
    }
  }
  console.log("─".repeat(70));
  if (totalBefore > 0) {
    const savedMB = ((totalBefore - totalAfter) / (1024 * 1024)).toFixed(2);
    const beforeMB = (totalBefore / (1024 * 1024)).toFixed(2);
    const afterMB = (totalAfter / (1024 * 1024)).toFixed(2);
    console.log(
      `  TOTAL  ${beforeMB} MB → ${afterMB} MB  (saved ${savedMB} MB)`
    );
  } else {
    console.log("  Nothing to optimize.");
  }
}

run().catch((e) => {
  console.error(e);
  process.exit(1);
});
