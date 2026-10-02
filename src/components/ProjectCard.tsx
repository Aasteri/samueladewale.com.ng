import Link from "next/link";
import Image from "next/image";
import { ArrowUpRight, ExternalLink, Lock } from "lucide-react";
import { SpotlightCard } from "@/components/SpotlightCard";
import type { Project } from "@/data/projects";

type Variant = "default" | "compact";

export function ProjectCard({
  project,
}: {
  project: Project;
  variant?: Variant;
}) {
  return (
    <SpotlightCard className="surface card-hover h-full rounded-2xl">
      <Link
        href={`/projects/${project.slug}`}
        className="group flex h-full flex-col overflow-hidden rounded-2xl"
        aria-label={`View case study: ${project.name}`}
      >
        <ProjectThumb project={project} />

        <div className="flex flex-1 flex-col p-6">
          <div className="flex items-start justify-between gap-3">
            <span className="pill">{project.category}</span>
            <ArrowUpRight className="h-5 w-5 text-white/40 transition-colors group-hover:text-cyan-brand" />
          </div>

          <h3 className="mt-4 heading-3">{project.name}</h3>
          <p className="mt-3 text-sm leading-relaxed text-white/60">
            {project.summary}
          </p>

          <div className="mt-5 flex flex-wrap gap-1.5">
            {project.stack.slice(0, 3).map((s) => (
              <span
                key={s}
                className="rounded-md border border-white/10 bg-white/[0.04] px-2 py-0.5 text-[11px] font-medium text-white/75"
              >
                {s}
              </span>
            ))}
          </div>

          <div className="mt-auto pt-6 flex items-center justify-between text-sm">
            <span className="inline-flex items-center gap-1 font-semibold text-white/85 transition-colors group-hover:text-cyan-brand">
              View case study
              <ArrowUpRight
                className="h-4 w-4 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                aria-hidden
              />
            </span>
            {project.confidential ? (
              <span className="inline-flex items-center gap-1 text-xs text-white/45">
                <Lock className="h-3.5 w-3.5" aria-hidden /> Confidential
              </span>
            ) : project.liveUrl ? (
              <span className="inline-flex items-center gap-1 text-xs text-white/45">
                <ExternalLink className="h-3.5 w-3.5" aria-hidden /> Live
              </span>
            ) : null}
          </div>
        </div>
      </Link>
    </SpotlightCard>
  );
}

function ProjectThumb({ project }: { project: Project }) {
  if (project.imageUrl) {
    return (
      <div className="media-frame aspect-[16/10] w-full overflow-hidden bg-[#0a0e1a]">
        <Image
          src={project.imageUrl}
          alt={`${project.name} — screenshot`}
          fill
          sizes="(max-width: 768px) 100vw, (max-width: 1280px) 50vw, 33vw"
          className="object-cover object-top transition-transform duration-500 group-hover:scale-[1.03] opacity-90"
        />
        <div
          aria-hidden
          className="absolute inset-0 bg-gradient-to-t from-[#060912]/85 via-[#060912]/10 to-transparent"
        />
      </div>
    );
  }

  return (
    <div className="media-frame aspect-[16/10] w-full overflow-hidden bg-[#0a0e1a]">
      <div aria-hidden className="absolute inset-0 grid-bg opacity-60" />
      <div
        aria-hidden
        className="pointer-events-none absolute -top-20 right-[-10%] h-72 w-72 rounded-full bg-cyan-brand/15 blur-3xl"
      />
      <div className="relative flex h-full w-full items-center justify-between gap-4 p-6 text-white">
        <div>
          <p className="text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-brand">
            {project.category}
          </p>
          <p className="mt-2 text-lg font-semibold leading-tight">
            {project.name}
          </p>
        </div>
        <span className="grid h-12 w-12 shrink-0 place-items-center rounded-xl border border-white/15 bg-white/5 font-mono text-sm text-cyan-brand">
          {project.name
            .split(/\s|-/)
            .slice(0, 2)
            .map((w) => w[0])
            .join("")
            .toUpperCase()}
        </span>
      </div>
    </div>
  );
}
