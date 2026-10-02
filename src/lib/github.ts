import { site } from "./site";

const GITHUB_GRAPHQL = "https://api.github.com/graphql";
const GITHUB_REST = "https://api.github.com";
const CONTRIBUTIONS_API = "https://github-contributions-api.jogruber.de/v4";

const CACHE_TTL_SECONDS = 60 * 60 * 6; // 6 hours

export type ContributionDay = {
  date: string;
  count: number;
  level: 0 | 1 | 2 | 3 | 4;
  weekday: number;
};

export type ContributionWeek = {
  startDate: string;
  days: ContributionDay[];
};

export type LanguageBreakdown = {
  name: string;
  size: number;
  color: string;
  percent: number;
};

export type RepoSummary = {
  name: string;
  description: string | null;
  url: string;
  stars: number;
  forks: number;
  primaryLanguage: { name: string; color: string } | null;
  pushedAt: string;
  isPrivate: boolean;
};

export type MonthlyActivity = {
  month: string;
  date: string;
  contributions: number;
};

export type GithubActivity = {
  username: string;
  profile: {
    name: string | null;
    avatarUrl: string | null;
    bio: string | null;
    company: string | null;
    location: string | null;
    url: string;
    followers: number;
    following: number;
  };
  totals: {
    publicRepositories: number;
    totalContributions: number;
    contributionsLast90Days: number;
    totalPullRequests: number;
    totalIssues: number;
    totalPullRequestReviews: number;
    totalCommits: number;
    currentStreakDays: number;
    longestStreakDays: number;
    activeLanguageCount: number;
    followers: number;
  };
  weeks: ContributionWeek[];
  languages: LanguageBreakdown[];
  recentRepos: RepoSummary[];
  monthly: MonthlyActivity[];
  generatedAt: string;
  source: "graphql" | "rest" | "fallback";
  error?: string;
};

// ─── Language colours (used when GitHub doesn't supply one) ──────────────
const FALLBACK_LANGUAGE_COLORS: Record<string, string> = {
  TypeScript: "#3178C6",
  JavaScript: "#F1E05A",
  PHP: "#4F5D95",
  HTML: "#E34C26",
  CSS: "#563D7C",
  Python: "#3572A5",
  Go: "#00ADD8",
  Shell: "#89E051",
  SCSS: "#C6538C",
  Vue: "#41B883",
  Dart: "#00B4AB",
  Java: "#B07219",
  Kotlin: "#A97BFF",
  Swift: "#F05138",
  Ruby: "#701516",
  C: "#555555",
  "C++": "#F34B7D",
  "C#": "#178600",
  Rust: "#DEA584",
};

function colorFor(name: string, fromApi?: string | null): string {
  return (
    fromApi ||
    FALLBACK_LANGUAGE_COLORS[name] ||
    "#64748B"
  );
}

// ─── Auth headers ────────────────────────────────────────────────────────
function authHeaders(): HeadersInit {
  const headers: Record<string, string> = {
    Accept: "application/vnd.github+json",
    "User-Agent": "samueladewale.com",
  };
  if (process.env.GITHUB_TOKEN) {
    headers.Authorization = `Bearer ${process.env.GITHUB_TOKEN}`;
  }
  return headers;
}

// ─── Streak + monthly aggregation helpers ────────────────────────────────
function computeStreaks(weeks: ContributionWeek[]) {
  const days = weeks.flatMap((w) => w.days);
  let longest = 0;
  let running = 0;
  for (const d of days) {
    if (d.count > 0) {
      running += 1;
      if (running > longest) longest = running;
    } else {
      running = 0;
    }
  }

  let current = 0;
  // Walk backwards from the most recent non-future day with data.
  const todayIso = new Date().toISOString().slice(0, 10);
  for (let i = days.length - 1; i >= 0; i--) {
    const d = days[i];
    if (d.date > todayIso) continue;
    if (d.count > 0) current += 1;
    else break;
  }
  return { current, longest };
}

