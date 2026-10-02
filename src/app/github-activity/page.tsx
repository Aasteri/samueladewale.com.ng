import type { Metadata } from "next";
import Link from "next/link";
import {
  Activity,
  AlertTriangle,
  Calendar,
  ExternalLink,
  Flame,
  GitBranch,
  GitPullRequest,
  CircleAlert,
  Languages,
  Star,
  GitFork,
  Lock,
  Users,
  Sparkles,
} from "lucide-react";
import dynamic from "next/dynamic";
import { Container } from "@/components/Container";
import { PageHeader } from "@/components/PageHeader";
import { Reveal } from "@/components/Reveal";
import { ContributionHeatmap } from "@/components/github/ContributionHeatmap";
import { StatsCards } from "@/components/github/StatsCards";
import { getGithubActivity } from "@/lib/github";
import { site } from "@/lib/site";

const ChartFallback = () => (
  <div className="h-64 w-full animate-pulse rounded-xl border border-white/5 bg-white/[0.02]" />
);

const MonthlyChart = dynamic(
  () => import("@/components/github/MonthlyChart").then((m) => m.MonthlyChart),
  { loading: ChartFallback }
);
const LanguageChart = dynamic(
  () =>
    import("@/components/github/LanguageChart").then((m) => m.LanguageChart),
  { loading: ChartFallback }
);
const RepoActivityChart = dynamic(
  () =>
    import("@/components/github/RepoActivityChart").then(
      (m) => m.RepoActivityChart
    ),
  { loading: ChartFallback }
);

export const revalidate = 21600; // 6 hours

export const metadata: Metadata = {
  title: "GitHub Activity",
  description:
    "A visual snapshot of Samuel Adewale's public coding activity — repositories, commits, pull requests, languages, and contribution patterns.",
  alternates: { canonical: "/github-activity" },
};

