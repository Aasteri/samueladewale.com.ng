import Link from "next/link";
import { Github, Linkedin, Mail, MapPin } from "lucide-react";
import { site } from "@/lib/site";
import { Container } from "./Container";

export function Footer() {
  const year = new Date().getFullYear();

  return (
    <footer className="mt-16 border-t border-white/[0.06] bg-[#040710] text-white sm:mt-24">
      <Container className="py-10 sm:py-14">
        <div className="grid gap-10 sm:grid-cols-2 md:grid-cols-3">
          <div className="space-y-3">
            <p className="text-lg font-semibold">{site.name}</p>
            <p className="text-sm text-white/70">{site.tagline}</p>
            <p className="inline-flex items-center gap-2 text-sm text-white/60">
              <MapPin className="h-4 w-4" aria-hidden /> {site.location}
            </p>
          </div>

          <div>
            <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
              Quick links
            </p>
            <ul className="mt-4 space-y-2 text-sm text-white/75">
              {site.footerLinks.map((l) => (
                <li key={l.href}>
                  <Link
                    href={l.href}
                    className="transition-colors hover:text-white"
                  >
                    {l.label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div>
            <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
              Connect
            </p>
            <ul className="mt-4 space-y-2 text-sm text-white/75">
              <li>
                <a
                  href={`mailto:${site.email}`}
                  className="inline-flex max-w-full items-start gap-2 break-all transition-colors hover:text-white"
                >
                  <Mail className="mt-1 h-4 w-4 shrink-0" aria-hidden />
                  <span>{site.email}</span>
                </a>
              </li>
              <li>
                <a
                  href={site.linkedin}
                  target="_blank"
                  rel="noreferrer noopener"
                  className="inline-flex items-center gap-2 transition-colors hover:text-white"
                >
                  <Linkedin className="h-4 w-4" aria-hidden /> LinkedIn
                </a>
              </li>
              <li>
                <a
                  href={`https://github.com/${site.github}`}
                  target="_blank"
                  rel="noreferrer noopener"
                  className="inline-flex items-center gap-2 transition-colors hover:text-white"
                >
                  <Github className="h-4 w-4" aria-hidden /> GitHub
                </a>
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-12 flex flex-col items-start justify-between gap-3 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row sm:items-center">
          <p>© {year} Samuel Adewale. All rights reserved.</p>
          <p className="font-mono">Built with Next.js, Tailwind, and care.</p>
        </div>
      </Container>
    </footer>
  );
}
