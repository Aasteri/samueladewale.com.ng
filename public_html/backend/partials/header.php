<?php $site = site(); ?>
<header id="site-header" class="sticky top-0 z-50 bg-transparent transition-all duration-300">
  <div class="container container-prose flex h-16 items-center justify-between gap-3 sm:h-20 sm:gap-4">
    <a href="/" class="group flex min-w-0 items-center gap-2" aria-label="Samuel Adewale, home">
      <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.04] font-mono text-sm font-semibold text-cyan-brand">SA</span>
      <span class="flex min-w-0 flex-col leading-tight">
        <span class="truncate text-sm font-semibold text-white">Samuel Adewale</span>
        <span class="hidden truncate text-[11px] font-medium uppercase tracking-[0.14em] text-white/45 sm:inline">Full-Stack · Lead · Consultant</span>
      </span>
    </a>
    <nav class="hidden lg:block" aria-label="Primary">
      <ul class="flex items-center gap-1">
        <?php foreach ($site['navigation'] as $item): $active = nav_active($item['href']); ?>
          <li>
            <a href="<?= e($item['href']) ?>" class="relative inline-flex h-9 items-center rounded-lg px-3 text-sm font-medium transition-colors <?= $active ? 'text-white' : 'text-white/60 hover:text-white' ?>">
              <?= e($item['label']) ?>
              <?php if ($active): ?><span class="absolute inset-x-3 -bottom-px h-0.5 rounded-full bg-cyan-brand"></span><?php endif; ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <?php if (!empty($site['contact_on'])): ?><div class="hidden lg:block"><a href="/contact" class="btn-primary">Hire Me</a></div><?php endif; ?>
    <button type="button" id="menu-toggle" aria-label="Open menu" aria-expanded="false" class="grid h-10 w-10 place-items-center rounded-lg border border-white/15 bg-white/[0.04] text-white lg:hidden">
      <?= icon('menu', 'h-5 w-5') ?>
    </button>
  </div>
  <div id="mobile-nav" class="hidden lg:hidden">
    <div class="container container-prose pb-4">
      <nav class="surface flex flex-col gap-1 p-3" aria-label="Mobile primary">
        <?php foreach ($site['navigation'] as $item): $active = nav_active($item['href']); ?>
          <a href="<?= e($item['href']) ?>" class="rounded-lg px-3 py-2 text-sm font-medium <?= $active ? 'bg-white/[0.06] text-white' : 'text-white/65 hover:bg-white/[0.04] hover:text-white' ?>"><?= e($item['label']) ?></a>
        <?php endforeach; ?>
        <?php if (!empty($site['contact_on'])): ?><a href="/contact" class="btn-primary mt-2">Hire Me</a><?php endif; ?>
      </nav>
    </div>
  </div>
</header>
