<?php
declare(strict_types=1);

function content_file(): string
{
    return dirname(__DIR__) . '/storage/content.json';
}

function content_store(): array
{
    if (isset($GLOBALS['__content']) && is_array($GLOBALS['__content'])) {
        return $GLOBALS['__content'];
    }
    $store = ['enabled' => [], 'pages' => [], 'lists' => []];
    if (is_file(content_file())) {
        $decoded = json_decode((string) file_get_contents(content_file()), true);
        if (is_array($decoded)) {
            $store = array_replace_recursive($store, $decoded);
        }
    }
    $GLOBALS['__content'] = $store;
    return $store;
}

function content_save(array $store): void
{
    $dir = dirname(content_file());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $fp = fopen(content_file(), 'c+');
    if (!$fp) {
        throw new RuntimeException('Could not save the page.');
    }
    flock($fp, LOCK_EX);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($store, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    $GLOBALS['__content'] = $store;
}

function content_pages(): array
{
    return [
        'home' => ['label' => 'Home', 'href' => '/', 'off' => false],
        'about' => ['label' => 'About', 'href' => '/about', 'off' => true],
        'projects' => ['label' => 'Projects page', 'href' => '/projects', 'off' => true],
        'services' => ['label' => 'Services', 'href' => '/services', 'off' => true],
        'cvs' => ['label' => 'CVs', 'href' => '/cvs', 'off' => true],
        'contact' => ['label' => 'Contact', 'href' => '/contact', 'off' => true],
        'github' => ['label' => 'GitHub', 'href' => '/github-activity', 'off' => true],
        'site' => ['label' => 'Site details', 'href' => '/', 'off' => false],
    ];
}

function content_enabled(string $page): bool
{
    $pages = content_pages();
    if (empty($pages[$page]['off'])) {
        return true;
    }
    $flag = content_store()['enabled'][$page] ?? true;
    return (bool) $flag;
}

function content_href_on(string $href): bool
{
    $map = [
        '/about' => 'about',
        '/projects' => 'projects',
        '/services' => 'services',
        '/cvs' => 'cvs',
        '/contact' => 'contact',
        '/github-activity' => 'github',
    ];
    $page = $map[$href] ?? '';
    return $page === '' || content_enabled($page);
}

function content_defaults(): array
{
    return [
        'home' => [
            'status' => 'Available for senior & lead roles',
            'greeting' => "Hi, I'm",
            'skill_line' => 'Senior Full-Stack · Lead · Consultant',
            'place' => 'Abuja, NG',
            'projects_button' => 'View Projects',
            'download_button' => 'Download CV',
            'track_eyebrow' => 'Track record',
            'track_heading' => 'Mission-critical software for real institutions.',
            'track_lead' => 'National portals, federal compliance systems, enterprise ERPs, and healthtech MVPs, shipped to production, not slide decks.',
            'clients_label' => 'Selected clients',
            'work_eyebrow' => 'Selected work',
            'work_heading' => 'Platforms I have designed, built, or led.',
            'work_lead' => 'A small selection, picked to show range across enterprise, government, and healthtech.',
            'work_link' => 'View all projects',
            'journey_eyebrow' => 'Career timeline',
            'journey_heading' => 'A short snapshot of my professional journey.',
            'journey_link' => 'View full story',
            'cta_eyebrow' => "Let's build something",
            'cta_heading' => 'Need a senior engineer who can lead, build, and ship?',
            'cta_lead' => 'I work with teams that need a hands-on developer who can also talk to stakeholders, scope realistic delivery, and get to production.',
            'cv_label' => 'CV & documents',
            'cv_heading' => 'Grab the CV that fits the role.',
            'cv_text' => 'Full-stack, frontend, technical lead, and consulting versions, ready to download.',
            'skills' => ['Next.js', 'React', 'TypeScript', 'Node.js', 'Laravel', 'Flutter', 'React Native'],
            'clients' => ['Nigerian Bulk Electricity Trading PLC', 'Nigerian National Petroleum Corporation', 'Federal Ministry of Labour & Employment', 'Federal Character Commission', 'Federal Fire Service', 'Nigeria Social Insurance Trust Fund', 'National Inland Waterways Authority', 'Akwa Ibom State Government'],
        ],
        'about' => [
            'eyebrow' => 'About',
            'title' => 'Hands-on senior engineer. Calm under pressure. Built for serious platforms.',
            'lead' => "I'm Samuel, a software developer and technical consultant based in Abuja, Nigeria. I help organizations turn complex operational needs into scalable software products.",
            'who_eyebrow' => 'Who I am',
            'who_title' => 'Senior full-stack developer, technical lead, and tech consultant.',
            'who_1' => 'I have 7+ years of experience delivering enterprise, government, healthtech, compliance, ERP, and digital transformation platforms. I work across the full stack, including Next.js, React, TypeScript, Node.js, and Laravel, and I ship cross-platform mobile apps in Flutter and React Native alongside them.',
            'who_2' => 'My favourite engagements involve high-stakes operational needs: regulators that need workflow tooling, enterprises that need ERP-style modules, healthtech founders that need an MVP yesterday, and clients who need an engineer who can also talk to their stakeholders.',
            'who_3' => "I've shipped public-facing platforms for the Federal Ministry of Labour & Employment (NELEX, LMIS), the Federal Character Commission, NNPC, NSITF, and supported-care services in the UK, along with Flutter and React Native mobile apps, corporate sites, and MVPs for private-sector clients.",
            'diff_eyebrow' => 'What makes me different',
            'timeline_eyebrow' => 'Career timeline',
            'timeline_title' => "Where I've delivered, led, and learned.",
            'points' => ['I can code hands-on.', 'I can lead developers.', 'I can speak with clients and stakeholders.', 'I understand business requirements.', 'I can turn rough ideas into working systems.', 'I have experience with sensitive enterprise and public-sector platforms.'],
        ],
        'projects' => [
            'eyebrow' => 'Projects',
            'title' => 'Platforms across enterprise, government, healthtech, and commercial.',
            'lead' => 'Each project is grouped by category. Open any case study for the problem, solution, key features, tech stack, and impact.',
            'primary' => 'Request Project Walkthrough',
            'secondary' => 'View GitHub Activity',
        ],
        'services' => [
            'eyebrow' => 'Services',
            'title' => 'How I can help your team ship serious software.',
            'lead' => 'Five focused services covering hands-on delivery, leadership, and consulting, for teams that need outcomes, not just code.',
            'primary' => 'Hire Me',
            'secondary' => 'View Case Studies',
        ],
        'cvs' => [
            'eyebrow' => 'CVs & documents',
            'title' => 'CVs and Professional Documents',
            'lead' => 'One profile, multiple stories. Pick the version that matches the role or engagement you have in mind.',
            'note' => "Not sure which CV fits? Send me the role brief and I'll send the best version back.",
        ],
        'contact' => [
            'eyebrow' => 'Contact',
            'title' => "Let's talk.",
            'lead' => 'For roles, partnerships, consulting, or technical leadership opportunities, send a message and I will respond as soon as possible.',
            'button' => 'Send Message',
        ],
        'github' => [
            'eyebrow' => 'Developer Activity Dashboard',
            'title' => 'A live snapshot of my public GitHub activity.',
            'lead' => 'Repositories, commits, pull requests, languages, and how the work is spread across the year. Private and client systems are not shown here.',
            'note' => 'Some enterprise and client work stays off this page because it lives in private repositories.',
        ],
    ];
}

function content_text(string $page, string $key, string $default = ''): string
{
    $value = content_store()['pages'][$page][$key] ?? null;
    if (is_string($value) && $value !== '') {
        return $value;
    }
    if ($default !== '') {
        return $default;
    }
    $fallback = content_defaults()[$page][$key] ?? '';
    return is_string($fallback) ? $fallback : '';
}

function content_lines(string $page, string $key, array $default = []): array
{
    $value = content_store()['pages'][$page][$key] ?? null;
    if (!is_array($value)) {
        $fallback = content_defaults()[$page][$key] ?? $default;
        return is_array($fallback) ? $fallback : $default;
    }
    $lines = [];
    foreach ($value as $line) {
        $line = trim((string) $line);
        if ($line !== '') {
            $lines[] = $line;
        }
    }
    $fallback = content_defaults()[$page][$key] ?? $default;
    return $lines ?: (is_array($fallback) ? $fallback : $default);
}

function content_list(string $name): ?array
{
    $value = content_store()['lists'][$name] ?? null;
    return is_array($value) && $value ? $value : null;
}

function content_split_lines(string $text): array
{
    $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
    $clean = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $clean[] = $line;
        }
    }
    return $clean;
}

