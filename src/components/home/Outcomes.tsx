import { Building2, ShieldCheck, Smartphone, Server } from "lucide-react";
import { Container } from "@/components/Container";
import { SectionHeading } from "@/components/SectionHeading";
import { Reveal } from "@/components/Reveal";
import { SpotlightCard } from "@/components/SpotlightCard";

const metrics = [
  {
    icon: Server,
    value: "28+",
    label: "Projects delivered",
    helper: "Across enterprise, government, healthtech and commercial",
  },
  {
    icon: Building2,
    value: "9",
    label: "Federal & enterprise platforms",
    helper: "From ERPs and HSE systems to national portals",
  },
  {
    icon: ShieldCheck,
    value: "5+",
    label: "Federal agencies served",
    helper: "Including ministries and government corporations",
  },
  {
    icon: Smartphone,
    value: "7+",
    label: "Years shipping production",
    helper: "Web, mobile, and back-office systems",
  },
];

const trustedBy = [
  "Nigerian Bulk Electricity Trading PLC",
  "Nigerian National Petroleum Corporation",
  "Federal Ministry of Labour & Employment",
  "Federal Character Commission",
  "Federal Fire Service",
  "Nigeria Social Insurance Trust Fund",
  "National Inland Waterways Authority",
  "Akwa Ibom State Government",
];

export function Outcomes() {
  return (
    <section className="section">
      <Container>
        <SectionHeading
          eyebrow="Track record"
          title="Mission-critical software for real institutions."
          description="National portals, federal compliance systems, enterprise ERPs and healthtech MVPs — shipped to production, not slide decks."
        />

        <ul className="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          {metrics.map((m, i) => (
            <Reveal as="li" key={m.label} delay={i * 0.05}>
              <SpotlightCard className="surface card-hover h-full rounded-2xl p-6">
                <span className="grid h-11 w-11 place-items-center rounded-xl border border-cyan-brand/20 bg-cyan-brand/[0.08] text-cyan-brand">
                  <m.icon className="h-5 w-5" aria-hidden />
                </span>
                <p className="mt-6 text-[2.5rem] font-semibold leading-none tracking-tight text-white sm:text-[2.75rem]">
                  {m.value}
                </p>
                <p className="mt-3 text-sm font-medium text-white/90">
                  {m.label}
                </p>
                <p className="mt-1.5 text-sm leading-relaxed text-white/55">
                  {m.helper}
                </p>
              </SpotlightCard>
            </Reveal>
          ))}
        </ul>

        <Reveal>
          <div className="surface mt-10 p-6 sm:p-8">
            <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
              Selected clients
            </p>
            <ul className="mt-5 flex flex-wrap gap-2">
              {trustedBy.map((name) => (
                <li
                  key={name}
                  className="rounded-md border border-white/10 bg-white/[0.03] px-3 py-1.5 text-xs font-medium text-white/75"
                >
                  {name}
                </li>
              ))}
            </ul>
          </div>
        </Reveal>
      </Container>
    </section>
  );
}