function aggregateMonthly(weeks: ContributionWeek[]): MonthlyActivity[] {
  const buckets = new Map<string, MonthlyActivity>();
  for (const w of weeks) {
    for (const d of w.days) {
      const key = d.date.slice(0, 7);
      const date = `${key}-01`;
      const monthLabel = new Date(date).toLocaleString("en-US", {
        month: "short",
      });
      const bucket =
        buckets.get(key) || {
          month: monthLabel,
          date,
          contributions: 0,
        };
      bucket.contributions += d.count;
      buckets.set(key, bucket);
    }
  }
  return Array.from(buckets.values())
    .sort((a, b) => a.date.localeCompare(b.date))
    .slice(-12);
}

function computeLast90DaysTotal(weeks: ContributionWeek[]): number {
  const cutoff = new Date();
  cutoff.setUTCDate(cutoff.getUTCDate() - 90);
  const cutoffIso = cutoff.toISOString().slice(0, 10);
  let total = 0;
  for (const w of weeks) {
    for (const d of w.days) {
      if (d.date >= cutoffIso) total += d.count;
    }
  }
  return total;
}

// ─── Calendar shaping (jogruber → ContributionWeek[]) ────────────────────
type JogruberDay = { date: string; count: number; level: 0 | 1 | 2 | 3 | 4 };
type JogruberResponse = {
  total: Record<string, number>;
  contributions: JogruberDay[];
};

function shapeCalendarFromJogruber(
  contributions: JogruberDay[]
): ContributionWeek[] {
  if (!contributions.length) return [];

  // Only keep the last 53 weeks (≈ 1 year, the same window GitHub shows),
  // and strip any future-dated zero-padding the upstream API includes
  // through the end of the current year.
  const todayIso = new Date().toISOString().slice(0, 10);
  const cutoff = new Date();
  cutoff.setUTCDate(cutoff.getUTCDate() - 53 * 7);
  const cutoffIso = cutoff.toISOString().slice(0, 10);
  const trimmed = contributions
    .filter((d) => d.date >= cutoffIso && d.date <= todayIso)
    .sort((a, b) => a.date.localeCompare(b.date));

  if (!trimmed.length) return [];

  // Pad to align Sunday-start weeks like GitHub's calendar.
  const firstDate = new Date(trimmed[0].date);
  const padding = firstDate.getUTCDay(); // 0 = Sunday
  const weeks: ContributionWeek[] = [];
  let currentWeek: ContributionDay[] = [];
  let weekStart: string | null = null;

  // Walk in 1-day steps from the padded start to keep weeks aligned.
  const start = new Date(firstDate);
  start.setUTCDate(start.getUTCDate() - padding);
  const end = new Date(trimmed[trimmed.length - 1].date);
  const map = new Map(trimmed.map((d) => [d.date, d]));

  for (
    const cursor = new Date(start);
    cursor <= end;
    cursor.setUTCDate(cursor.getUTCDate() + 1)
  ) {
    const iso = cursor.toISOString().slice(0, 10);
    const weekday = cursor.getUTCDay();
    if (weekday === 0) {
      if (currentWeek.length) {
        weeks.push({
          startDate: weekStart ?? iso,
          days: currentWeek,
        });
      }
      currentWeek = [];
      weekStart = iso;
    }
    const day = map.get(iso);
    currentWeek.push({
      date: iso,
      count: day?.count ?? 0,
      level: day?.level ?? 0,
      weekday,
    });
  }
  if (currentWeek.length) {
    weeks.push({
      startDate: weekStart ?? currentWeek[0].date,
      days: currentWeek,
    });
  }
  return weeks;
}

// ─── REST: profile, repos, languages, PR/issue search ────────────────────
type RestUser = {
  login: string;
  name: string | null;
  avatar_url: string;
  bio: string | null;
  company: string | null;
  location: string | null;
  html_url: string;
  followers: number;
  following: number;
  public_repos: number;
};

