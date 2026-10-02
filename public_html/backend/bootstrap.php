<?php
declare(strict_types=1);

$ROOT = dirname(__DIR__);
$CONFIG_FILE = __DIR__ . '/config.php';
$CONFIG = is_file($CONFIG_FILE) ? require $CONFIG_FILE : [];

function cfg(string $key, ?string $default = null): ?string
{
    global $CONFIG;
    $value = $CONFIG[$key] ?? $default;
    if ($value === null || $value === '') {
        return $default;
    }
    return (string) $value;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function site(): array
{
    static $site;
    if ($site) {
        return $site;
    }
    require_once __DIR__ . '/lib/content.php';
    $site = [
        'name' => 'Samuel Adewale',
        'title' => 'Samuel Adewale | Senior Full-Stack Developer & Tech Consultant',
        'description' => 'Portfolio of Samuel Adewale, a senior full-stack developer, technical lead, and tech consultant based in Abuja, Nigeria, experienced in enterprise, government, healthtech, ERP, and digital transformation platforms.',
        'url' => rtrim(cfg('site_url', 'https://samueladewale.com.ng'), '/'),
        'email' => cfg('contact_email', 'hello@samueladewale.com.ng'),
        'phone' => '+234 813 423 7131',
        'location' => 'Abuja, Nigeria',
        'github' => cfg('github_username', 'aasteri'),
        'linkedin' => 'https://www.linkedin.com/in/samueladewale',
        'availability' => 'Remote, Hybrid, Contract, Full-time, Consulting',
        'tagline' => 'Senior Full-Stack Developer, Technical Lead, Tech Consultant',
        'headline' => 'I build web and mobile platforms for enterprise, government, and healthtech teams. From national portals and ERPs to healthtech MVPs.',
        'portrait' => [
            'primary' => '/img/Adewale_Samuel_Professional_Shot_2.jpg.jpeg',
            'secondary' => '/img/Adewale_Samuel_Professional_Shot.jpg.jpeg',
        ],
        'trustLine' => ['7+ years', 'Enterprise & government', 'Web & mobile delivery'],
        'navigation' => [
            ['href' => '/', 'label' => 'Home'],
            ['href' => '/about', 'label' => 'About'],
            ['href' => '/projects', 'label' => 'Projects'],
            ['href' => '/github-activity', 'label' => 'GitHub Activity'],
            ['href' => '/services', 'label' => 'Services'],
            ['href' => '/cvs', 'label' => 'CVs'],
            ['href' => '/contact', 'label' => 'Contact'],
        ],
        'footerLinks' => [
            ['href' => '/projects', 'label' => 'Projects'],
            ['href' => '/github-activity', 'label' => 'GitHub Activity'],
            ['href' => '/cvs', 'label' => 'CVs'],
            ['href' => '/contact', 'label' => 'Contact'],
        ],
    ];
    foreach (['name', 'title', 'description', 'tagline', 'headline', 'phone', 'email', 'location', 'availability', 'linkedin', 'github'] as $key) {
        $site[$key] = content_text('site', $key, $site[$key]);
    }
    $site['portrait']['primary'] = content_text('site', 'portrait_primary', $site['portrait']['primary']);
    $site['portrait']['secondary'] = content_text('site', 'portrait_secondary', $site['portrait']['secondary']);
    $site['trustLine'] = content_lines('site', 'trust', $site['trustLine']);
    $site['navigation'] = array_values(array_filter($site['navigation'], fn ($item) => content_href_on($item['href'])));
    $site['footerLinks'] = array_values(array_filter($site['footerLinks'], fn ($item) => content_href_on($item['href'])));
    $site['contact_on'] = content_enabled('contact');
    return $site;
}

function current_path(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if ($path !== '/') {
        $path = rtrim($path, '/');
    }
    return $path === '' ? '/' : $path;
}

function nav_active(string $href): bool
{
    $path = current_path();
    if ($href === '/') {
        return $path === '/';
    }
    return $path === $href || str_starts_with($path, $href . '/');
}

function icon(string $name, string $class = 'h-4 w-4'): string
{
    $paths = [
        'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'download' => '<path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"/>',
        'mail' => '<path d="M4 6h16v12H4z"/><path d="M4 7l8 6 8-6"/>',
        'pin' => '<path d="M12 21s7-6.2 7-11a7 7 0 10-14 0c0 4.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'chevron' => '<path d="M6 9l6 6 6-6"/>',
        'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'x' => '<path d="M6 6l12 12M18 6L6 18"/>',
        'github' => '<path d="M9 19c-4 1.5-4-2.5-6-3m12 6v-3.9a3.4 3.4 0 00-.9-2.6c3-.3 6.1-1.5 6.1-6.6a5.2 5.2 0 00-1.4-3.6 4.8 4.8 0 00-.1-3.6s-1.1-.3-3.5 1.3a12.1 12.1 0 00-6.2 0C6.4 2.4 5.3 2.7 5.3 2.7a4.8 4.8 0 00-.1 3.6A5.2 5.2 0 003.8 9.9c0 5.1 3.1 6.3 6.1 6.6a3.4 3.4 0 00-.9 2.6V22"/>',
        'linkedin' => '<path d="M6 9H3v12h3zM4.5 3A1.8 1.8 0 104.5 6.6 1.8 1.8 0 004.5 3zM21 21h-3v-6.2c0-1.8-.8-2.4-1.8-2.4s-2 .9-2 2.5V21h-3V9h3v1.6c.6-.9 1.8-1.8 3.6-1.8 2.4 0 4.2 1.6 4.2 4.9z"/>',
        'phone' => '<path d="M6 3h3l1.5 4-2 1.5a12 12 0 006 6L16 13l4 1.5V18a2 2 0 01-2 2A15 15 0 014 5a2 2 0 012-2z"/>',
        'clock' => '<circle cx="12" cy="12" r="8"/><path d="M12 8v5l3 2"/>',
        'lock' => '<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>',
        'external' => '<path d="M14 5h5v5M19 5l-8 8M10 5H6a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1v-4"/>',
        'check' => '<path d="M5 12l5 5L20 7"/>',
        'file' => '<path d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M14 3v6h6"/>',
        'award' => '<circle cx="12" cy="9" r="5"/><path d="M8.5 13.5L7 21l5-2 5 2-1.5-7.5"/>',
        'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>',
        'server' => '<rect x="3" y="4" width="18" height="6" rx="1"/><rect x="3" y="14" width="18" height="6" rx="1"/><path d="M7 7h.01M7 17h.01"/>',
        'building' => '<path d="M4 21V5a1 1 0 011-1h8a1 1 0 011 1v16M14 9h5a1 1 0 011 1v11M8 8h2M8 12h2M8 16h2M17 13h2M17 17h2"/>',
        'shield' => '<path d="M12 3l8 3v6c0 5-3.4 8.4-8 9-4.6-.6-8-4-8-9V6l8-3z"/>',
        'phone2' => '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>',
        'target' => '<circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="4"/><circle cx="12" cy="12" r="1"/>',
        'flow' => '<rect x="3" y="3" width="6" height="6" rx="1"/><rect x="15" y="15" width="6" height="6" rx="1"/><path d="M6 9v3a3 3 0 003 3h6"/>',
        'spark' => '<path d="M12 3l1.5 5.5L19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/>',
    ];
    $d = $paths[$name] ?? $paths['spark'];
    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}

function projects(): array
{
    static $rows;
    if ($rows === null) {
        require_once __DIR__ . '/lib/catalog.php';
        $rows = catalog_merge(require __DIR__ . '/data/projects.php');
    }
    return $rows;
}

function project_groups(): array
{
    static $rows;
    if ($rows === null) {
        $rows = require __DIR__ . '/data/groups.php';
    }
    return $rows;
}

function project_by_slug(string $slug): ?array
{
    foreach (projects() as $project) {
        if ($project['slug'] === $slug) {
            return $project;
        }
    }
    return null;
}

function featured_projects(): array
{
    $preferred = ['national-quality-council', 'the-tailors-market', 'propa3'];
    $rows = array_values(array_filter(projects(), fn ($p) => !empty($p['featured'])));
    $front = [];
    $rest = [];
    foreach ($rows as $project) {
        if (in_array($project['slug'], $preferred, true)) {
            $front[$project['slug']] = $project;
        } else {
            $rest[] = $project;
        }
    }
    $ordered = [];
    foreach ($preferred as $slug) {
        if (isset($front[$slug])) {
            $ordered[] = $front[$slug];
        }
    }
    return array_merge($ordered, $rest);
}

function cvs(): array
{
    require_once __DIR__ . '/lib/content.php';
    $saved = content_list('cvs');
    if ($saved) {
        return $saved;
    }
    return [
        [
            'title' => 'Senior Full-Stack Developer CV',
            'bestFor' => 'Best for software engineer, full-stack developer, backend/frontend hybrid, and web application roles.',
            'pdf' => '/cvs/Samuel_Adewale_Senior_Full_Stack_Developer_CV.pdf',
            'filename' => 'Samuel_Adewale_Senior_Full_Stack_Developer_CV.pdf',
        ],
        [
            'title' => 'Senior Frontend / React Developer CV',
            'bestFor' => 'Best for React, Next.js, frontend engineer, UI engineer, and product engineer roles.',
            'pdf' => '/cvs/Samuel_Adewale_Senior_Frontend_React_Developer_CV.pdf',
            'filename' => 'Samuel_Adewale_Senior_Frontend_React_Developer_CV.pdf',
        ],
        [
            'title' => 'Technical Lead / Solutions Architect CV',
            'bestFor' => 'Best for technical lead, engineering manager, solutions architect, and platform leadership roles.',
            'pdf' => '/cvs/Samuel_Adewale_Technical_Lead_Solutions_Architect_CV.pdf',
            'filename' => 'Samuel_Adewale_Technical_Lead_Solutions_Architect_CV.pdf',
        ],
        [
            'title' => 'Tech Consultant / Technical Project Manager CV',
            'bestFor' => 'Best for technical consulting, technical project manager, delivery lead, and transformation roles.',
            'pdf' => '/cvs/Samuel_Adewale_Tech_Consultant_Technical_Project_Manager_CV.pdf',
            'filename' => 'Samuel_Adewale_Tech_Consultant_Technical_Project_Manager_CV.pdf',
        ],
        [
            'title' => 'Executive Tech Portfolio PDF',
            'bestFor' => 'High-level portfolio summary of projects, impact, and capabilities, ideal for executives and decision-makers.',
            'pdf' => '/cvs/Samuel_Adewale_Executive_Tech_Portfolio.pdf',
            'filename' => 'Samuel_Adewale_Executive_Tech_Portfolio.pdf',
        ],
    ];
}

function primary_cv(): array
{
    return cvs()[0];
}

function timeline(): array
{
    require_once __DIR__ . '/lib/content.php';
    $saved = content_list('timeline');
    if ($saved) {
        return $saved;
    }
    return [
        ['company' => 'Underwired Nigeria Ltd.', 'role' => 'Senior Full-Stack Developer / Technical Lead', 'period' => 'Most recent', 'description' => 'Led delivery of enterprise and government platforms across ERP, HSE, compliance, and digital transformation programmes.', 'highlights' => ['Technical leadership across cross-functional teams', 'Architecture and code review on critical platforms', 'Stakeholder engagement with public-sector clients']],
        ['company' => 'Bisly Nigeria Ltd.', 'role' => 'Full-Stack Developer', 'period' => 'Previous', 'description' => 'Delivered corporate platforms, internal tools, and operational dashboards.', 'highlights' => ['Built dashboards and admin systems end-to-end', 'Integrated third-party APIs into business workflows']],
        ['company' => 'PYRICH Group Nigeria Limited', 'role' => 'Software Developer', 'period' => 'Previous', 'description' => 'Implemented commercial web platforms and supporting back-office systems.', 'highlights' => ['Frontend and backend delivery for client projects', 'Performance, accessibility, and SEO improvements']],
        ['company' => 'Foreshield Nigeria', 'role' => 'Software Developer', 'period' => 'Previous', 'description' => 'Built and maintained business-critical web platforms.', 'highlights' => ['Hands-on full-stack delivery', 'Production support and platform reliability']],
        ['company' => 'EDSINE Technologies', 'role' => 'Software Developer', 'period' => 'Previous', 'description' => 'Worked on web and mobile products across multiple verticals.', 'highlights' => ['Web and mobile application delivery', 'Cross-functional collaboration']],
        ['company' => 'Apeke Couture', 'role' => 'Software Developer', 'period' => 'Previous', 'description' => "Delivered the brand's digital storefront and supporting tooling.", 'highlights' => ['Storefront development', 'Foundational tooling and operations']],
    ];
}

function certifications(): array
{
    require_once __DIR__ . '/lib/content.php';
    $saved = content_list('certifications');
    if ($saved) {
        return $saved;
    }
    return [
        'Google Project Management Professional Certificate',
        'Machine Learning Specialization',
        'Meta JavaScript Developer Professional Certificate',
        'Meta Social Media Marketing',
        'Front-End Web Development',
    ];
}

function services(): array
{
    require_once __DIR__ . '/lib/content.php';
    $saved = content_list('services');
    if ($saved) {
        return $saved;
    }
    return [
        ['icon' => 'server', 'title' => 'Full-Stack Web Application Development', 'summary' => 'React, Next.js, Node.js, Laravel, APIs, dashboards, portals, and admin systems.', 'bullets' => ['Production-grade Next.js / React applications', 'Robust Node.js and Laravel APIs', 'Operational dashboards, portals, and admin systems', 'Authentication, RBAC, and audit-friendly architectures']],
        ['icon' => 'phone2', 'title' => 'Mobile App Development', 'summary' => 'Cross-platform mobile apps for iOS and Android, including Flutter, React Native, offline-first field tools, and customer apps.', 'bullets' => ['Cross-platform Flutter and React Native delivery', 'Offline-first sync for field and low-connectivity use cases', 'Integrations with REST APIs, payments, push notifications', 'App Store and Play Store release engineering']],
        ['icon' => 'target', 'title' => 'Technical Leadership and Architecture', 'summary' => 'System design, code review, team mentorship, delivery planning, and architecture documentation.', 'bullets' => ['System architecture and technical decision support', 'Code review and engineering standards', 'Delivery planning and milestone management', 'Mentorship and knowledge transfer to client teams']],
        ['icon' => 'building', 'title' => 'Enterprise and Government Digital Platforms', 'summary' => 'Workflow systems, compliance platforms, regulatory portals, ERP modules, and data visibility tools.', 'bullets' => ['Workflow and approval systems', 'Compliance and regulatory portals', 'ERP-style modules and back-office automation', 'Reporting, analytics, and data visibility tooling']],
        ['icon' => 'spark', 'title' => 'MVP Development', 'summary' => 'Fast product validation for startups, healthtech, fintech, and internal business tools.', 'bullets' => ['From idea to working MVP under tight deadlines', 'Lean, focused scoping and prioritisation', "Stack choices that won't block scale later", 'Hand-off and training for internal teams']],
        ['icon' => 'award', 'title' => 'Technical Consulting', 'summary' => 'Requirements gathering, digital transformation planning, system audits, and client training.', 'bullets' => ['Requirements and stakeholder workshops', 'Digital transformation roadmaps', 'System audits and architecture reviews', 'Hands-on client and end-user training']],
    ];
}

function layout_start(string $title, string $description): void
{
    $site = site();
    $full = $title === $site['title'] ? $title : $title . ' | ' . $site['name'];
    $path = current_path();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= e($full) ?></title>
  <meta name="description" content="<?= e($description) ?>" />
  <link rel="canonical" href="<?= e($site['url'] . ($path === '/' ? '/' : $path)) ?>" />
  <meta name="theme-color" content="#060912" />
  <meta property="og:title" content="<?= e($full) ?>" />
  <meta property="og:description" content="<?= e($description) ?>" />
  <meta property="og:type" content="website" />
  <link rel="stylesheet" href="/assets/app.css" />
  <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml" />
</head>
<body class="min-h-screen font-sans antialiased">
<a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-md focus:bg-cyan-brand focus:px-3 focus:py-2 focus:text-sm focus:text-navy-800">Skip to content</a>
<?php require __DIR__ . '/partials/header.php'; ?>
<main id="main">
    <?php
}

function layout_end(): void
{
    echo '</main>';
    require __DIR__ . '/partials/footer.php';
    echo '<script src="/assets/site.js?v=3" defer></script></body></html>';
}

function project_card(array $project): void
{
    $initials = strtoupper(implode('', array_map(fn ($w) => $w[0] ?? '', array_slice(preg_split('/\s|-/', $project['name']) ?: [], 0, 2))));
    ?>
    <a href="/projects/<?= e($project['slug']) ?>" class="spotlight-card surface card-hover flex h-full flex-col overflow-hidden rounded-2xl" aria-label="View case study: <?= e($project['name']) ?>">
      <?php if (!empty($project['imageUrl'])): ?>
        <div class="media-frame aspect-[16/10] w-full overflow-hidden bg-[#0a0e1a]">
          <img src="<?= e($project['imageUrl']) ?>" alt="Screenshot of <?= e($project['name']) ?>" class="h-full w-full object-cover object-top opacity-90" />
          <div aria-hidden class="absolute inset-0 bg-gradient-to-t from-[#060912]/85 via-[#060912]/10 to-transparent"></div>
        </div>
      <?php else: ?>
        <div class="media-frame aspect-[16/10] w-full overflow-hidden bg-[#0a0e1a]">
          <div aria-hidden class="absolute inset-0 grid-bg opacity-60"></div>
          <div class="relative flex h-full items-center justify-between gap-4 p-6 text-white">
            <div>
              <p class="text-[10px] font-semibold uppercase tracking-[0.18em] text-cyan-brand"><?= e($project['category']) ?></p>
              <p class="mt-2 text-lg font-semibold leading-tight"><?= e($project['name']) ?></p>
            </div>
            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-xl border border-white/15 bg-white/5 font-mono text-sm text-cyan-brand"><?= e($initials) ?></span>
          </div>
        </div>
      <?php endif; ?>
      <div class="flex flex-1 flex-col p-6">
        <div class="flex items-start justify-between gap-3">
          <span class="pill"><?= e($project['category']) ?></span>
          <?= icon('external', 'h-5 w-5 text-white/40') ?>
        </div>
        <h3 class="mt-4 heading-3"><?= e($project['name']) ?></h3>
        <?php if (!empty($project['liveUrl'])): ?>
          <p class="mt-1 text-xs font-semibold text-cyan-brand"><?= e($project['previewLabel'] ?: $project['liveUrl']) ?></p>
        <?php endif; ?>
        <p class="mt-3 text-sm leading-relaxed text-white/60"><?= e($project['summary']) ?></p>
        <div class="mt-5 flex flex-wrap gap-1.5">
          <?php foreach (array_slice($project['stack'] ?? [], 0, 3) as $chip): ?>
            <span class="rounded-md border border-white/10 bg-white/[0.04] px-2 py-0.5 text-[11px] font-medium text-white/75"><?= e($chip) ?></span>
          <?php endforeach; ?>
        </div>
        <div class="mt-auto flex items-center justify-between pt-6 text-sm">
          <span class="inline-flex items-center gap-1 font-semibold text-white/85">View case study <?= icon('arrow', 'h-4 w-4') ?></span>
          <?php if (!empty($project['confidential'])): ?>
            <span class="inline-flex items-center gap-1 text-xs text-white/45"><?= icon('lock', 'h-3.5 w-3.5') ?> Confidential</span>
          <?php elseif (!empty($project['liveUrl'])): ?>
            <span class="inline-flex items-center gap-1 text-xs text-cyan-brand"><?= icon('external', 'h-3.5 w-3.5') ?> <?= e($project['previewLabel'] ?: 'Live site') ?></span>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php
}
