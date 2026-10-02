# samueladewale.com

Portfolio of **Samuel Adewale** — Senior Full-Stack Developer, Technical Lead &
Tech Consultant.

Built with **Next.js 15 (App Router)**, **TypeScript**, **Tailwind CSS**,
**Framer Motion** (used lightly), **Recharts**, and **Lucide React**. Hosted on
**Vercel**.

## Quick start

```bash
# 1. Install dependencies
npm install

# 2. Create your env file
cp .env.example .env.local
# Fill in NEXT_PUBLIC_SITE_URL, NEXT_PUBLIC_GITHUB_USERNAME, GITHUB_TOKEN

# 3. Run the dev server
npm run dev
```

Open [http://localhost:3000](http://localhost:3000).

## Project structure

```
src/
├─ app/                       # App Router routes
│  ├─ layout.tsx              # Root layout, metadata, nav, footer
│  ├─ page.tsx                # Home (hero → impact → projects → strengths → github → timeline → CV → contact)
│  ├─ about/                  # About page
│  ├─ projects/               # Projects index + dynamic [slug] case studies
│  ├─ github-activity/        # Developer Activity Dashboard
│  ├─ services/               # Services page
│  ├─ cvs/                    # CV and downloads page
│  ├─ contact/                # Contact page
│  ├─ api/
│  │  ├─ contact/route.ts     # Contact form endpoint
│  │  └─ github/route.ts      # GitHub activity JSON endpoint (cached)
│  ├─ opengraph-image.tsx     # Default OG image
│  ├─ icon.tsx                # Favicon
│  ├─ sitemap.ts              # Sitemap.xml
│  ├─ robots.ts               # robots.txt
│  ├─ not-found.tsx           # Custom 404
│  └─ globals.css             # Tailwind + design tokens
├─ components/
│  ├─ Navbar.tsx              # Sticky nav with mobile hamburger
│  ├─ Footer.tsx
│  ├─ Container.tsx           # Layout container helpers
│  ├─ PageHeader.tsx
│  ├─ SectionHeading.tsx
│  ├─ ProjectCard.tsx
│  ├─ ContactForm.tsx
│  ├─ Reveal.tsx              # Framer Motion fade-up wrapper
│  ├─ home/                   # Homepage section components
│  └─ github/                 # Recharts visualisations + heatmap
├─ data/
│  ├─ projects.ts             # Case studies (problem/solution/features/stack/impact)
│  ├─ services.ts             # 5 service blocks
│  ├─ timeline.ts             # Career timeline + certifications
│  ├─ impact.ts               # Impact cards + technical strengths
│  └─ cvs.ts                  # Downloadable CV/portfolio docs
└─ lib/
   ├─ site.ts                 # Site config (name, navigation, contact, headlines)
   └─ github.ts               # GitHub GraphQL fetcher + cache + fallback
```

## Brand system

| Token       | Hex       | Use                            |
| ----------- | --------- | ------------------------------ |
| Primary     | `#0B1220` | Deep navy — surfaces, headings |
| Secondary   | `#1E293B` | Slate blue                     |
| Accent      | `#06B6D4` | Electric cyan — CTAs, links    |
| Impact      | `#10B981` | Emerald — metrics, success     |
| Background  | `#F8FAFC` | Soft off-white                 |
| Text        | `#111827` | Charcoal                       |
| Muted text  | `#64748B` | Gray                           |

Set in `tailwind.config.ts` and exposed via utility classes
(`bg-navy`, `text-cyan-brand`, `text-emerald-impact`, `bg-canvas`, etc.) and
component classes (`.btn-primary`, `.btn-accent`, `.surface`, `.heading-1`).

## Pages

1. **Home** — Hero · Impact metrics · Featured projects · Technical strengths ·
   GitHub activity preview · Career timeline preview · Download CV section ·
   Contact CTA.
2. **About** — Who I am · What makes me different · Career timeline ·
   Certifications.
3. **Projects** — Grouped by category (Enterprise & Government, Healthtech &
   Records, Corporate & Commercial). Each card links to a full case study at
   `/projects/[slug]` with problem, solution, features, stack, impact, and a
   confidentiality note where applicable.
4. **GitHub Activity** — Developer Activity Dashboard with:
   - 8 top metric cards
   - Year-long contribution calendar heatmap
   - Monthly contributions area chart
   - Top languages bar chart
   - Recent repository activity chart
   - Recent repositories list
   - Recruiter-friendly explanation including the "private/NDA work" note
5. **Services** — 5 service blocks + engagement model card.
6. **CVs** — 5 downloadable CV/portfolio variants, each with a "best for" hint.
7. **Contact** — Form posting to `/api/contact`, plus email/phone/LinkedIn/
   GitHub/location/availability cards.

## GitHub integration

The Developer Activity Dashboard pulls data from the **GitHub GraphQL API**
because that endpoint exposes the
[`ContributionsCollection`](https://docs.github.com/en/graphql/reference/objects#contributionscollection)
object (commits, issues, pull requests, contribution calendar).

- The token is read from `GITHUB_TOKEN` **on the server only** — never sent to
  the client.
- Responses are cached with `next.revalidate = 21600` (6 hours) to respect
  [GitHub's REST and GraphQL rate limits](https://docs.github.com/en/rest/rate-limit/rate-limit).
- If the token is missing or the API fails, the page renders a graceful
  fallback shell with a helpful message instead of crashing.

You can also call the cached JSON directly: `GET /api/github`.

## SEO

- `metadata` API in every route (title template, description, canonical, OG).
- `src/app/sitemap.ts` and `src/app/robots.ts` use Next's file conventions.
- `src/app/opengraph-image.tsx` generates a dynamic 1200×630 OG image.
- `Person` JSON-LD is injected globally in `layout.tsx`.
- Fully responsive, accessible color contrast, and clear semantic headings.

## Scripts

| Script          | What it does                       |
| --------------- | ---------------------------------- |
| `npm run dev`   | Start the dev server               |
| `npm run build` | Production build                   |
| `npm run start` | Run the production server          |
| `npm run lint`  | Lint with `next lint`              |
| `npm run typecheck` | TypeScript type check          |

## Deploying to cPanel (shared hosting)

This app needs **Node.js** for the contact form and GitHub API routes. See
**[docs/DEPLOY-CPANEL.md](docs/DEPLOY-CPANEL.md)** for Plan A (Node on cPanel),
Plan B (static export), and Plan C (Vercel + DNS on cPanel).

## Deploying to Vercel

1. Push this repo to GitHub.
2. Import it on [vercel.com](https://vercel.com/new) — Vercel auto-detects
   Next.js.
3. Add environment variables (copy the keys from `.env.example`):
   - `NEXT_PUBLIC_SITE_URL=https://samueladewale.com.ng`
   - `NEXT_PUBLIC_GITHUB_USERNAME=aasteri`
   - `GITHUB_TOKEN=...` *(optional — improves accuracy of the
     Developer Activity Dashboard)*
   - `CONTACT_EMAIL=hello@samueladewale.com.ng`
   - `SMTP_HOST=mail.samueladewale.com.ng`
   - `SMTP_PORT=465`
   - `SMTP_USER=hello@samueladewale.com.ng`
   - `SMTP_PASS=...` *(the mailbox password)*
     - **Local `.env.local` only:** escape every `$` with a backslash
       (e.g. `Lc5lMQ=%r\$ZAk{f]`). Next.js's env loader expands
       `$NAME` references, which silently corrupts the password.
     - On Vercel's env panel paste the raw password — that UI does not
       perform expansion.
   - `SMTP_FROM="Samuel Adewale Portfolio <hello@samueladewale.com.ng>"`
4. Add the custom domain `samueladewale.com.ng` from the Vercel project
   "Domains" tab and configure the DNS records with your domain registrar as
   Vercel instructs.

### Contact form (cPanel SMTP)

The contact form posts to `/api/contact`, which sends a styled HTML email
via your cPanel mailbox using **nodemailer**.

- Host: `mail.samueladewale.com.ng`, port `465`, SSL/TLS (`secure: true`).
- Authenticates as `hello@samueladewale.com.ng`.
- `Reply-To` is set to the sender's address, so hitting *Reply* in your
  mail client replies straight to the person who filled in the form.
- A hidden honeypot field (`website`) drops obvious bot submissions.
- Best-effort per-process rate limit (4 sends / minute / IP) returns a 429
  if hammered.
- If `SMTP_*` env vars are unset (e.g. local dev), the route logs the
  payload and returns success — so the UI works without secrets.

## Adding CVs

Drop your PDFs into `public/cvs/` using the filenames listed in
`public/cvs/README.md`. Update `src/data/cvs.ts` if you want to rename a card
or add a DOCX version.
# samueladewale.com.ng
