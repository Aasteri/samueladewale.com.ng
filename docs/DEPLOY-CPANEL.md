# Deploying to cPanel (shared hosting)

This portfolio is a **Next.js 15** app. It is **not** a folder of static HTML you can drop into `public_html` and forget — unless you accept losing the contact form API (see [Plan B](#plan-b-static-only-no-nodejs) below).

**What must run on a server:**

| Feature | Needs Node? |
| ------- | ----------- |
| All pages (home, about, projects, …) | Yes (or static export at build time) |
| Contact form → `/api/contact` → SMTP | **Yes** |
| GitHub Activity page (refreshes every ~6h) | **Yes** (or stale snapshot at build time) |
| `next/image` optimization | Yes (or `unoptimized` in static export) |

---

## Step 0 — Check your host first

In cPanel, look for **“Setup Node.js App”** (sometimes **“Application Manager”** or **“Node.js Selector”**).

- **If you see it** → use [Plan A](#plan-a-nodejs-on-cpanel-recommended). This keeps the contact form and live GitHub dashboard working.
- **If you do not** → either upgrade hosting, use [Plan C](#plan-c-vercel--domain-on-cpanel) (free, easiest), or [Plan B](#plan-b-static-only-no-nodejs).

You need **Node.js 18.18+** (20 LTS is ideal). Check the version in the Node.js app screen before deploying.

---

## Plan A — Node.js on cPanel (recommended)

### 1. Build locally (on your PC)

```bash
cd /path/to/samueladewale.com
npm ci
npm run build
```

Fix any build errors before uploading.

### 2. What to upload

Upload the **whole project** to a folder **outside** `public_html`, e.g.:

```text
/home/YOUR_CPANEL_USER/samueladewale.com/
```

Include:

- `package.json`, `package-lock.json`
- `next.config.ts`, `tsconfig.json`, `tailwind.config.ts`, `postcss.config.mjs`
- `src/`
- `public/` (images, CVs, `img/`, etc.)
- `.next/` (the build output from `npm run build`)

**Do not upload:**

- `node_modules/` (install on the server instead — faster and smaller upload)
- `.env.local` (set secrets in cPanel — see below)
- `.git/` (optional)

You can zip the project, upload via **File Manager**, then **Extract**.

### 3. Create the Node.js application in cPanel

1. Open **Setup Node.js App** → **Create Application**.
2. **Node.js version:** 20.x (or highest available ≥ 18.18).
3. **Application mode:** `Production`.
4. **Application root:** `/home/YOUR_USER/samueladewale.com` (path to the folder above).
5. **Application URL:** your domain or subdomain (e.g. `samueladewale.com.ng`).
6. **Application startup file:** leave default or use npm — many hosts use:
   - **Startup command:** `npm start`  
   or  
   - **Script:** `node_modules/next/dist/bin/next` with args `start`

If the panel asks for a startup file only, create `server.js` in the project root:

```js
const { createServer } = require("http");
const { parse } = require("url");
const next = require("next");

const port = parseInt(process.env.PORT || "3000", 10);
const app = next({ dev: false });
const handle = app.getRequestHandler();

app.prepare().then(() => {
  createServer((req, res) => {
    const parsedUrl = parse(req.url, true);
    handle(req, res, parsedUrl);
  }).listen(port, (err) => {
    if (err) throw err;
    console.log(`> Ready on port ${port}`);
  });
});
```

Then set startup file to `server.js`.

7. Click **Run NPM Install** (or SSH: `cd ~/samueladewale.com && npm ci --omit=dev`).

8. If you did **not** upload `.next`, run **Run Script** / SSH: `npm run build`.

### 4. Environment variables (cPanel)

In the Node.js app → **Environment variables**, add (from `.env.example`):

| Variable | Example |
| -------- | ------- |
| `NODE_ENV` | `production` |
| `NEXT_PUBLIC_SITE_URL` | `https://samueladewale.com.ng` |
| `NEXT_PUBLIC_GITHUB_USERNAME` | `aasteri` |
| `GITHUB_TOKEN` | *(optional — improves GitHub dashboard)* |
| `CONTACT_EMAIL` | `hello@samueladewale.com.ng` |
| `SMTP_HOST` | `mail.samueladewale.com.ng` |
| `SMTP_PORT` | `465` |
| `SMTP_USER` | `hello@samueladewale.com.ng` |
| `SMTP_PASS` | your mailbox password |
| `SMTP_FROM` | `Samuel Adewale Portfolio <hello@samueladewale.com.ng>` |

**SMTP password:** On cPanel’s env UI you can usually paste the **raw** password (with `$` characters). You do **not** need backslash escaping like in local `.env.local`.

### 5. Start / restart the app

Use **Restart** in the Node.js app panel after every deploy or env change.

### 6. Domain

- If the app is mounted on the **main domain**, cPanel often configures Apache/Nginx → Node automatically.
- If the site still shows an old page, clear **public_html** default `index.html` or point the domain document root to the Node app URL per your host’s docs.

### 7. Smoke test after deploy

1. Home page loads, dark theme + cursor glow.
2. `/contact` — submit a test message; check `hello@samueladewale.com.ng` inbox.
3. `/github-activity` — metrics and heatmap load (not all zeros).
4. Download a CV from `/cvs`.
5. `https://samueladewale.com.ng/sitemap.xml` opens.

### 8. Updates later

```bash
# On your PC: pull changes, build
npm run build

# Upload changed files + fresh .next folder
# On server: Restart Node.js app
```

Or use **Git** + SSH if your host provides it: `git pull && npm ci --omit=dev && npm run build` then restart.

---

## Plan B — Static only (no Node.js)

Only use this if your host has **no** Node.js support.

1. Change `next.config.ts` to add `output: "export"` and `images: { unoptimized: true }`.
2. Remove or replace `/api/contact` (e.g. PHP mail script in `public_html`).
3. Run `npm run build` → upload the **`out/`** folder contents into `public_html`.

**Downsides:** Contact form won’t work as-is; GitHub Activity won’t refresh until you rebuild and re-upload; more manual work.

Ask if you want this path prepared in the repo.

---

## Plan C — Vercel + domain on cPanel (easiest ops)

1. Push the repo to **GitHub**.
2. Import on [vercel.com](https://vercel.com) (free tier).
3. Add the same env vars as in `.env.example`.
4. In cPanel **Zone Editor** / DNS, point the domain to Vercel (A/CNAME records Vercel shows you).

The site stays on Vercel; cPanel only hosts email (`mail.samueladewale.com.ng`) and DNS. No Node.js on shared hosting required.

Full steps are in the root `README.md` under **Deploying to Vercel**.

---

## Common problems

| Symptom | Likely cause |
| ------- | ------------- |
| Contact form always errors | Wrong `SMTP_PASS`, or `$` corrupted in env; port 465 blocked — try 587 + STARTTLS |
| 503 / blank after deploy | App not started, or `npm run build` missing on server |
| Old site still showing | Browser cache, or files still in `public_html` overriding Node |
| Images 404 | `public/img/` not uploaded |
| GitHub page all zeros | Temporary API limit; optional `GITHUB_TOKEN` helps |

---

## Quick decision

| Your situation | Do this |
| -------------- | ------- |
| cPanel has Node.js 18+ | **Plan A** |
| No Node.js, want minimum hassle | **Plan C** (Vercel) |
| No Node.js, must stay on cPanel files only | **Plan B** (we can adapt the project) |

If you tell me whether your cPanel shows **Setup Node.js App** and your domain (main domain vs subdomain), the exact clicks can be narrowed further.
