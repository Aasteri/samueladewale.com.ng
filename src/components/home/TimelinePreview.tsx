import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Container } from "@/components/Container";
import { SectionHeading } from "@/components/SectionHeading";
import { Reveal } from "@/components/Reveal";
import { SpotlightCard } from "@/components/SpotlightCard";
import { timeline } from "@/data/timeline";

export function TimelinePreview() {
  const preview = timeline.slice(0, 4);
  return (
    <section className="section">
      <Container>
        <SectionHeading
          eyebrow="Career timeline"
          title="A short snapshot of my professional journey."
          description="From startup-paced delivery to senior leadership on national platforms."
          action={
            <Link
              href="/about"
              className="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"
            >
              View full story
              <ArrowRight className="h-4 w-4" aria-hidden />
            </Link>
          }
        />

        <ol className="relative mt-10 grid gap-4 sm:grid-cols-2">
          {preview.map((item, i) => (
            <Reveal as="li" key={item.company} delay={i * 0.05}>
              <SpotlightCard className="surface card-hover h-full rounded-2xl p-6">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
                  {item.period}
                </p>
                <h3 className="mt-2 text-lg font-semibold text-white">
                  {item.company}
                </h3>
                <p className="mt-1 text-sm font-medium text-white/70">
                  {item.role}
                </p>
                <p className="mt-3 text-sm leading-relaxed text-white/60">
                  {item.description}
                </p>
              </SpotlightCard>
            </Reveal>
          ))}
        </ol>
      </Container>
    </section>
  );
}
