<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
page_guard('services');
$site = site();
layout_start('Services', 'Full-stack development, technical leadership, enterprise and government platforms, MVP delivery, and technical consulting.');
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-14 pt-12 sm:pb-20 sm:pt-16">
  <div class="container container-prose relative">
    <p class="eyebrow"><?= e(content_text('services', 'eyebrow')) ?></p>
    <h1 class="mt-3 heading-1 text-balance"><?= e(content_text('services', 'title')) ?></h1>
    <p class="mt-4 max-w-3xl lead"><?= e(content_text('services', 'lead')) ?></p>
    <div class="mt-8 grid grid-cols-1 gap-3 sm:flex">
      <?php if (!empty($site['contact_on'])): ?><a href="/contact" class="btn-primary w-full sm:w-auto"><?= e(content_text('services', 'primary')) ?></a><?php endif; ?>
      <a href="/projects" class="btn-ghost w-full sm:w-auto"><?= e(content_text('services', 'secondary')) ?></a>
    </div>
  </div>
</section>
<div class="container container-prose py-12">
  <ul class="grid gap-6 md:grid-cols-2">
    <?php foreach (services() as $service): ?>
      <li class="spotlight-card surface card-hover h-full rounded-2xl p-7">
        <div class="grid h-12 w-12 place-items-center rounded-xl border border-white/10 bg-white/[0.04] text-cyan-brand"><?= icon($service['icon'], 'h-6 w-6') ?></div>
        <h2 class="mt-5 heading-3"><?= e($service['title']) ?></h2>
        <p class="mt-2 leading-relaxed text-white/65"><?= e($service['summary']) ?></p>
        <ul class="mt-5 space-y-2">
          <?php foreach ($service['bullets'] ?? [] as $b): ?>
            <li class="flex items-start gap-2 text-sm text-white/80"><?= icon('check', 'mt-0.5 h-4 w-4 shrink-0 text-cyan-brand') ?><span><?= e($b) ?></span></li>
          <?php endforeach; ?>
        </ul>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
<?php layout_end(); ?>
