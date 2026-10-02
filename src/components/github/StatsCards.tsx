type StatCard = {
  title: string;
  src: string;
  alt: string;
  span: "full" | "half";
  aspect: string;
};

export function StatsCards({ username }: { username: string }) {
  // All endpoints below have been live-checked. We intentionally avoid the
  // popular `github-readme-stats.vercel.app` service because its hosted
  // deployment is currently paused (`DEPLOYMENT_PAUSED`) and returns 503s.
  // We use `github-profile-summary-cards.vercel.app` for the bulk of the
  // cards (it's run by vn7n24fzkq and stays up reliably), plus
  // `streak-stats.demolab.com` (a maintained fork of DenverCoder1's streak
  // stats) for the streak card.
  const cards: StatCard[] = [
    {
      title: "Profile summary",
      src: `https://github-profile-summary-cards.vercel.app/api/cards/profile-details?username=${username}&theme=github_dark`,
      alt: `${username} — GitHub profile summary`,
      span: "full",
      aspect: "aspect-[3/1]",
    },
    {
      title: "Overall stats",
      src: `https://github-profile-summary-cards.vercel.app/api/cards/stats?username=${username}&theme=github_dark`,
      alt: `${username} — overall GitHub stats`,
      span: "half",
      aspect: "aspect-[16/10]",
    },
    {
      title: "Repos per language",
      src: `https://github-profile-summary-cards.vercel.app/api/cards/repos-per-language?username=${username}&theme=github_dark`,
      alt: `${username} — repos per language`,
      span: "half",
      aspect: "aspect-[16/10]",
    },
    {
      title: "Most committed language",
      src: `https://github-profile-summary-cards.vercel.app/api/cards/most-commit-language?username=${username}&theme=github_dark`,
      alt: `${username} — most committed language`,
      span: "half",
      aspect: "aspect-[16/10]",
    },
    {
      title: "Productive time",
      src: `https://github-profile-summary-cards.vercel.app/api/cards/productive-time?username=${username}&theme=github_dark&utcOffset=1`,
      alt: `${username} — productive time of day`,
      span: "half",
      aspect: "aspect-[16/10]",
    },
    {
      title: "Contribution streak",
      src: `https://streak-stats.demolab.com?user=${username}&hide_border=true&background=FFFFFF00&stroke=22D3EE&ring=22D3EE&fire=22D3EE&currStreakLabel=22D3EE&sideLabels=94A3B8&currStreakNum=F1F5F9&sideNums=F1F5F9&dates=94A3B8`,
      alt: `${username} — GitHub contribution streak`,
      span: "full",
      aspect: "aspect-[3/1]",
    },
  ];

  return (
    <ul className="grid gap-4 sm:grid-cols-2">
      {cards.map((card) => (
        <li
          key={card.title}
          className={card.span === "full" ? "sm:col-span-2" : undefined}
        >
          <a
            href={`https://github.com/${username}`}
            target="_blank"
            rel="noreferrer noopener"
            className="surface card-hover block h-full p-4"
            aria-label={`Open ${username} on GitHub — ${card.title}`}
          >
            <div className="flex items-center justify-between gap-3">
              <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
                {card.title}
              </p>
              <span className="text-[11px] font-medium text-white/55">
                Live from GitHub
              </span>
            </div>
            <div
              className={`mt-3 grid place-items-center overflow-hidden rounded-xl bg-[#0a0e1a]/60 ${card.aspect}`}
            >
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={card.src}
                alt={card.alt}
                loading="lazy"
                className="h-full w-full object-contain p-3"
              />
            </div>
          </a>
        </li>
      ))}
    </ul>
  );
}