type RestRepo = {
  name: string;
  description: string | null;
  html_url: string;
  stargazers_count: number;
  forks_count: number;
  language: string | null;
  pushed_at: string;
  private: boolean;
  fork: boolean;
  archived: boolean;
};

async function restFetch<T>(path: string): Promise<T | null> {
  try {
    const res = await fetch(`${GITHUB_REST}${path}`, {
      headers: authHeaders(),
      next: { revalidate: CACHE_TTL_SECONDS },
    });
    if (!res.ok) return null;
    return (await res.json()) as T;
  } catch {
    return null;
  }
}

async function fetchContributions(
  username: string
): Promise<{
  weeks: ContributionWeek[];
  totalYear: number;
} | null> {
  try {
    const res = await fetch(`${CONTRIBUTIONS_API}/${username}`, {
      next: { revalidate: CACHE_TTL_SECONDS },
    });
    if (!res.ok) return null;
    const data = (await res.json()) as JogruberResponse;
    const weeks = shapeCalendarFromJogruber(data.contributions);
    const year = new Date().getUTCFullYear().toString();
    const totalYear =
      data.total[year] ??
      Object.values(data.total).reduce((a, b) => a + b, 0);
    return { weeks, totalYear };
  } catch {
    return null;
  }
}

async function fetchSearchCount(query: string): Promise<number> {
  const data = await restFetch<{ total_count: number }>(
    `/search/issues?q=${encodeURIComponent(query)}&per_page=1`
  );
  return data?.total_count ?? 0;
}

async function fetchLanguageBreakdown(
  username: string,
  repos: RestRepo[]
): Promise<LanguageBreakdown[]> {
  const top = repos
    .filter((r) => !r.archived && !r.fork)
    .slice(0, 12);
  const results = await Promise.allSettled(
    top.map((r) =>
      restFetch<Record<string, number>>(
        `/repos/${username}/${r.name}/languages`
      )
    )
  );
  const totals = new Map<string, number>();
  results.forEach((r) => {
    if (r.status !== "fulfilled" || !r.value) return;
    for (const [name, size] of Object.entries(r.value)) {
      totals.set(name, (totals.get(name) ?? 0) + size);
    }
  });
  // If we got nothing (REST rate limited?), fall back to primary-language
  // counts so we still render *something* useful.
  if (totals.size === 0) {
    const counts = new Map<string, number>();
    for (const r of top) {
      if (!r.language) continue;
      counts.set(r.language, (counts.get(r.language) ?? 0) + 1);
    }
    for (const [name, count] of counts) totals.set(name, count);
  }
  const sum = Array.from(totals.values()).reduce((a, b) => a + b, 0) || 1;
  return Array.from(totals.entries())
    .map(([name, size]) => ({
      name,
      size,
      color: colorFor(name),
      percent: (size / sum) * 100,
    }))
    .sort((a, b) => b.size - a.size)
    .slice(0, 8);
}

// ─── GraphQL (only used when GITHUB_TOKEN is set, for richer data) ───────
type GraphQLResponse = {
  data?: {
    user?: {
      contributionsCollection: {
        totalCommitContributions: number;
        totalPullRequestContributions: number;
        totalIssueContributions: number;
        totalPullRequestReviewContributions: number;
        contributionCalendar: {
          totalContributions: number;
          weeks: Array<{
            firstDay: string;
            contributionDays: Array<{
              date: string;
              contributionCount: number;
              contributionLevel:
                | "NONE"
                | "FIRST_QUARTILE"
                | "SECOND_QUARTILE"
                | "THIRD_QUARTILE"
                | "FOURTH_QUARTILE";
              weekday: number;
            }>;
          }>;
        };
      };
    };
  };
  errors?: Array<{ message: string }>;
};