export default async function GithubActivityPage() {
  const data = await getGithubActivity();

  const topCards = [
    {
      icon: GitBranch,
      label: "Public repositories",
      value: data.totals.publicRepositories,
    },
    {
      icon: Activity,
      label: "Contributions (last year)",
      value: data.totals.totalContributions,
    },
    {
      icon: Sparkles,
      label: "Contributions (last 90 days)",
      value: data.totals.contributionsLast90Days,
    },
    {
      icon: GitPullRequest,
      label: "Pull requests authored",
      value: data.totals.totalPullRequests,
    },
    {
      icon: CircleAlert,
      label: "Issues opened",
      value: data.totals.totalIssues,
    },
    {
      icon: Languages,
      label: "Languages used",
      value: data.totals.activeLanguageCount,
    },
    {
      icon: Flame,
      label: "Current streak (days)",
      value: data.totals.currentStreakDays,
    },
    {
      icon: Calendar,
      label: "Longest streak (days)",
      value: data.totals.longestStreakDays,
    },
    {
      icon: Users,
      label: "Followers",
      value: data.totals.followers,
    },
  ];

  return (
    <>
      <PageHeader
        eyebrow="Developer Activity Dashboard"
        title="A live, visual snapshot of my GitHub activity."
        description="This page shows my public coding activity across repositories, commits, pull requests, and project updates. Refreshed automatically every few hours."
        actions={
          <>
            <a
              href={data.profile.url}
              target="_blank"
              rel="noreferrer noopener"
              className="btn-primary w-full sm:w-auto"
            >
              <ExternalLink className="h-4 w-4" aria-hidden />
              View on GitHub
            </a>
            <Link href="/projects" className="btn-ghost w-full sm:w-auto">
              View My Projects
            </Link>
          </>
        }
      />

      <Container className="py-10">
        <Reveal>
          <aside className="surface flex items-start gap-4 border-l-4 border-cyan-brand p-5">
            <Lock className="mt-0.5 h-5 w-5 text-cyan-brand" aria-hidden />
            <div className="text-sm leading-relaxed text-white/85">
              <p>
                Some enterprise and client work may not appear here because it
                was completed in private repositories, client-owned systems, or
                NDA-protected environments.
              </p>
              <p className="mt-2 text-white/60">
                For a full walkthrough of confidential projects, request an
                interview or session.
              </p>
            </div>
          </aside>
        </Reveal>

        {data.source === "fallback" && (
          <Reveal>
            <aside className="mt-4 surface flex items-start gap-3 border-l-4 border-yellow-400/70 p-5">
              <AlertTriangle
                className="mt-0.5 h-5 w-5 text-yellow-400"
                aria-hidden
              />
              <div className="text-sm leading-relaxed text-white/85">
                <p className="font-semibold">
                  Live GitHub data is temporarily unavailable.
                </p>
                <p className="mt-1 text-white/60">
                  {data.error
                    ? `Reason: ${data.error}`
                    : "GitHub APIs are not responding right now — please refresh in a few minutes."}
                </p>
              </div>
            </aside>
          </Reveal>
        )}

        <ul className="mt-8 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
          {topCards.map((c, i) => (
            <Reveal as="li" key={c.label} delay={i * 0.03}>
              <div className="surface card-hover h-full p-4 sm:p-5">
                <div className="grid h-9 w-9 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand">
                  <c.icon className="h-4 w-4" aria-hidden />
                </div>
                <p className="mt-3 text-xl font-semibold text-white sm:text-2xl">
                  {Intl.NumberFormat("en-US").format(c.value || 0)}
                </p>
                <p className="mt-1 break-words text-[11px] uppercase tracking-[0.1em] text-white/55 sm:text-xs sm:tracking-[0.12em]">
                  {c.label}
                </p>
              </div>
            </Reveal>
          ))}
        </ul>

        <Reveal>
          <section className="mt-10 surface p-5 sm:p-8">
            <div className="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-end sm:justify-between sm:gap-3">
              <div>
                <p className="eyebrow">Contribution calendar</p>
                <h2 className="mt-1 heading-3">Year-long activity heatmap</h2>
              </div>
              <p className="text-sm text-white/60">
                {Intl.NumberFormat("en-US").format(
                  data.totals.totalContributions
                )}{" "}
                contributions in the last year
              </p>
            </div>
            <div className="mt-6">
              <ContributionHeatmap weeks={data.weeks} />
            </div>
          </section>
        </Reveal>

        <div className="mt-8 grid gap-6 lg:grid-cols-2">
          <Reveal>
            <section className="surface p-5 sm:p-8">
              <p className="eyebrow">Monthly commits</p>
              <h2 className="mt-1 heading-3">Activity over the last 12 months</h2>
              <p className="mt-1 text-sm text-white/60">
                Monthly aggregate of public contributions.
              </p>
              <div className="mt-6 w-full overflow-hidden">
                <MonthlyChart data={data.monthly} />
              </div>
            </section>
          </Reveal>

          <Reveal delay={0.05}>
            <section className="surface p-5 sm:p-8">
              <p className="eyebrow">Language usage</p>
              <h2 className="mt-1 heading-3">Top languages by code size</h2>
              <p className="mt-1 text-sm text-white/60">
                Aggregated from recent public repositories.
              </p>
              <div className="mt-6 w-full overflow-hidden">
                <LanguageChart data={data.languages} />
              </div>
            </section>
          </Reveal>

          <Reveal>
            <section className="surface p-5 sm:p-8 lg:col-span-2">
              <p className="eyebrow">Repository activity</p>
              <h2 className="mt-1 heading-3">Recent project activity timeline</h2>
              <p className="mt-1 text-sm text-white/60">
                Repositories ranked by how recently they were pushed.
              </p>
              <div className="mt-6 w-full overflow-hidden">
                <RepoActivityChart repos={data.recentRepos} />
              </div>
            </section>
          </Reveal>
        </div>

        <Reveal>
          <section className="mt-10">
            <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
              <div className="min-w-0">
                <p className="eyebrow">Profile summary</p>
                <h2 className="mt-1 heading-3">
                  Live GitHub profile cards
                </h2>
                <p className="mt-1 max-w-2xl text-sm text-white/60">
                  Auto-generated summary cards from{" "}
                  <span className="font-mono">github-readme-stats</span> and{" "}
                  <span className="font-mono">
                    github-profile-summary-cards
                  </span>{" "}
                  — they always reflect the latest public data on my account.
                </p>
              </div>
              <a
                href={`https://github.com/${site.github}`}
                target="_blank"
                rel="noreferrer noopener"
                className="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"
              >
                Open profile
                <ExternalLink className="h-4 w-4" aria-hidden />
              </a>
            </div>
            <div className="mt-6">
              <StatsCards username={site.github} />
            </div>
          </section>
        </Reveal>

        <Reveal>
          <section className="mt-10">
            <div className="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between sm:gap-3">
              <div className="min-w-0">
                <p className="eyebrow">Recent repositories</p>
                <h2 className="mt-1 heading-3">Latest public work</h2>
              </div>
              <a
                href={`https://github.com/${site.github}?tab=repositories`}
                target="_blank"
                rel="noreferrer noopener"
                className="inline-flex shrink-0 items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"
              >
                See all on GitHub
                <ExternalLink className="h-4 w-4" aria-hidden />
              </a>
            </div>

            {data.recentRepos.length === 0 ? (
              <p className="mt-6 surface p-6 text-sm text-white/60">
                No public repositories to show yet. Most of my recent work
                lives in private and client-owned repositories.
              </p>
            ) : (
              <ul className="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                {data.recentRepos.map((r) => (
                  <li key={r.name}>
                    <a
                      href={r.url}
                      target="_blank"
                      rel="noreferrer noopener"
                      className="surface card-hover block h-full p-5"
                    >
                      <div className="flex items-start justify-between gap-3">
                        <p className="text-sm font-semibold text-white">
                          {r.name}
                        </p>
                        <ExternalLink
                          className="h-4 w-4 text-white/45"
                          aria-hidden
                        />
                      </div>
                      <p className="mt-2 line-clamp-3 text-sm text-white/60">
                        {r.description ?? "No description provided."}
                      </p>
                      <div className="mt-4 flex flex-wrap items-center gap-3 text-xs text-white/55">
                        {r.primaryLanguage && (
                          <span className="inline-flex items-center gap-1.5">
                            <span
                              className="h-2.5 w-2.5 rounded-full"
                              style={{
                                background: r.primaryLanguage.color,
                              }}
                            />
                            {r.primaryLanguage.name}
                          </span>
                        )}
                        <span className="inline-flex items-center gap-1">
                          <Star className="h-3.5 w-3.5" aria-hidden /> {r.stars}
                        </span>
                        <span className="inline-flex items-center gap-1">
                          <GitFork className="h-3.5 w-3.5" aria-hidden />{" "}
                          {r.forks}
                        </span>
                      </div>
                    </a>
                  </li>
                ))}
              </ul>
            )}
          </section>
        </Reveal>

        <p className="mt-10 text-center text-xs text-white/45">
          Data refreshed at{" "}
          {new Date(data.generatedAt).toLocaleString("en-GB", {
            dateStyle: "medium",
            timeStyle: "short",
          })}{" "}
          · cached for ~6 hours to respect GitHub API limits.
        </p>
      </Container>
    </>
  );
}
