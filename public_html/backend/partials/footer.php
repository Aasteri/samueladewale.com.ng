<?php $site = site(); $year = (int) date('Y'); ?>
<footer class="mt-16 border-t border-white/[0.06] bg-[#040710] text-white sm:mt-24">
  <div class="container container-prose py-10 sm:py-14">
    <div class="grid gap-10 sm:grid-cols-2 md:grid-cols-3">
      <div class="space-y-3">
        <p class="text-lg font-semibold"><?= e($site['name']) ?></p>
        <p class="text-sm text-white/70"><?= e($site['tagline']) ?></p>
        <p class="inline-flex items-center gap-2 text-sm text-white/60"><?= icon('pin', 'h-4 w-4') ?> <?= e($site['location']) ?></p>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">Quick links</p>
        <ul class="mt-4 space-y-2 text-sm text-white/75">
          <?php foreach ($site['footerLinks'] as $link): ?>
            <li><a class="transition-colors hover:text-white" href="<?= e($link['href']) ?>"><?= e($link['label']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div>
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">Connect</p>
        <ul class="mt-4 space-y-2 text-sm text-white/75">
          <li><a class="inline-flex max-w-full items-start gap-2 break-all hover:text-white" href="mailto:<?= e($site['email']) ?>"><?= icon('mail', 'mt-1 h-4 w-4 shrink-0') ?><span><?= e($site['email']) ?></span></a></li>
          <li><a class="inline-flex items-center gap-2 hover:text-white" href="<?= e($site['linkedin']) ?>" target="_blank" rel="noreferrer noopener"><?= icon('linkedin', 'h-4 w-4') ?> LinkedIn</a></li>
          <li><a class="inline-flex items-center gap-2 hover:text-white" href="https://github.com/<?= e($site['github']) ?>" target="_blank" rel="noreferrer noopener"><?= icon('github', 'h-4 w-4') ?> GitHub</a></li>
        </ul>
      </div>
    </div>
    <div class="mt-12 flex flex-col items-start justify-between gap-3 border-t border-white/10 pt-6 text-xs text-white/60 sm:flex-row sm:items-center">
      <p>© <?= $year ?> Samuel Adewale. All rights reserved.</p>
    </div>
  </div>
</footer>
