<?php
require __DIR__ . '/backend/bootstrap.php';
$site = site();
$cv = primary_cv();
layout_start($site['title'], $site['description']);
?>
<section class="relative overflow-hidden">
  <div aria-hidden class="absolute inset-0 grid-bg opacity-60"></div>
  <div class="container container-prose relative grid gap-10 py-14 sm:gap-12 sm:py-24 lg:grid-cols-[1.15fr_0.85fr] lg:py-32">
    <div class="order-2 space-y-8 fade-in lg:order-1">
      <span class="pill">
        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-brand opacity-75"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-cyan-brand"></span></span>
        <?= e(content_text('home', 'status')) ?>
      </span>
      <div>
        <p class="text-sm font-medium text-white/55"><?= e(content_text('home', 'greeting')) ?></p>
        <h1 class="mt-1 heading-1 text-balance"><span class="gradient-text"><?= e($site['name']) ?></span>.</h1>
        <p class="mt-3 text-sm font-medium text-white/75 sm:text-base"><?= e($site['tagline']) ?></p>
        <p class="mt-6 max-w-xl text-pretty text-lg leading-relaxed text-white/70 sm:text-xl"><?= e($site['headline']) ?></p>
      </div>
      <div class="grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
        <a href="/projects" class="btn-primary w-full sm:w-auto"><?= e(content_text('home', 'projects_button')) ?> <?= icon('arrow') ?></a>
        <a href="<?= e($cv['pdf']) ?>" class="btn-ghost w-full sm:w-auto" download="<?= e($cv['filename']) ?>"><?= icon('download') ?> <?= e(content_text('home', 'download_button')) ?></a>
      </div>
      <ul class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[11px] font-medium text-white/45 sm:gap-x-5 sm:text-xs">
        <?php foreach ($site['trustLine'] as $i => $item): ?>
          <li class="flex items-center gap-2"><span><?= e($item) ?></span><?php if ($i < count($site['trustLine']) - 1): ?><span aria-hidden class="hidden h-1 w-1 rounded-full bg-white/20 sm:block"></span><?php endif; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="relative order-1 fade-in lg:order-2">
      <div class="relative mx-auto w-full max-w-md lg:max-w-none">
        <div class="media-frame aspect-[4/5] overflow-hidden rounded-3xl border border-white/10 bg-[#0a0e1a]">
          <img src="<?= e($site['portrait']['primary']) ?>" alt="<?= e($site['name']) ?>, Senior Full-Stack Developer, Technical Lead, and Tech Consultant" class="h-full w-full object-cover object-center" />
          <div aria-hidden class="pointer-events-none absolute inset-0 bg-gradient-to-t from-[#060912] via-[#060912]/30 to-transparent"></div>
          <div class="absolute inset-x-4 top-4">
            <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-black/30 px-2.5 py-1 text-[11px] font-medium text-white/90 backdrop-blur-md"><?= icon('pin', 'h-3 w-3 text-cyan-brand') ?> <?= e(content_text('home', 'place')) ?></span>
          </div>
          <div class="absolute inset-x-4 bottom-4 space-y-3 text-white">
            <div>
              <p class="text-sm font-semibold"><?= e($site['name']) ?></p>
              <p class="text-[11px] uppercase tracking-[0.18em] text-cyan-brand"><?= e(content_text('home', 'skill_line')) ?></p>
            </div>
            <ul class="flex flex-wrap gap-1.5">
              <?php foreach (content_lines('home', 'skills') as $tag): ?>
                <li class="rounded-md border border-white/15 bg-white/[0.06] px-2 py-0.5 text-[10px] font-medium text-white/85 backdrop-blur-sm"><?= e($tag) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container container-prose">
    <p class="eyebrow"><?= e(content_text('home', 'track_eyebrow')) ?></p>
    <h2 class="mt-2 heading-2 text-balance"><?= e(content_text('home', 'track_heading')) ?></h2>
    <p class="mt-3 max-w-2xl text-base leading-relaxed text-white/65 sm:text-lg"><?= e(content_text('home', 'track_lead')) ?></p>
    <ul class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
      <?php
      $metrics = content_home_metrics();
      foreach ($metrics as $m): ?>
        <li class="spotlight-card surface card-hover h-full rounded-2xl p-6">
          <span class="grid h-11 w-11 place-items-center rounded-xl border border-cyan-brand/20 bg-cyan-brand/[0.08] text-cyan-brand"><?= icon($m['icon'], 'h-5 w-5') ?></span>
          <p class="mt-6 text-[2.5rem] font-semibold leading-none tracking-tight text-white"><?= e($m['value']) ?></p>
          <p class="mt-3 text-sm font-medium text-white/90"><?= e($m['label']) ?></p>
          <p class="mt-1.5 text-sm leading-relaxed text-white/55"><?= e($m['helper']) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="surface mt-10 p-6 sm:p-8">
      <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand"><?= e(content_text('home', 'clients_label')) ?></p>
      <ul class="mt-5 flex flex-wrap gap-2">
        <?php foreach (content_lines('home', 'clients') as $name): ?>
          <li class="rounded-md border border-white/10 bg-white/[0.03] px-3 py-1.5 text-xs font-medium text-white/75"><?= e($name) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<section class="section">
  <div class="container container-prose">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div class="max-w-2xl">
        <p class="eyebrow"><?= e(content_text('home', 'work_eyebrow')) ?></p>
        <h2 class="mt-2 heading-2 text-balance"><?= e(content_text('home', 'work_heading')) ?></h2>
        <p class="mt-3 text-base leading-relaxed text-white/65 sm:text-lg"><?= e(content_text('home', 'work_lead')) ?></p>
      </div>
      <a href="/projects" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"><?= e(content_text('home', 'work_link')) ?> <?= icon('arrow') ?></a>
    </div>
    <ul class="mt-12 grid gap-6 md:grid-cols-2">
      <?php foreach (featured_projects() as $project): ?>
        <li><?php project_card($project); ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="section">
  <div class="container container-prose">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="eyebrow"><?= e(content_text('home', 'journey_eyebrow')) ?></p>
        <h2 class="mt-2 heading-2"><?= e(content_text('home', 'journey_heading')) ?></h2>
      </div>
      <a href="/about" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white"><?= e(content_text('home', 'journey_link')) ?> <?= icon('arrow') ?></a>
    </div>
    <ol class="mt-10 grid gap-4 sm:grid-cols-2">
      <?php foreach (array_slice(timeline(), 0, 4) as $item): ?>
        <li class="spotlight-card surface card-hover h-full rounded-2xl p-6">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand"><?= e($item['period']) ?></p>
          <h3 class="mt-2 text-lg font-semibold text-white"><?= e($item['company']) ?></h3>
          <p class="mt-1 text-sm font-medium text-white/70"><?= e($item['role']) ?></p>
          <p class="mt-3 text-sm leading-relaxed text-white/60"><?= e($item['description']) ?></p>
        </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>

