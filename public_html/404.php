<?php
if (!function_exists('layout_start')) {
    require __DIR__ . '/backend/bootstrap.php';
}
http_response_code(404);
layout_start('Page not found', 'The page you requested was not found.');
?>
<div class="container container-prose grid min-h-[60vh] place-items-center py-20 text-center">
  <div>
    <p class="font-mono text-sm uppercase tracking-[0.18em] text-cyan-brand">404</p>
    <h1 class="mt-3 heading-1">This page wasn't found.</h1>
    <p class="mt-3 text-white/65">The link may be broken or the page may have moved.</p>
    <div class="mt-8 grid grid-cols-1 gap-3 sm:flex sm:justify-center">
      <a href="/" class="btn-primary w-full sm:w-auto">Go home</a>
      <a href="/projects" class="btn-ghost w-full sm:w-auto">View Projects</a>
      <a href="/contact" class="btn-ghost w-full sm:w-auto">Contact Me</a>
    </div>
  </div>
</div>
<?php layout_end(); ?>
