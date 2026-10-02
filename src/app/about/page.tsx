import type { Metadata } from "next";
import Link from "next/link";
import Image from "next/image";
import {
  Award,
  Briefcase,
  Code2,
  HeartHandshake,
  MapPin,
  MessagesSquare,
  ShieldCheck,
  Sparkles,
  Target,
} from "lucide-react";
import { Container } from "@/components/Container";
import { PageHeader } from "@/components/PageHeader";
import { SectionHeading } from "@/components/SectionHeading";
import { Reveal } from "@/components/Reveal";
import { timeline, certifications } from "@/data/timeline";
import { site } from "@/lib/site";

export const metadata: Metadata = {
  title: "About",
  description:
    "Samuel Adewale is a software developer and technical consultant based in Abuja, Nigeria, with experience delivering enterprise, government, healthtech, ERP, and digital transformation platforms.",
  alternates: { canonical: "/about" },
};

const differentiators = [
  {
    icon: Code2,
    text: "I can code hands-on.",
  },
  {
    icon: Briefcase,
    text: "I can lead developers.",
  },
  {
    icon: MessagesSquare,
    text: "I can speak with clients and stakeholders.",
  },
  {
    icon: Target,
    text: "I understand business requirements.",
  },
  {
    icon: Sparkles,
    text: "I can turn rough ideas into working systems.",
  },
  {
    icon: ShieldCheck,
    text: "I have experience with sensitive enterprise and public-sector platforms.",
  },
];

