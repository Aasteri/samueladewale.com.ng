<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
require __DIR__ . '/backend/lib/github.php';
page_guard('github');
$site = site();
$data = github_activity($site['github']);
$levels = ['#1e293b', '#155e75', '#0891b2', '#22d3ee', '#a5f3fc'];
$monthMax = 1;
foreach ($data['monthly'] ?? [] as $row) {
    $monthMax = max($monthMax, (int) $row['count']);
}
$cards = [
    ['Public repositories', $data['repos'] ?? 0],
    ['Contributions, last year', $data['total'] ?? 0],
    ['Last 90 days', $data['last90'] ?? 0],
    ['Pull requests', $data['pulls'] ?? 0],
    ['Issues opened', $data['issues'] ?? 0],
    ['Languages', count($data['languages'] ?? [])],
    ['Current streak', $data['currentStreak'] ?? 0],
    ['Longest streak', $data['longestStreak'] ?? 0],
    ['Followers', $data['followers'] ?? 0],
];
layout_start('GitHub Activity', "A visual snapshot of {$site['name']}'s public coding activity, repositories, pull requests, languages, and contribution patterns.");
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-14 pt-12 sm:pb-20 sm:pt-16">
  <div class="container container-prose relative">
    <p class="eyebrow"><?= e(content_text('github', 'eyebrow')) ?></p>
    <h1 class="mt-3 heading-1 text-balance"><?= e(content_text('github', 'title')) ?></h1>
    <p class="mt-4 max-w-3xl lead"><?= e(content_text('github', 'lead')) ?></p>
    <div class="mt-8 grid grid-cols-1 gap-3 sm:flex">
      <a href="<?= e($data['url']) ?>" class="btn-primary w-full sm:w-auto" target="_blank" rel="noreferrer noopener"><?= icon('external') ?> View on GitHub</a>
      <a href="/projects" class="btn-ghost w-full sm:w-auto">View My Projects</a>
    </div>
  </div>
</section>
<div class="container container-prose py-10">
  <aside class="surface flex items-start gap-4 border-l-4 border-cyan-brand p-5">
    <?= icon('lock', 'mt-0.5 h-5 w-5 text-cyan-brand') ?>
    <p class="text-sm leading-relaxed text-white/85"><?= e($data['analysis'] ?? '') ?> <?= e(content_text('github', 'note')) ?></p>
  </aside>
  <?php if (empty($data['ok'])): ?>
    <p class="surface mt-4 p-5 text-sm text-white/70">Live GitHub data is temporarily unavailable. Please refresh in a few minutes.</p>
  <?php endif; ?>

  <ul class="mt-8 grid grid-cols-2 gap-3 md:grid-cols-3">
    <?php foreach ($cards as $card): ?>
      <li class="surface p-4 sm:p-5">
        <p class="text-2xl font-semibold text-white"><?= e(number_format((int) $card[1])) ?></p>
        <p class="mt-1 text-[11px] uppercase tracking-[0.12em] text-white/55"><?= e($card[0]) ?></p>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if (!empty($data['weeks'])): ?>
    <section class="surface mt-10 overflow-x-auto p-5 sm:p-8">
      <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
          <p class="eyebrow">Contribution calendar</p>
          <h2 class="mt-1 heading-3">Year-long activity heatmap</h2>
        </div>
        <p class="text-sm text-white/60"><?= e(number_format((int) $data['total'])) ?> contributions in the tracked year</p>
      </div>
      <div class="mt-6 flex gap-1">
        <?php foreach ($data['weeks'] as $week): ?>
          <div class="grid grid-rows-7 gap-1">
            <?php foreach ($week as $day):
              $level = (int) ($day['level'] ?? 0);
              $color = $levels[$level] ?? $levels[0];
            ?>
              <span title="<?= e(($day['date'] ?? '') . ': ' . (int) ($day['count'] ?? 0)) ?>" class="block h-[10px] w-[10px] rounded-sm sm:h-[12px] sm:w-[12px]" style="background: <?= e($color) ?>"></span>
            <?php endforeach; ?>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

  <div class="mt-8 grid gap-6 lg:grid-cols-2">
    <section class="surface p-5 sm:p-8">
      <p class="eyebrow">Monthly activity</p>
      <h2 class="mt-1 heading-3">Contributions over the last 12 months</h2>
      <div class="mt-6 flex items-end gap-2" style="height: 200px">
        <?php foreach ($data['monthly'] ?? [] as $row):
          $height = max(4, (int) round(((int) $row['count'] / $monthMax) * 160));
        ?>
          <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end">
            <span class="mb-1 text-[10px] text-white/50"><?= (int) $row['count'] ?></span>
            <div class="w-full rounded-t-md" style="height: <?= $height ?>px; background: #22d3ee"></div>
            <span class="mt-2 text-[10px] uppercase tracking-wide text-white/45"><?= e($row['label']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="surface p-5 sm:p-8">
      <p class="eyebrow">Language usage</p>
      <h2 class="mt-1 heading-3">Top languages by code size</h2>
      <ul class="mt-6 space-y-3">
        <?php foreach ($data['languages'] ?? [] as $lang): ?>
          <li>
            <div class="flex items-center justify-between text-sm">
              <span class="font-medium text-white"><?= e($lang['name']) ?></span>
              <span class="text-white/50"><?= e((string) $lang['percent']) ?>%</span>
            </div>
            <div class="mt-1 h-2 overflow-hidden rounded-full bg-white/10">
              <div class="h-full rounded-full" style="width: <?= e((string) $lang['percent']) ?>%; background: <?= e($lang['color']) ?>"></div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    </section>
  </div>

  <section class="mt-10">
    <p class="eyebrow">Recent repositories</p>
    <h2 class="mt-1 heading-3">Latest public work</h2>
    <ul class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <?php foreach ($data['recent'] ?? [] as $repo): ?>
        <li>
          <a href="<?= e($repo['url']) ?>" target="_blank" rel="noreferrer noopener" class="surface card-hover block h-full p-5">
            <p class="text-sm font-semibold text-white"><?= e($repo['name']) ?></p>
            <p class="mt-2 line-clamp-3 text-sm text-white/60"><?= e($repo['description'] ?: 'No description provided.') ?></p>
            <p class="mt-4 text-xs text-white/55">
              <?php if (!empty($repo['language'])): ?><span style="color: <?= e(github_color($repo['language'])) ?>"><?= e($repo['language']) ?></span> · <?php endif; ?>
              ★ <?= (int) $repo['stars'] ?> · forks <?= (int) $repo['forks'] ?>
              <?php if (!empty($repo['pushed'])): ?> · updated <?= e(date('j M Y', strtotime($repo['pushed']))) ?><?php endif; ?>
            </p>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="mt-8 text-center text-xs text-white/45">Cached for about 6 hours. Refreshed <?= e($data['generated'] ?? '') ?>.</p>
  </section>
</div>
<?php layout_end(); ?>
