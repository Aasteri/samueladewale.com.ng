import type { Metadata } from "next";
import Link from "next/link";
import { CheckCircle2 } from "lucide-react";
import { Container } from "@/components/Container";
import { PageHeader } from "@/components/PageHeader";
import { Reveal } from "@/components/Reveal";
import { SpotlightCard } from "@/components/SpotlightCard";
import { services } from "@/data/services";

export const metadata: Metadata = {
  title: "Services",
  description:
    "Full-stack development, technical leadership, enterprise and government platforms, MVP delivery, and technical consulting.",
  alternates: { canonical: "/services" },
};

export default function ServicesPage() {
  return (
    <>
      <PageHeader
        eyebrow="Services"
        title="How I can help your team ship serious software."
        description="Five focused services covering hands-on delivery, leadership, and consulting — for teams that need outcomes, not just code."
        actions={
          <>
            <Link href="/contact" className="btn-primary w-full sm:w-auto">
              Hire Me
            </Link>
            <Link href="/projects" className="btn-ghost w-full sm:w-auto">
              View Case Studies
            </Link>
          </>
        }
      />

      <Container className="py-12">
        <ul className="grid gap-6 md:grid-cols-2">
          {services.map((service, i) => (
            <Reveal as="li" key={service.title} delay={i * 0.05}>
              <SpotlightCard
                as="article"
                className="surface card-hover h-full rounded-2xl p-7"
              >
                <div className="grid h-12 w-12 place-items-center rounded-xl border border-white/10 bg-white/[0.04] text-cyan-brand">
                  <service.icon className="h-6 w-6" aria-hidden />
                </div>
                <h2 className="mt-5 heading-3">{service.title}</h2>
                <p className="mt-2 leading-relaxed text-white/65">
                  {service.summary}
                </p>
                <ul className="mt-5 space-y-2">
                  {service.bullets.map((b) => (
                    <li
                      key={b}
                      className="flex items-start gap-2 text-sm text-white/80"
                    >
                      <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-cyan-brand" />
                      <span>{b}</span>
                    </li>
                  ))}
                </ul>
              </SpotlightCard>
            </Reveal>
          ))}
        </ul>

        <Reveal>
          <section className="mt-12 surface-strong relative overflow-hidden p-7 sm:mt-16 sm:p-14">
            <div
              aria-hidden
              className="pointer-events-none absolute -bottom-20 -right-20 h-72 w-72 rounded-full bg-cyan-brand/[0.06] blur-3xl"
            />
            <p className="eyebrow">Engagement models</p>
            <h2 className="mt-3 heading-2 text-balance">
              Flexible engagement, senior-only delivery.
            </h2>
            <ul className="mt-6 grid gap-3 sm:grid-cols-3 sm:gap-4">
              <li className="surface p-5">
                <p className="text-sm font-semibold text-white">Full-time</p>
                <p className="mt-2 text-sm text-white/65">
                  Senior or lead roles with deep ownership.
                </p>
              </li>
              <li className="surface p-5">
                <p className="text-sm font-semibold text-white">Contract</p>
                <p className="mt-2 text-sm text-white/65">
                  Fixed-scope or retainer engagements for specific outcomes.
                </p>
              </li>
              <li className="surface p-5">
                <p className="text-sm font-semibold text-white">Consulting</p>
                <p className="mt-2 text-sm text-white/65">
                  Audits, planning, mentorship, and stakeholder workshops.
                </p>
              </li>
            </ul>
            <div className="mt-7 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
              <Link href="/contact" className="btn-primary w-full sm:w-auto">
                Hire Me
              </Link>
              <Link href="/cvs" className="btn-ghost w-full sm:w-auto">
                Download CV
              </Link>
            </div>
          </section>
        </Reveal>
      </Container>
    </>
  );
}