function content_upload(array $file, string $folder, string $name): ?string
{
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The file did not upload. Try one under 8 MB.');
    }
    if (($file['size'] ?? 0) > 8 * 1024 * 1024) {
        throw new RuntimeException('Use a file under 8 MB.');
    }
    $tmp = (string) ($file['tmp_name'] ?? '');
    $image = @getimagesize($tmp);
    $imageTypes = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    if ($folder === 'img') {
        $ext = $imageTypes[$image[2] ?? 0] ?? null;
        if (!$image || $ext === null) {
            throw new RuntimeException('Use a JPG, PNG, WEBP, or GIF.');
        }
        $dir = dirname(__DIR__, 2) . '/img/uploads';
        $public = '/img/uploads/';
    } else {
        $mime = (string) ($file['type'] ?? '');
        $original = strtolower((string) ($file['name'] ?? ''));
        if ($mime !== 'application/pdf' && !str_ends_with($original, '.pdf')) {
            throw new RuntimeException('CV files need to be PDF.');
        }
        $ext = 'pdf';
        $dir = dirname(__DIR__, 2) . '/cvs';
        $public = '/cvs/';
    }
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $safe = trim((string) preg_replace('/[^a-z0-9_-]+/i', '-', $name), '-');
    if ($safe === '') {
        $safe = 'file';
    }
    $filename = $safe . '-' . date('YmdHis') . '.' . $ext;
    if (!move_uploaded_file($tmp, $dir . '/' . $filename)) {
        throw new RuntimeException('Could not save the file on the server.');
    }
    return $public . $filename;
}