<section class="section">
  <div class="container container-prose">
    <div class="surface-strong relative overflow-hidden p-7 sm:p-14">
      <div class="relative grid gap-10 lg:grid-cols-[1.4fr_1fr] lg:items-center">
        <div class="max-w-2xl">
          <p class="eyebrow"><?= e(content_text('home', 'cta_eyebrow')) ?></p>
          <h2 class="mt-3 heading-2 text-balance"><?= e(content_text('home', 'cta_heading')) ?></h2>
          <p class="mt-4 text-base leading-relaxed text-white/70 sm:text-lg"><?= e(content_text('home', 'cta_lead')) ?></p>
          <div class="mt-7 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
            <?php if (!empty($site['contact_on'])): ?><a href="/contact" class="btn-primary w-full sm:w-auto"><?= icon('briefcase') ?> Hire Me</a><?php endif; ?>
            <a href="mailto:<?= e($site['email']) ?>" class="btn-ghost w-full sm:w-auto"><?= icon('mail') ?> Send Email</a>
          </div>
        </div>
        <div class="surface p-6 sm:p-7">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand"><?= e(content_text('home', 'cv_label')) ?></p>
          <p class="mt-3 text-base font-semibold text-white"><?= e(content_text('home', 'cv_heading')) ?></p>
          <p class="mt-2 text-sm leading-relaxed text-white/60"><?= e(content_text('home', 'cv_text')) ?></p>
          <div class="mt-5 flex flex-col gap-2.5">
            <a href="<?= e($cv['pdf']) ?>" class="btn-ghost w-full" download="<?= e($cv['filename']) ?>"><?= icon('download') ?> Download CV (PDF)</a>
            <a href="/cvs" class="btn-ghost w-full"><?= icon('file') ?> Browse all CVs</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php layout_end(); ?>