export default function AboutPage() {
  return (
    <>
      <PageHeader
        eyebrow="About"
        title="Hands-on senior engineer. Calm under pressure. Built for serious platforms."
        description="I'm Samuel — a software developer and technical consultant based in Abuja, Nigeria. I help organizations turn complex operational needs into scalable software products."
        actions={
          <>
            <Link href="/projects" className="btn-primary w-full sm:w-auto">
              View My Projects
            </Link>
            <Link href="/contact" className="btn-ghost w-full sm:w-auto">
              Contact Me
            </Link>
          </>
        }
      />

      <section className="section">
        <Container>
          <div className="grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
            <Reveal>
              <div className="space-y-6">
                <div className="media-frame mx-auto aspect-[4/5] w-full max-w-sm overflow-hidden rounded-3xl border border-white/10 bg-[#0a0e1a]">
                  <Image
                    src={site.portrait.secondary}
                    alt={`Portrait of ${site.name} — Senior Full-Stack Developer, Technical Lead, and Tech Consultant`}
                    fill
                    sizes="(max-width: 1024px) 80vw, 360px"
                    className="object-cover object-center"
                    priority
                  />
                  <div
                    aria-hidden
                    className="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-[#060912]/90 to-transparent"
                  />
                  <div className="absolute inset-x-4 bottom-4 flex items-end justify-between text-white">
                    <div>
                      <p className="text-sm font-semibold">{site.name}</p>
                      <p className="text-xs text-white/70">{site.tagline}</p>
                    </div>
                    <span className="inline-flex items-center gap-1 rounded-full border border-white/15 bg-white/5 px-2.5 py-1 text-[11px] font-medium text-white/80">
                      <MapPin className="h-3 w-3" aria-hidden />
                      Abuja, NG
                    </span>
                  </div>
                </div>

                <div className="surface-strong relative overflow-hidden p-6 sm:p-8">
                  <div
                    aria-hidden
                    className="pointer-events-none absolute -top-20 right-[-10%] h-64 w-64 rounded-full bg-cyan-brand/[0.08] blur-3xl"
                  />
                  <p className="eyebrow">
                    What makes me different
                  </p>
                  <ul className="mt-4 space-y-3">
                    {differentiators.map((d) => (
                      <li key={d.text} className="flex items-start gap-3">
                        <span className="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white/5 text-cyan-brand">
                          <d.icon className="h-4 w-4" aria-hidden />
                        </span>
                        <span className="text-sm leading-relaxed text-white/85">
                          {d.text}
                        </span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </Reveal>

            <Reveal delay={0.1}>
              <div className="surface p-6 sm:p-8">
                <p className="eyebrow">Who I am</p>
                <h2 className="mt-2 heading-2">
                  Senior full-stack developer, technical lead, and tech
                  consultant.
                </h2>
                <p className="mt-4 leading-relaxed text-white/70">
                  I have 7+ years of experience delivering enterprise,
                  government, healthtech, compliance, ERP, and digital
                  transformation platforms. I work across the full stack —
                  Next.js, React, TypeScript, Node.js, Laravel — and ship
                  cross-platform mobile apps in Flutter and React Native
                  alongside them. My remit covers the full delivery
                  lifecycle, from requirements and architecture to
                  deployment and support.
                </p>
                <p className="mt-4 leading-relaxed text-white/70">
                  My favourite engagements involve high-stakes operational
                  needs: regulators that need workflow tooling, enterprises
                  that need ERP-style modules, healthtech founders that need
                  an MVP yesterday, and clients who need an engineer who can
                  also talk to their stakeholders.
                </p>
                <p className="mt-4 leading-relaxed text-white/70">
                  I&apos;ve shipped public-facing platforms for the Federal
                  Ministry of Labour &amp; Employment (NELEX, LMIS), the
                  Federal Character Commission, NNPC, NSITF, and
                  supported-care services in the UK — alongside Flutter and
                  React Native mobile apps, corporate sites, and MVPs for
                  private-sector clients.
                </p>
              </div>
            </Reveal>
          </div>
        </Container>
      </section>

      <section className="section">
        <Container>
          <SectionHeading
            eyebrow="Career timeline"
            title="Where I've delivered, led, and learned."
            description="A vertical timeline of the teams and businesses I've helped grow."
          />

          <ol className="relative mt-12 space-y-4 before:absolute before:left-4 before:top-2 before:h-[calc(100%-1rem)] before:w-px before:bg-white/[0.08] sm:before:left-5">
            {timeline.map((item, i) => (
              <Reveal as="li" key={item.company} delay={i * 0.05}>
                <div className="relative pl-12 sm:pl-14">
                  <span
                    aria-hidden
                    className="absolute left-0 top-1 grid h-9 w-9 place-items-center rounded-full border border-white/15 bg-[#0a0e1a] text-cyan-brand sm:h-10 sm:w-10"
                  >
                    <Briefcase className="h-4 w-4" aria-hidden />
                  </span>
                  <div className="surface p-6">
                    <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
                      {item.period}
                    </p>
                    <h3 className="mt-1 text-lg font-semibold text-white">
                      {item.company}
                    </h3>
                    <p className="text-sm font-medium text-white/70">
                      {item.role}
                    </p>
                    <p className="mt-3 text-sm leading-relaxed text-white/60">
                      {item.description}
                    </p>
                    <ul className="mt-3 grid gap-1.5 text-sm text-white/75 sm:grid-cols-2">
                      {item.highlights.map((h) => (
                        <li
                          key={h}
                          className="flex items-start gap-2 before:mt-2 before:h-1 before:w-1 before:shrink-0 before:rounded-full before:bg-cyan-brand"
                        >
                          <span>{h}</span>
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              </Reveal>
            ))}
          </ol>
        </Container>
      </section>

      <section className="section">
        <Container>
          <SectionHeading
            eyebrow="Certifications"
            title="Recognised certifications and continuous learning."
          />

          <ul className="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {certifications.map((c, i) => (
              <Reveal as="li" key={c} delay={i * 0.04}>
                <div className="surface flex items-start gap-3 p-5">
                  <span className="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand">
                    <Award className="h-4 w-4" aria-hidden />
                  </span>
                  <p className="text-sm font-medium text-white">{c}</p>
                </div>
              </Reveal>
            ))}
          </ul>

          <div className="mt-10 surface p-6 sm:mt-12 sm:p-8">
            <div className="flex flex-col items-start gap-4 sm:flex-row">
              <span className="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-white/10 bg-cyan-brand/[0.08] text-cyan-brand">
                <HeartHandshake className="h-5 w-5" aria-hidden />
              </span>
              <div className="min-w-0 flex-1">
                <h3 className="heading-3">Want to work together?</h3>
                <p className="mt-2 max-w-2xl text-white/65">
                  I&apos;m open to senior engineering roles, technical leadership
                  positions, consulting engagements, and partnerships.
                </p>
                <div className="mt-5 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                  <Link href="/contact" className="btn-primary w-full sm:w-auto">
                    Contact Me
                  </Link>
                  <Link href="/cvs" className="btn-ghost w-full sm:w-auto">
                    Download CV
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </Container>
      </section>
    </>
  );
}
