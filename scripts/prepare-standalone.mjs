/**
 * After `next build` with output: "standalone", copy assets the standalone
 * server expects next to the traced output folder.
 */
import { cpSync, existsSync, mkdirSync } from "fs";
import { join } from "path";

const root = process.cwd();
const standaloneDir = join(root, ".next", "standalone");

if (!existsSync(standaloneDir)) {
  console.error(
    "[prepare-standalone] Missing .next/standalone — run `npm run build` first."
  );
  process.exit(1);
}

const publicSrc = join(root, "public");
const publicDest = join(standaloneDir, "public");
if (existsSync(publicSrc)) {
  cpSync(publicSrc, publicDest, { recursive: true });
  console.log("[prepare-standalone] Copied public/ → .next/standalone/public/");
}

const staticSrc = join(root, ".next", "static");
const staticDest = join(standaloneDir, ".next", "static");
if (existsSync(staticSrc)) {
  mkdirSync(join(standaloneDir, ".next"), { recursive: true });
  cpSync(staticSrc, staticDest, { recursive: true });
  console.log(
    "[prepare-standalone] Copied .next/static → .next/standalone/.next/static/"
  );
}

console.log("[prepare-standalone] Done.");