const LEVEL_MAP: Record<string, 0 | 1 | 2 | 3 | 4> = {
  NONE: 0,
  FIRST_QUARTILE: 1,
  SECOND_QUARTILE: 2,
  THIRD_QUARTILE: 3,
  FOURTH_QUARTILE: 4,
};

async function fetchGraphqlContributions(
  username: string
): Promise<{
  weeks: ContributionWeek[];
  totalYear: number;
  totalCommits: number;
  totalPRs: number;
  totalIssues: number;
  totalReviews: number;
} | null> {
  if (!process.env.GITHUB_TOKEN) return null;
  try {
    const res = await fetch(GITHUB_GRAPHQL, {
      method: "POST",
      headers: {
        ...authHeaders(),
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        query: `
          query($login: String!) {
            user(login: $login) {
              contributionsCollection {
                totalCommitContributions
                totalPullRequestContributions
                totalIssueContributions
                totalPullRequestReviewContributions
                contributionCalendar {
                  totalContributions
                  weeks {
                    firstDay
                    contributionDays {
                      date
                      contributionCount
                      contributionLevel
                      weekday
                    }
                  }
                }
              }
            }
          }
        `,
        variables: { login: username },
      }),
      next: { revalidate: CACHE_TTL_SECONDS },
    });
    if (!res.ok) return null;
    const json = (await res.json()) as GraphQLResponse;
    const user = json.data?.user;
    if (!user) return null;
    const c = user.contributionsCollection;
    const weeks = c.contributionCalendar.weeks.map((w) => ({
      startDate: w.firstDay,
      days: w.contributionDays.map((d) => ({
        date: d.date,
        count: d.contributionCount,
        level: LEVEL_MAP[d.contributionLevel] ?? 0,
        weekday: d.weekday,
      })),
    }));
    return {
      weeks,
      totalYear: c.contributionCalendar.totalContributions,
      totalCommits: c.totalCommitContributions,
      totalPRs: c.totalPullRequestContributions,
      totalIssues: c.totalIssueContributions,
      totalReviews: c.totalPullRequestReviewContributions,
    };
  } catch {
    return null;
  }
}

// ─── Public entry point ──────────────────────────────────────────────────
async function fetchActivity(username: string): Promise<GithubActivity> {
  // 1. Profile + repos run in parallel and are needed in every mode.
  const [user, repos, jogruber, graphql] = await Promise.all([
    restFetch<RestUser>(`/users/${username}`),
    restFetch<RestRepo[]>(
      `/users/${username}/repos?sort=pushed&per_page=30&type=owner`
    ),
    fetchContributions(username),
    fetchGraphqlContributions(username),
  ]);

  if (!user || !repos) {
    return buildErrorFallback(
      username,
      "GitHub API is unavailable right now."
    );
  }

  const publicRepos = repos.filter(
    (r) => !r.private && !r.fork && !r.archived
  );
  const recentRepos: RepoSummary[] = publicRepos.slice(0, 9).map((r) => ({
    name: r.name,
    description: r.description,
    url: r.html_url,
    stars: r.stargazers_count,
    forks: r.forks_count,
    primaryLanguage: r.language
      ? { name: r.language, color: colorFor(r.language) }
      : null,
    pushedAt: r.pushed_at,
    isPrivate: r.private,
  }));

  // Prefer GraphQL data (more accurate) when available, fall back to the
  // free no-auth contributions API otherwise. Both shape the same way.
  const calendarSource = graphql ?? jogruber;
  const weeks = calendarSource?.weeks ?? [];
  // For the no-token (REST) path, sum across the trimmed weeks so the
  // metric reflects the **rolling last 12 months** (matching what GitHub
  // shows on the profile page) instead of the current calendar year.
  const totalYear = graphql
    ? calendarSource?.totalYear ?? 0
    : weeks.reduce(
        (sum, w) => sum + w.days.reduce((s, d) => s + d.count, 0),
        0
      );

  const { current: currentStreakDays, longest: longestStreakDays } =
    computeStreaks(weeks);
  const monthly = aggregateMonthly(weeks);
  const contributionsLast90Days = computeLast90DaysTotal(weeks);

  // PR / issue counts: prefer GraphQL totals, otherwise use the public
  // search API which works without a token (subject to rate limits).
  let totalPullRequests = graphql?.totalPRs ?? 0;
  let totalIssues = graphql?.totalIssues ?? 0;
  let totalPullRequestReviews = graphql?.totalReviews ?? 0;
  let totalCommits = graphql?.totalCommits ?? 0;

  if (!graphql) {
    const [prCount, issueCount] = await Promise.all([
      fetchSearchCount(`author:${username} type:pr`),
      fetchSearchCount(`author:${username} type:issue`),
    ]);
    totalPullRequests = prCount;
    totalIssues = issueCount;
    // Without GraphQL we can't separately count commits / reviews; show 0
    // and we hide those cards on the page when there's no GraphQL source.
    totalPullRequestReviews = 0;
    totalCommits = 0;
  }

  const languages = await fetchLanguageBreakdown(username, publicRepos);

  return {
    username,
    profile: {
      name: user.name,
      avatarUrl: user.avatar_url,
      bio: user.bio,
      company: user.company,
      location: user.location,
      url: user.html_url,
      followers: user.followers,
      following: user.following,
    },
    totals: {
      publicRepositories: user.public_repos,
      totalContributions: totalYear,
      contributionsLast90Days,
      totalPullRequests,
      totalIssues,
      totalPullRequestReviews,
      totalCommits,
      currentStreakDays,
      longestStreakDays,
      activeLanguageCount: languages.length,
      followers: user.followers,
    },
    weeks,
    languages,
    recentRepos,
    monthly,
    generatedAt: new Date().toISOString(),
    source: graphql ? "graphql" : "rest",
  };
}

