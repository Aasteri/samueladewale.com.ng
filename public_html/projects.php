<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
page_guard('projects');
$site = site();
layout_start('Projects', 'A selected portfolio of enterprise, government, healthtech, and commercial platforms designed, built, and led by Samuel Adewale.');
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-14 pt-12 sm:pb-20 sm:pt-16">
  <div class="container container-prose relative">
    <p class="eyebrow"><?= e(content_text('projects', 'eyebrow')) ?></p>
    <h1 class="mt-3 heading-1 text-balance"><?= e(content_text('projects', 'title')) ?></h1>
    <p class="mt-4 max-w-3xl lead"><?= e(content_text('projects', 'lead')) ?></p>
    <div class="mt-8 grid grid-cols-1 gap-3 sm:flex">
      <?php if (!empty($site['contact_on'])): ?><a href="/contact" class="btn-primary w-full sm:w-auto"><?= e(content_text('projects', 'primary')) ?></a><?php endif; ?>
      <a href="/github-activity" class="btn-ghost w-full sm:w-auto"><?= e(content_text('projects', 'secondary')) ?></a>
    </div>
  </div>
</section>
<div class="container container-prose py-12">
  <?php foreach (project_groups() as $i => $group):
    $items = array_values(array_filter(projects(), fn ($p) => $p['group'] === $group['group']));
  ?>
    <section class="py-10">
      <p class="eyebrow">Category <?= $i + 1 ?></p>
      <h2 class="mt-2 heading-2"><?= e($group['group']) ?></h2>
      <p class="mt-3 max-w-3xl text-white/65"><?= e($group['description']) ?></p>
      <ul class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($items as $project): ?><li><?php project_card($project); ?></li><?php endforeach; ?>
      </ul>
    </section>
  <?php endforeach; ?>
</div>
<?php layout_end(); ?>
