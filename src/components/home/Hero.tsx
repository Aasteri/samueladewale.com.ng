import Image from "next/image";
import Link from "next/link";
import { ArrowRight, ChevronDown, Download, MapPin } from "lucide-react";
import { Container } from "@/components/Container";
import { site } from "@/lib/site";
import { primaryCv } from "@/data/cvs";

export function Hero() {
  return (
    <section className="relative overflow-hidden">
      <div aria-hidden className="absolute inset-0 grid-bg opacity-60" />

      <Container className="relative grid gap-10 py-14 sm:gap-12 sm:py-24 lg:grid-cols-[1.15fr_0.85fr] lg:py-32">
        <div className="order-2 space-y-8 fade-in lg:order-1">
          <span className="pill">
            <span className="relative flex h-2 w-2">
              <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-brand opacity-75" />
              <span className="relative inline-flex h-2 w-2 rounded-full bg-cyan-brand" />
            </span>
            Available for senior & lead roles
          </span>

          <div>
            <p className="text-sm font-medium text-white/55">Hi, I&apos;m</p>
            <h1 className="mt-1 heading-1 text-balance">
              <span className="gradient-text">{site.name}</span>.
            </h1>
            <p className="mt-3 text-sm font-medium text-white/75 sm:text-base">
              {site.tagline}
            </p>
            <p className="mt-6 max-w-xl text-lg leading-relaxed text-white/70 text-pretty sm:text-xl">
              {site.headline}
            </p>
          </div>

          <div className="grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
            <Link href="/projects" className="btn-primary w-full sm:w-auto">
              View Projects
              <ArrowRight className="h-4 w-4" aria-hidden />
            </Link>
            <a
              href={primaryCv.pdf}
              className="btn-ghost w-full sm:w-auto"
              download={primaryCv.filename}
            >
              <Download className="h-4 w-4" aria-hidden />
              Download CV
            </a>
          </div>

          <ul className="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-medium text-white/45 sm:gap-x-5 sm:text-xs">
            {site.trustLine.map((t, i) => (
              <li key={t} className="flex items-center gap-2">
                <span>{t}</span>
                {i < site.trustLine.length - 1 && (
                  <span
                    aria-hidden
                    className="hidden h-1 w-1 rounded-full bg-white/20 sm:block"
                  />
                )}
              </li>
            ))}
          </ul>
        </div>

        <div
          className="order-1 relative fade-in lg:order-2"
          style={{ animationDelay: "100ms" }}
        >
          <HeroPanel />
        </div>
      </Container>

      <div className="pointer-events-none absolute inset-x-0 bottom-6 hidden justify-center sm:flex">
        <span className="inline-flex items-center gap-2 text-[11px] font-medium uppercase tracking-[0.22em] text-white/35">
          Scroll
          <ChevronDown className="h-3.5 w-3.5 animate-bounce" aria-hidden />
        </span>
      </div>
    </section>
  );
}

function HeroPanel() {
  const stackTags = [
    "Next.js",
    "React",
    "TypeScript",
    "Node.js",
    "Laravel",
    "Flutter",
    "React Native",
  ];

  return (
    <div className="relative mx-auto w-full max-w-md lg:max-w-none">
      <div className="media-frame aspect-[4/5] overflow-hidden rounded-3xl border border-white/10 bg-[#0a0e1a]">
        <Image
          src={site.portrait.primary}
          alt={`${site.name} — Senior Full-Stack Developer, Technical Lead, and Tech Consultant`}
          fill
          sizes="(max-width: 1024px) 90vw, 480px"
          className="object-cover object-center"
          priority
        />

        <div
          aria-hidden
          className="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#060912] via-[#060912]/30 to-transparent"
        />
        <div
          aria-hidden
          className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-cyan-brand/70 to-transparent"
        />

        <div className="absolute inset-x-3 top-3 flex flex-wrap items-center justify-between gap-2 sm:inset-x-4 sm:top-4">
          <span className="inline-flex shrink-0 items-center gap-1.5 rounded-full border border-white/15 bg-black/30 px-2.5 py-1 text-[11px] font-medium text-white/90 backdrop-blur-md">
            <MapPin className="h-3 w-3 text-cyan-brand" aria-hidden />
            Abuja, NG
          </span>
        </div>

        <div className="absolute inset-x-4 bottom-4 space-y-3 text-white">
          <div>
            <p className="text-sm font-semibold">{site.name}</p>
            <p className="text-[11px] uppercase tracking-[0.18em] text-cyan-brand">
              Senior Full-Stack · Lead · Consultant
            </p>
          </div>
          <ul className="flex flex-wrap gap-1.5">
            {stackTags.map((s) => (
              <li
                key={s}
                className="rounded-md border border-white/15 bg-white/[0.06] px-2 py-0.5 text-[10px] font-medium text-white/85 backdrop-blur-sm"
              >
                {s}
              </li>
            ))}
          </ul>
        </div>
      </div>
    </div>
  );
}
