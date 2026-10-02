<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
page_guard('cvs');
$site = site();
layout_start(content_text('cvs', 'title'), 'Download tailored CVs and a portfolio PDF, including full-stack, frontend, technical lead, and consulting versions.');
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-14 pt-12 sm:pb-20 sm:pt-16">
  <div class="container container-prose relative">
    <p class="eyebrow"><?= e(content_text('cvs', 'eyebrow')) ?></p>
    <h1 class="mt-3 heading-1"><?= e(content_text('cvs', 'title')) ?></h1>
    <p class="mt-4 max-w-3xl lead"><?= e(content_text('cvs', 'lead')) ?></p>
  </div>
</section>
<div class="container container-prose py-12">
  <ul class="grid gap-6 md:grid-cols-2">
    <?php foreach (cvs() as $doc): ?>
      <li class="spotlight-card surface card-hover h-full rounded-2xl p-7">
        <div class="grid h-12 w-12 place-items-center rounded-xl border border-white/10 bg-white/[0.04] text-cyan-brand"><?= icon('file', 'h-6 w-6') ?></div>
        <h2 class="mt-5 heading-3"><?= e($doc['title']) ?></h2>
        <p class="mt-2 leading-relaxed text-white/65"><?= e($doc['bestFor']) ?></p>
        <div class="mt-6 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
          <a href="<?= e($doc['pdf']) ?>" class="btn-primary w-full sm:w-auto" download="<?= e($doc['filename']) ?>"><?= icon('download') ?> Download PDF</a>
          <a href="/contact" class="btn-ghost w-full sm:w-auto"><?= icon('mail') ?> Contact Samuel</a>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
  <aside class="surface mt-10 flex flex-col items-start justify-between gap-4 p-6 sm:flex-row sm:items-center">
    <p class="text-sm text-white/65"><?= e(content_text('cvs', 'note')) ?></p>
    <a href="mailto:<?= e($site['email']) ?>" class="btn-primary w-full sm:w-auto"><?= icon('mail') ?> Email Samuel</a>
  </aside>
</div>
<?php layout_end(); ?>
