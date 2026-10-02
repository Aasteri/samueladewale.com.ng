import type { Metadata } from "next";
import { notFound } from "next/navigation";
import Link from "next/link";
import Image from "next/image";
import {
  ArrowLeft,
  CheckCircle2,
  ExternalLink,
  Lock,
  Sparkles,
  Target,
  Workflow,
} from "lucide-react";
import { Container } from "@/components/Container";
import { Reveal } from "@/components/Reveal";
import {
  confidentialityNote,
  getProjectBySlug,
  projects,
} from "@/data/projects";

export function generateStaticParams() {
  return projects.map((p) => ({ slug: p.slug }));
}

type Props = { params: Promise<{ slug: string }> };

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { slug } = await params;
  const project = getProjectBySlug(slug);
  if (!project) {
    return {
      title: "Project not found",
    };
  }
  return {
    title: project.name,
    description: project.summary,
    alternates: { canonical: `/projects/${project.slug}` },
    openGraph: {
      title: `${project.name} — Case Study`,
      description: project.summary,
      type: "article",
      images: project.imageUrl ? [{ url: project.imageUrl }] : undefined,
    },
  };
}

export default async function ProjectPage({ params }: Props) {
  const { slug } = await params;
  const project = getProjectBySlug(slug);
  if (!project) return notFound();

  return (
    <>
      <section className="relative overflow-hidden border-b border-white/[0.06] pt-12 pb-12 sm:pt-16">
        <div aria-hidden className="grid-bg pointer-events-none absolute inset-0" />
        <Container className="relative">
          <Link
            href="/projects"
            className="inline-flex items-center gap-2 text-sm font-medium text-white/55 hover:text-white"
          >
            <ArrowLeft className="h-4 w-4" aria-hidden />
            All projects
          </Link>

          <div className="mt-6 grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center lg:gap-10">
            <div>
              <div className="flex flex-wrap items-center gap-2">
                <span className="pill">{project.category}</span>
                <span className="pill">{project.group}</span>
                {project.year && <span className="pill">{project.year}</span>}
                {project.confidential && (
                  <span className="pill">
                    <Lock className="h-3.5 w-3.5" aria-hidden /> Confidential
                  </span>
                )}
              </div>

              <h1 className="mt-4 heading-1 text-balance">{project.name}</h1>
              <p className="mt-3 break-words text-base font-medium text-cyan-brand">
                Role: {project.role}
              </p>
              {project.client && (
                <p className="mt-1 break-words text-sm text-white/55">
                  Client:{" "}
                  <span className="font-medium text-white/85">
                    {project.client}
                  </span>
                </p>
              )}
              <p className="mt-4 max-w-3xl lead text-pretty">
                {project.summary}
              </p>

              <div className="mt-6 flex flex-wrap gap-2">
                {project.stack.map((s) => (
                  <span
                    key={s}
                    className="rounded-md border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs font-medium text-white/75"
                  >
                    {s}
                  </span>
                ))}
              </div>

              {project.liveUrl && (
                <div className="mt-6">
                  <a
                    href={project.liveUrl}
                    target="_blank"
                    rel="noreferrer noopener"
                    className="btn-primary w-full sm:w-auto"
                  >
                    <ExternalLink className="h-4 w-4" aria-hidden />
                    Visit live site
                    {project.previewLabel && (
                      <span className="ml-2 hidden font-mono text-xs font-normal text-white/70 sm:inline">
                        {project.previewLabel}
                      </span>
                    )}
                  </a>
                </div>
              )}
            </div>

            <div className="relative">
              {project.imageUrl ? (
                <div className="media-frame aspect-[16/10] overflow-hidden rounded-2xl border border-white/10 bg-[#0a0e1a]">
                  <Image
                    src={project.imageUrl}
                    alt={`${project.name} — screenshot`}
                    fill
                    sizes="(max-width: 1024px) 100vw, 50vw"
                    className="object-cover object-top"
                    priority
                  />
                </div>
              ) : (
                <div className="media-frame aspect-[16/10] overflow-hidden rounded-2xl border border-white/10 bg-[#0a0e1a]">
                  <div
                    aria-hidden
                    className="absolute inset-0 grid-bg opacity-60"
                  />
                  <div
                    aria-hidden
                    className="pointer-events-none absolute -top-20 right-[-10%] h-72 w-72 rounded-full bg-cyan-brand/20 blur-3xl"
                  />
                  <div className="relative flex h-full w-full flex-col items-start justify-between p-8 text-white">
                    <span className="pill-dark">
                      <Lock className="h-3.5 w-3.5" aria-hidden />
                      Screenshot unavailable
                    </span>
                    <div>
                      <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
                        {project.category}
                      </p>
                      <p className="mt-2 text-2xl font-semibold leading-tight">
                        {project.name}
                      </p>
                    </div>
                  </div>
                </div>
              )}
            </div>
          </div>
        </Container>
      </section>

      <Container className="py-12">
        <div className="grid gap-6 lg:grid-cols-2 lg:gap-8">
          <Reveal>
            <article className="surface h-full p-6 sm:p-8">
              <div className="grid h-10 w-10 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand">
                <Target className="h-5 w-5" aria-hidden />
              </div>
              <h2 className="mt-4 heading-3">Problem</h2>
              <p className="mt-3 leading-relaxed text-white/65">
                {project.problem}
              </p>
            </article>
          </Reveal>
          <Reveal delay={0.05}>
            <article className="surface h-full p-6 sm:p-8">
              <div className="grid h-10 w-10 place-items-center rounded-lg border border-white/10 bg-cyan-brand/[0.08] text-cyan-brand">
                <Workflow className="h-5 w-5" aria-hidden />
              </div>
              <h2 className="mt-4 heading-3">Solution</h2>
              <p className="mt-3 leading-relaxed text-white/65">
                {project.solution}
              </p>
            </article>
          </Reveal>
        </div>

        <div className="mt-6 grid gap-6 sm:mt-8 lg:grid-cols-[1.1fr_0.9fr] lg:gap-8">
          <Reveal>
            <article className="surface p-6 sm:p-8">
              <div className="grid h-10 w-10 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand">
                <CheckCircle2 className="h-5 w-5" aria-hidden />
              </div>
              <h2 className="mt-4 heading-3">Key features</h2>
              <ul className="mt-4 grid gap-2 sm:grid-cols-2">
                {project.features.map((f) => (
                  <li
                    key={f}
                    className="flex items-start gap-2 text-sm text-white/80"
                  >
                    <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-cyan-brand" />
                    <span>{f}</span>
                  </li>
                ))}
              </ul>
            </article>
          </Reveal>

          <Reveal delay={0.05}>
            <article className="surface-strong p-6 sm:p-8">
              <div className="grid h-10 w-10 place-items-center rounded-lg border border-white/10 bg-white/[0.06] text-cyan-brand">
                <Sparkles className="h-5 w-5" aria-hidden />
              </div>
              <h2 className="mt-4 text-2xl font-semibold tracking-tight text-white">
                Impact
              </h2>
              <ul className="mt-4 space-y-2">
                {project.impact.map((i) => (
                  <li
                    key={i}
                    className="flex items-start gap-2 text-sm text-white/85"
                  >
                    <span className="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-brand" />
                    <span>{i}</span>
                  </li>
                ))}
              </ul>
            </article>
          </Reveal>
        </div>

        {project.confidential && (
          <Reveal>
            <aside className="mt-8 surface flex items-start gap-4 border-l-4 border-cyan-brand p-6">
              <Lock className="mt-1 h-5 w-5 text-cyan-brand" aria-hidden />
              <p className="text-sm leading-relaxed text-white/75">
                {confidentialityNote}
              </p>
            </aside>
          </Reveal>
        )}

        <Reveal>
          <div className="mt-10 surface flex flex-col items-start justify-between gap-5 p-6 sm:mt-12 md:flex-row md:items-center">
            <p className="text-sm text-white/65">
              Want a deeper walkthrough of this project?
            </p>
            <div className="grid w-full grid-cols-1 gap-3 sm:flex sm:flex-wrap md:w-auto">
              {project.liveUrl && (
                <a
                  href={project.liveUrl}
                  target="_blank"
                  rel="noreferrer noopener"
                  className="btn-ghost w-full sm:w-auto"
                >
                  <ExternalLink className="h-4 w-4" aria-hidden />
                  Visit live site
                </a>
              )}
              <Link href="/contact" className="btn-primary w-full sm:w-auto">
                Request Project Walkthrough
              </Link>
              <Link href="/projects" className="btn-ghost w-full sm:w-auto">
                View Case Studies
              </Link>
            </div>
          </div>
        </Reveal>
      </Container>
    </>
  );
}
