import Link from "next/link";
import { ArrowRight } from "lucide-react";
import { Container } from "@/components/Container";
import { SectionHeading } from "@/components/SectionHeading";
import { ProjectCard } from "@/components/ProjectCard";
import { Reveal } from "@/components/Reveal";
import { featuredProjects } from "@/data/projects";

export function FeaturedProjects() {
  return (
    <section className="section">
      <Container>
        <SectionHeading
          eyebrow="Selected work"
          title="Platforms I have designed, built, or led."
          description="A small selection — picked to show range across enterprise, government, and healthtech."
          action={
            <Link
              href="/projects"
              className="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"
            >
              View all projects
              <ArrowRight className="h-4 w-4" aria-hidden />
            </Link>
          }
        />

        <ul className="mt-12 grid gap-6 md:grid-cols-2">
          {featuredProjects.map((p, i) => (
            <Reveal as="li" key={p.slug} delay={i * 0.05}>
              <ProjectCard project={p} />
            </Reveal>
          ))}
        </ul>
      </Container>
    </section>
  );
}