function buildErrorFallback(username: string, error: string): GithubActivity {
  const now = new Date();
  const weeks: ContributionWeek[] = [];
  const cursor = new Date(now);
  cursor.setUTCDate(cursor.getUTCDate() - 52 * 7);
  cursor.setUTCDate(cursor.getUTCDate() - cursor.getUTCDay());

  for (let w = 0; w < 53; w++) {
    const days: ContributionDay[] = [];
    const start = new Date(cursor);
    for (let d = 0; d < 7; d++) {
      const day = new Date(cursor);
      days.push({
        date: day.toISOString().slice(0, 10),
        count: 0,
        level: 0,
        weekday: day.getUTCDay(),
      });
      cursor.setUTCDate(cursor.getUTCDate() + 1);
    }
    weeks.push({ startDate: start.toISOString().slice(0, 10), days });
  }
  const monthly: MonthlyActivity[] = [];
  for (let m = 11; m >= 0; m--) {
    const d = new Date(now.getUTCFullYear(), now.getUTCMonth() - m, 1);
    monthly.push({
      month: d.toLocaleString("en-US", { month: "short" }),
      date: d.toISOString().slice(0, 10),
      contributions: 0,
    });
  }
  return {
    username,
    profile: {
      name: null,
      avatarUrl: null,
      bio: null,
      company: null,
      location: null,
      url: `https://github.com/${username}`,
      followers: 0,
      following: 0,
    },
    totals: {
      publicRepositories: 0,
      totalContributions: 0,
      contributionsLast90Days: 0,
      totalPullRequests: 0,
      totalIssues: 0,
      totalPullRequestReviews: 0,
      totalCommits: 0,
      currentStreakDays: 0,
      longestStreakDays: 0,
      activeLanguageCount: 0,
      followers: 0,
    },
    weeks,
    languages: [],
    recentRepos: [],
    monthly,
    generatedAt: new Date().toISOString(),
    source: "fallback",
    error,
  };
}

export async function getGithubActivity(): Promise<GithubActivity> {
  return fetchActivity(site.github);
}
