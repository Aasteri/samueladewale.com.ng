<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
page_guard('projects');
$slug = $_GET['slug'] ?? '';
$project = project_by_slug($slug);
if (!$project) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}
$note = require __DIR__ . '/backend/data/confidential.php';
layout_start($project['name'], $project['summary']);
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-12 pt-12 sm:pt-16">
  <div class="container container-prose relative grid gap-8 lg:grid-cols-[1.05fr_0.95fr] lg:items-center">
    <div>
      <a href="/projects" class="inline-flex items-center gap-2 text-sm font-medium text-white/55 hover:text-white"><?= icon('arrow', 'h-4 w-4 rotate-180') ?> All projects</a>
      <div class="mt-6 flex flex-wrap gap-2">
        <span class="pill"><?= e($project['category']) ?></span>
        <span class="pill"><?= e($project['group']) ?></span>
        <?php if (!empty($project['year'])): ?><span class="pill"><?= e($project['year']) ?></span><?php endif; ?>
        <?php if (!empty($project['confidential'])): ?><span class="pill"><?= icon('lock', 'h-3.5 w-3.5') ?> Confidential</span><?php endif; ?>
      </div>
      <h1 class="mt-4 heading-1 text-balance"><?= e($project['name']) ?></h1>
      <p class="mt-3 text-base font-medium text-cyan-brand">Role: <?= e($project['role']) ?></p>
      <?php if (!empty($project['client'])): ?><p class="mt-1 text-sm text-white/55">Client: <span class="font-medium text-white/85"><?= e($project['client']) ?></span></p><?php endif; ?>
      <p class="mt-4 max-w-3xl lead"><?= e($project['summary']) ?></p>
      <div class="mt-6 flex flex-wrap gap-2">
        <?php foreach ($project['stack'] ?? [] as $s): ?><span class="rounded-md border border-white/10 bg-white/[0.04] px-2.5 py-1 text-xs font-medium text-white/75"><?= e($s) ?></span><?php endforeach; ?>
      </div>
      <?php if (!empty($project['liveUrl'])): ?>
        <div class="mt-6"><a class="btn-primary" href="<?= e($project['liveUrl']) ?>" target="_blank" rel="noreferrer noopener"><?= icon('external') ?> Visit live site</a></div>
      <?php endif; ?>
    </div>
    <div class="media-frame aspect-[16/10] overflow-hidden rounded-2xl border border-white/10 bg-[#0a0e1a]">
      <?php if (!empty($project['imageUrl'])): ?>
        <img src="<?= e($project['imageUrl']) ?>" alt="<?= e($project['name']) ?> screenshot" class="h-full w-full object-cover object-top" />
      <?php else: ?>
        <div class="absolute inset-0 grid-bg opacity-60"></div>
        <div class="relative flex h-full flex-col justify-end p-8 text-white">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand"><?= e($project['category']) ?></p>
          <p class="mt-2 text-2xl font-semibold"><?= e($project['name']) ?></p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<div class="container container-prose py-12">
  <div class="grid gap-6 lg:grid-cols-2">
    <article class="surface p-6 sm:p-8"><h2 class="heading-3">Problem</h2><p class="mt-3 leading-relaxed text-white/65"><?= e($project['problem']) ?></p></article>
    <article class="surface p-6 sm:p-8"><h2 class="heading-3">Solution</h2><p class="mt-3 leading-relaxed text-white/65"><?= e($project['solution']) ?></p></article>
  </div>
  <div class="mt-6 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
    <article class="surface p-6 sm:p-8">
      <h2 class="heading-3">Key features</h2>
      <ul class="mt-4 grid gap-2 sm:grid-cols-2">
        <?php foreach ($project['features'] ?? [] as $f): ?><li class="flex items-start gap-2 text-sm text-white/80"><?= icon('check', 'mt-0.5 h-4 w-4 shrink-0 text-cyan-brand') ?><span><?= e($f) ?></span></li><?php endforeach; ?>
      </ul>
    </article>
    <article class="surface-strong p-6 sm:p-8">
      <h2 class="text-2xl font-semibold tracking-tight text-white">Impact</h2>
      <ul class="mt-4 space-y-2">
        <?php foreach ($project['impact'] ?? [] as $i): ?><li class="flex items-start gap-2 text-sm text-white/85"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-cyan-brand"></span><span><?= e($i) ?></span></li><?php endforeach; ?>
      </ul>
    </article>
  </div>
  <?php if (!empty($project['confidential'])): ?>
    <aside class="surface mt-8 flex items-start gap-4 border-l-4 border-cyan-brand p-6">
      <?= icon('lock', 'mt-1 h-5 w-5 text-cyan-brand') ?>
      <p class="text-sm leading-relaxed text-white/75"><?= e($note) ?></p>
    </aside>
  <?php endif; ?>
  <div class="surface mt-10 flex flex-col items-start justify-between gap-5 p-6 md:flex-row md:items-center">
    <p class="text-sm text-white/65">Want a deeper walkthrough of this project?</p>
    <a href="/contact" class="btn-primary w-full sm:w-auto">Request Project Walkthrough</a>
  </div>
</div>
<?php layout_end(); ?>
