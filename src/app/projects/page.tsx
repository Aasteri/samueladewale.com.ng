import type { Metadata } from "next";
import Link from "next/link";
import { Container } from "@/components/Container";
import { PageHeader } from "@/components/PageHeader";
import { ProjectCard } from "@/components/ProjectCard";
import { Reveal } from "@/components/Reveal";
import { projectGroups, projects } from "@/data/projects";

export const metadata: Metadata = {
  title: "Projects",
  description:
    "A selected portfolio of enterprise, government, healthtech, and commercial platforms designed, built, and led by Samuel Adewale.",
  alternates: { canonical: "/projects" },
};

export default function ProjectsPage() {
  return (
    <>
      <PageHeader
        eyebrow="Projects"
        title="Platforms across enterprise, government, healthtech, and commercial."
        description="Each project is grouped by category. Open any case study for the problem, solution, key features, tech stack, and impact."
        actions={
          <>
            <Link href="/contact" className="btn-primary w-full sm:w-auto">
              Request Project Walkthrough
            </Link>
            <Link href="/github-activity" className="btn-ghost w-full sm:w-auto">
              View GitHub Activity
            </Link>
          </>
        }
      />

      <Container className="py-12">
        {projectGroups.map((group, gi) => {
          const items = projects.filter((p) => p.group === group.group);
          return (
            <section key={group.group} className="py-10">
              <Reveal>
                <div className="max-w-3xl">
                  <p className="eyebrow">Category {gi + 1}</p>
                  <h2 className="mt-2 heading-2">{group.group}</h2>
                  <p className="mt-3 text-white/65">{group.description}</p>
                </div>
              </Reveal>
              <ul className="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                {items.map((p, i) => (
                  <Reveal as="li" key={p.slug} delay={i * 0.03}>
                    <ProjectCard project={p} />
                  </Reveal>
                ))}
              </ul>
            </section>
          );
        })}
      </Container>
    </>
  );
}
