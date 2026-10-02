<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
page_guard('about');
$site = site();
layout_start('About', 'Samuel Adewale is a software developer and technical consultant based in Abuja, Nigeria.');
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-14 pt-12 sm:pb-20 sm:pt-16">
  <div aria-hidden class="grid-bg pointer-events-none absolute inset-0"></div>
  <div class="container container-prose relative">
    <p class="eyebrow"><?= e(content_text('about', 'eyebrow')) ?></p>
    <h1 class="mt-3 heading-1 text-balance"><?= e(content_text('about', 'title')) ?></h1>
    <p class="mt-4 max-w-3xl lead text-pretty"><?= e(content_text('about', 'lead')) ?></p>
    <div class="mt-8 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
      <a href="/projects" class="btn-primary w-full sm:w-auto">View My Projects</a>
      <a href="/contact" class="btn-ghost w-full sm:w-auto">Contact Me</a>
    </div>
  </div>
</section>
<section class="section">
  <div class="container container-prose grid gap-10 lg:grid-cols-[0.9fr_1.1fr]">
    <div class="space-y-6">
      <div class="media-frame mx-auto aspect-[4/5] w-full max-w-sm overflow-hidden rounded-3xl border border-white/10 bg-[#0a0e1a]">
        <img src="<?= e($site['portrait']['secondary']) ?>" alt="Portrait of <?= e($site['name']) ?>" class="h-full w-full object-cover object-center" />
        <div class="absolute inset-x-4 bottom-4 text-white">
          <p class="text-sm font-semibold"><?= e($site['name']) ?></p>
          <p class="text-xs text-white/70"><?= e($site['tagline']) ?></p>
        </div>
      </div>
      <div class="surface-strong p-6 sm:p-8">
        <p class="eyebrow"><?= e(content_text('about', 'diff_eyebrow')) ?></p>
        <ul class="mt-4 space-y-3">
          <?php
          $pointIcons = ['server', 'briefcase', 'mail', 'target', 'spark', 'shield'];
          foreach (content_lines('about', 'points') as $i => $point):
            $row = [$pointIcons[$i % count($pointIcons)], $point];
          ?>
            <li class="flex items-start gap-3">
              <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-white/5 text-cyan-brand"><?= icon($row[0], 'h-4 w-4') ?></span>
              <span class="text-sm leading-relaxed text-white/85"><?= e($row[1]) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <div class="surface p-6 sm:p-8">
      <p class="eyebrow"><?= e(content_text('about', 'who_eyebrow')) ?></p>
      <h2 class="mt-2 heading-2"><?= e(content_text('about', 'who_title')) ?></h2>
      <p class="mt-4 leading-relaxed text-white/70"><?= e(content_text('about', 'who_1')) ?></p>
      <p class="mt-4 leading-relaxed text-white/70"><?= e(content_text('about', 'who_2')) ?></p>
      <p class="mt-4 leading-relaxed text-white/70"><?= e(content_text('about', 'who_3')) ?></p>
    </div>
  </div>
</section>
<section class="section">
  <div class="container container-prose">
    <p class="eyebrow"><?= e(content_text('about', 'timeline_eyebrow')) ?></p>
    <h2 class="mt-2 heading-2"><?= e(content_text('about', 'timeline_title')) ?></h2>
    <ol class="relative mt-12 space-y-4">
      <?php foreach (timeline() as $item): ?>
        <li class="surface p-6">
          <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand"><?= e($item['period']) ?></p>
          <h3 class="mt-1 text-lg font-semibold text-white"><?= e($item['company']) ?></h3>
          <p class="text-sm font-medium text-white/70"><?= e($item['role']) ?></p>
          <p class="mt-3 text-sm leading-relaxed text-white/60"><?= e($item['description']) ?></p>
          <ul class="mt-3 grid gap-1.5 text-sm text-white/75 sm:grid-cols-2">
            <?php foreach ($item['highlights'] ?? [] as $h): ?><li><?= e($h) ?></li><?php endforeach; ?>
          </ul>
        </li>
      <?php endforeach; ?>
    </ol>
    <ul class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
      <?php foreach (certifications() as $c): ?>
        <li class="surface flex items-start gap-3 p-5">
          <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand"><?= icon('award', 'h-4 w-4') ?></span>
          <p class="text-sm font-medium text-white"><?= e($c) ?></p>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>
<?php layout_end(); ?>