function content_home_metrics(): array
{
    $saved = content_list('metrics');
    if ($saved) {
        return $saved;
    }
    return [
        ['icon' => 'server', 'value' => '28+', 'label' => 'Projects delivered', 'helper' => 'Across enterprise, government, healthtech and commercial'],
        ['icon' => 'building', 'value' => '9', 'label' => 'Federal & enterprise platforms', 'helper' => 'From ERPs and HSE systems to national portals'],
        ['icon' => 'shield', 'value' => '5+', 'label' => 'Federal agencies served', 'helper' => 'Including ministries and government corporations'],
        ['icon' => 'phone2', 'value' => '7+', 'label' => 'Years shipping production', 'helper' => 'Web, mobile, and back-office systems'],
    ];
}

function content_related_lists(string $page): array
{
    return [
        'home' => ['metrics'],
        'about' => ['timeline', 'certifications'],
        'services' => ['services'],
        'cvs' => ['cvs'],
    ][$page] ?? [];
}

function admin_side(string $active): void
{
    $pages = content_pages();
    echo '<aside class="ib-side">';
    echo '<a class="' . ($active === 'inbox' ? 'is-on' : '') . '" href="/admin">Inbox</a>';
    echo '<a class="' . ($active === 'catalog' ? 'is-on' : '') . '" href="/admin/projects">Projects</a>';
    echo '<p class="ib-side-label">Pages</p>';
    foreach ($pages as $key => $meta) {
        $class = $active === $key ? 'is-on' : '';
        echo '<a class="' . $class . '" href="/admin/site/' . e($key) . '">' . e($meta['label']) . '</a>';
    }
    echo '<a class="ib-side-out" href="/admin?logout=1">Log out</a>';
    echo '</aside>';
}

function page_guard(string $page): void
{
    if (content_enabled($page)) {
        return;
    }
    http_response_code(404);
    layout_start('Page unavailable', 'This page is not available.');
    echo '<div class="container container-prose py-24"><h1 class="heading-2">This page is not available.</h1><p class="mt-4 text-white/70">It has been turned off.</p><a class="btn-primary mt-8" href="/">Go home</a></div>';
    layout_end();
    exit;
}
