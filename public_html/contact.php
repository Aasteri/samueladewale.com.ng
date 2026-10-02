<?php
require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';
page_guard('contact');
$site = site();
layout_start('Contact', 'Get in touch with Samuel Adewale for roles, partnerships, consulting, or technical leadership opportunities.');
$rows = [
  ['mail', 'Email', $site['email'], 'mailto:' . $site['email']],
  ['phone', 'Phone', $site['phone'], 'tel:' . preg_replace('/\s+/', '', $site['phone'])],
  ['linkedin', 'LinkedIn', 'linkedin.com/in/samueladewale', $site['linkedin']],
  ['github', 'GitHub', 'github.com/' . $site['github'], 'https://github.com/' . $site['github']],
  ['pin', 'Location', $site['location'], null],
  ['clock', 'Availability', $site['availability'], null],
];
?>
<section class="relative overflow-hidden border-b border-white/[0.06] pb-14 pt-12 sm:pb-20 sm:pt-16">
  <div class="container container-prose relative">
    <p class="eyebrow"><?= e(content_text('contact', 'eyebrow')) ?></p>
    <h1 class="mt-3 heading-1"><?= e(content_text('contact', 'title')) ?></h1>
    <p class="mt-4 max-w-3xl lead"><?= e(content_text('contact', 'lead')) ?></p>
  </div>
</section>
<div class="container container-prose py-12">
  <div class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
    <form id="contact-form" class="surface relative p-5 sm:p-7" novalidate>
      <div aria-hidden="true" class="absolute -left-[9999px] h-0 w-0 overflow-hidden">
        <label for="company_website">Leave this field empty</label>
        <input id="company_website" name="company_website" type="text" tabindex="-1" autocomplete="off" value="" />
      </div>
      <div class="grid gap-4 sm:grid-cols-2">
        <?php foreach ([['name','Name','text','name',true],['email','Email','email','email',true],['company','Company','text','organization',false],['opportunity','Role or opportunity type','text','',false]] as $f): ?>
          <div class="<?= $f[0] === 'opportunity' ? '' : '' ?>">
            <label for="f-<?= e($f[0]) ?>" class="block text-sm font-medium text-white"><?= e($f[1]) ?> <?php if ($f[4]): ?><span class="text-cyan-brand">*</span><?php endif; ?></label>
            <input id="f-<?= e($f[0]) ?>" name="<?= e($f[0]) ?>" type="<?= e($f[2]) ?>" <?= $f[4] ? 'required' : '' ?> <?= $f[3] ? 'autocomplete="' . e($f[3]) . '"' : '' ?> class="mt-1.5 h-12 w-full rounded-xl border border-white/10 bg-white/[0.04] px-3.5 text-base text-white placeholder:text-white/40 focus:border-cyan-brand/60 focus:outline-none focus:ring-2 focus:ring-cyan-brand/30 sm:h-11 sm:text-sm" />
          </div>
        <?php endforeach; ?>
      </div>
      <div class="mt-4">
        <label for="message" class="block text-sm font-medium text-white">Message <span class="text-cyan-brand">*</span></label>
        <textarea id="message" name="message" required rows="6" class="mt-1.5 w-full resize-y rounded-xl border border-white/10 bg-white/[0.04] px-3.5 py-3 text-base text-white placeholder:text-white/40 focus:border-cyan-brand/60 focus:outline-none focus:ring-2 focus:ring-cyan-brand/30 sm:text-sm" placeholder="Tell me a little about your team, project, or role…"></textarea>
      </div>
      <div class="mt-6 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:justify-between">
        <button type="submit" class="btn-primary w-full sm:w-auto"><?= icon('mail') ?> <span><?= e(content_text('contact', 'button')) ?></span></button>
        <p id="form-status" class="text-sm text-white/60" role="status"></p>
      </div>
    </form>
    <aside class="space-y-4">
      <div class="surface flex items-center gap-4 p-5">
        <div class="media-frame h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-white/10 bg-[#0a0e1a]">
          <img src="<?= e($site['portrait']['primary']) ?>" alt="" class="h-full w-full object-cover object-center" />
        </div>
        <div class="min-w-0">
          <p class="truncate text-sm font-semibold text-white"><?= e($site['name']) ?></p>
          <p class="truncate text-xs text-cyan-brand"><?= e($site['tagline']) ?></p>
          <p class="mt-1 text-[11px] text-white/55">Usually replies within a day</p>
        </div>
      </div>
      <?php foreach ($rows as $row): ?>
        <?php $inner = '<div class="surface flex items-start gap-4 p-5"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand">' . icon($row[0], 'h-5 w-5') . '</span><div class="min-w-0 flex-1"><p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">' . e($row[1]) . '</p><p class="mt-1 break-words text-sm font-medium text-white">' . e($row[2]) . '</p></div></div>'; ?>
        <?php if ($row[3]): ?><a class="block transition-transform hover:-translate-y-0.5" href="<?= e($row[3]) ?>" <?= str_starts_with($row[3], 'http') ? 'target="_blank" rel="noreferrer noopener"' : '' ?>><?= $inner ?></a><?php else: ?><?= $inner ?><?php endif; ?>
      <?php endforeach; ?>
    </aside>
  </div>
</div>
<?php layout_end(); ?>
