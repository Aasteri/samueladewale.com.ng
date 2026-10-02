<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/content.php';

session_start();

if (empty($_SESSION['admin'])) {
    header('Location: /admin');
    exit;
}
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$pages = content_pages();
$page = (string) ($_GET['page'] ?? '');
if (!isset($pages[$page])) {
    http_response_code(404);
    echo 'Unknown page';
    exit;
}

$meta = $pages[$page];
$error = '';
$notice = isset($_GET['saved']) ? 'Saved. The live page is updated.' : '';

$specs = [
    'home' => [
        'status' => 'Status pill',
        'greeting' => 'Greeting',
        'skill_line' => 'Skill line on the photo',
        'place' => 'Place label',
        'projects_button' => 'Projects button',
        'download_button' => 'Download button',
        'track_eyebrow' => 'Track record label',
        'track_heading' => 'Track record heading',
        'track_lead' => 'Track record text',
        'clients_label' => 'Clients label',
        'work_eyebrow' => 'Selected work label',
        'work_heading' => 'Selected work heading',
        'work_lead' => 'Selected work text',
        'work_link' => 'Selected work link',
        'journey_eyebrow' => 'Journey label',
        'journey_heading' => 'Journey heading',
        'journey_link' => 'Journey link',
        'cta_eyebrow' => 'Closing label',
        'cta_heading' => 'Closing heading',
        'cta_lead' => 'Closing text',
        'cv_label' => 'CV box label',
        'cv_heading' => 'CV box heading',
        'cv_text' => 'CV box text',
    ],
    'about' => [
        'eyebrow' => 'Label',
        'title' => 'Heading',
        'lead' => 'Intro',
        'who_eyebrow' => 'Who I am label',
        'who_title' => 'Who I am heading',
        'who_1' => 'Paragraph 1',
        'who_2' => 'Paragraph 2',
        'who_3' => 'Paragraph 3',
        'diff_eyebrow' => 'Difference label',
        'timeline_eyebrow' => 'Timeline label',
        'timeline_title' => 'Timeline heading',
    ],
    'projects' => [
        'eyebrow' => 'Label',
        'title' => 'Heading',
        'lead' => 'Intro',
        'primary' => 'Primary button',
        'secondary' => 'Secondary button',
    ],
    'services' => [
        'eyebrow' => 'Label',
        'title' => 'Heading',
        'lead' => 'Intro',
        'primary' => 'Primary button',
        'secondary' => 'Secondary button',
    ],
    'cvs' => [
        'eyebrow' => 'Label',
        'title' => 'Heading',
        'lead' => 'Intro',
        'note' => 'Note under the cards',
    ],
    'contact' => [
        'eyebrow' => 'Label',
        'title' => 'Heading',
        'lead' => 'Intro',
        'button' => 'Send button',
    ],
    'github' => [
        'eyebrow' => 'Label',
        'title' => 'Heading',
        'lead' => 'Intro',
        'note' => 'Private work note',
    ],
    'site' => [],
];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $token = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        $error = 'The form expired. Refresh and try again.';
    } else {
        try {
            $store = content_store();
            if (($_POST['action'] ?? '') === 'restore') {
                unset($store['pages'][$page]);
                foreach (content_related_lists($page) as $list) {
                    unset($store['lists'][$list]);
                }
                $store['enabled'][$page] = true;
                content_save($store);
                header('Location: /admin/site/' . rawurlencode($page) . '?saved=1');
                exit;
            }
            if (!empty($meta['off'])) {
                $store['enabled'][$page] = isset($_POST['on']);
            }
            foreach (array_keys($specs[$page]) as $key) {
                $store['pages'][$page][$key] = trim((string) ($_POST['text'][$key] ?? ''));
            }
            if ($page === 'home') {
                $store['pages']['home']['skills'] = content_split_lines((string) ($_POST['skills'] ?? ''));
                $store['pages']['home']['clients'] = content_split_lines((string) ($_POST['clients'] ?? ''));
                $metrics = [];
                foreach ((array) ($_POST['m_value'] ?? []) as $i => $value) {
                    $value = trim((string) $value);
                    $label = trim((string) ($_POST['m_label'][$i] ?? ''));
                    if ($value === '' && $label === '') {
                        continue;
                    }
                    $metrics[] = [
                        'icon' => trim((string) ($_POST['m_icon'][$i] ?? 'server')) ?: 'server',
                        'value' => $value,
                        'label' => $label,
                        'helper' => trim((string) ($_POST['m_helper'][$i] ?? '')),
                    ];
                }
                if ($metrics) {
                    $store['lists']['metrics'] = $metrics;
                }
            }
            if ($page === 'about') {
                $store['pages']['about']['points'] = content_split_lines((string) ($_POST['points'] ?? ''));
                $certs = content_split_lines((string) ($_POST['certifications'] ?? ''));
                if ($certs) {
                    $store['lists']['certifications'] = $certs;
                }
                $timeline = [];
                foreach ((array) ($_POST['tl_company'] ?? []) as $i => $company) {
                    $company = trim((string) $company);
                    if ($company === '') {
                        continue;
                    }
                    $timeline[] = [
                        'company' => $company,
                        'role' => trim((string) ($_POST['tl_role'][$i] ?? '')),
                        'period' => trim((string) ($_POST['tl_period'][$i] ?? '')),
                        'description' => trim((string) ($_POST['tl_description'][$i] ?? '')),
                        'highlights' => content_split_lines((string) ($_POST['tl_highlights'][$i] ?? '')),
                    ];
                }
                if ($timeline) {
                    $store['lists']['timeline'] = $timeline;
                }
            }
            if ($page === 'services') {
                $services = [];
                foreach ((array) ($_POST['svc_title'] ?? []) as $i => $title) {
                    $title = trim((string) $title);
                    if ($title === '') {
                        continue;
                    }
                    $services[] = [
                        'icon' => trim((string) ($_POST['svc_icon'][$i] ?? 'server')) ?: 'server',
                        'title' => $title,
                        'summary' => trim((string) ($_POST['svc_summary'][$i] ?? '')),
                        'bullets' => content_split_lines((string) ($_POST['svc_bullets'][$i] ?? '')),
                    ];
                }
                if ($services) {
                    $store['lists']['services'] = $services;
                }
            }
            if ($page === 'cvs') {
                $docs = [];
                $titles = (array) ($_POST['cv_title'] ?? []);
                foreach ($titles as $i => $title) {
                    $title = trim((string) $title);
                    $pdf = trim((string) ($_POST['cv_pdf'][$i] ?? ''));
                    $bag = $_FILES['cv_file'] ?? [];
                    $file = [
                        'name' => $bag['name'][$i] ?? '',
                        'type' => $bag['type'][$i] ?? '',
                        'tmp_name' => $bag['tmp_name'][$i] ?? '',
                        'error' => $bag['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $bag['size'][$i] ?? 0,
                    ];
                    $uploaded = content_upload($file, 'pdf', $title !== '' ? $title : 'cv');
                    if ($uploaded) {
                        $pdf = $uploaded;
                    }
                    if ($title === '' && $pdf === '') {
                        continue;
                    }
                    $filename = basename($pdf);
                    $docs[] = [
                        'title' => $title !== '' ? $title : $filename,
                        'bestFor' => trim((string) ($_POST['cv_best'][$i] ?? '')),
                        'pdf' => $pdf,
                        'filename' => $filename,
                    ];
                }
                if ($docs) {
                    $store['lists']['cvs'] = $docs;
                }
            }
            if ($page === 'site') {
                foreach (['name', 'title', 'description', 'tagline', 'headline', 'phone', 'email', 'location', 'availability', 'linkedin', 'github'] as $key) {
                    $store['pages']['site'][$key] = trim((string) ($_POST['site'][$key] ?? ''));
                }
                $store['pages']['site']['trust'] = content_split_lines((string) ($_POST['trust'] ?? ''));
                foreach (['portrait_primary' => 'primary', 'portrait_secondary' => 'secondary'] as $key => $slot) {
                    if (isset($_POST['clear_' . $key])) {
                        unset($store['pages']['site'][$key]);
                        continue;
                    }
                    $uploaded = content_upload($_FILES[$key] ?? [], 'img', $slot);
                    if ($uploaded) {
                        $store['pages']['site'][$key] = $uploaded;
                    }
                }
            }
            content_save($store);
            header('Location: /admin/site/' . rawurlencode($page) . '?saved=1');
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$site = site();
$long = ['lead', 'headline', 'description', 'track_lead', 'work_lead', 'cta_lead', 'cv_text', 'who_1', 'who_2', 'who_3', 'note', 'title'];
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title><?= e($meta['label']) ?> | Pages</title>
  <link rel="stylesheet" href="/assets/admin.css?v=6" />
</head>
<body class="ib">
  <div class="ib-shell">
    <?php admin_side($page); ?>
    <div class="ib-stage">
      <div class="ib-app">
        <header class="ib-top">
          <div class="ib-brand">
            <strong><?= e($meta['label']) ?></strong>
            <span>Edit the copy, files, and whether this page is on</span>
          </div>
          <?php if ($meta['href'] !== '/' || $page === 'home'): ?>
            <a class="ib-navlink" href="<?= e($meta['href']) ?>" target="_blank" rel="noreferrer">View page</a>
          <?php endif; ?>
        </header>
        <?php if ($error): ?><p class="ib-banner bad"><?= e($error) ?></p><?php endif; ?>
        <?php if ($notice): ?><p class="ib-banner ok"><?= e($notice) ?></p><?php endif; ?>
        <form class="ib-page" method="post" action="/admin/site/<?= e($page) ?>" enctype="multipart/form-data">
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>" />
          <?php if (!empty($meta['off'])): ?>
            <label class="ib-switch">
              <input type="checkbox" name="on" value="1" <?= content_enabled($page) ? 'checked' : '' ?> />
              <span>Page is on. Uncheck to hide it from the menu and visitors.</span>
            </label>
          <?php endif; ?>

          <?php if ($page === 'site'): ?>
            <div class="ib-grid2">
              <?php foreach ([
                'name' => 'Name',
                'title' => 'Browser title',
                'tagline' => 'Tagline',
                'phone' => 'Phone',
                'email' => 'Email',
                'location' => 'Location',
                'availability' => 'Availability',
                'linkedin' => 'LinkedIn URL',
                'github' => 'GitHub username',
              ] as $key => $label): ?>
                <label class="ib-field">
                  <span><?= e($label) ?></span>
                  <input name="site[<?= e($key) ?>]" value="<?= e((string) $site[$key]) ?>" />
                </label>
              <?php endforeach; ?>
            </div>
            <label class="ib-field"><span>Headline</span><textarea name="site[headline]" rows="3"><?= e($site['headline']) ?></textarea></label>
            <label class="ib-field"><span>Search description</span><textarea name="site[description]" rows="3"><?= e($site['description']) ?></textarea></label>
            <label class="ib-field"><span>Trust line, one item per line</span><textarea name="trust" rows="4"><?= e(implode("\n", $site['trustLine'])) ?></textarea></label>
            <div class="ib-grid2">
              <?php foreach (['portrait_primary' => 'Main portrait', 'portrait_secondary' => 'About portrait'] as $key => $label):
                $src = $key === 'portrait_primary' ? $site['portrait']['primary'] : $site['portrait']['secondary']; ?>
                <div class="ib-cardbox">
                  <p><?= e($label) ?></p>
                  <img class="ib-preview" src="<?= e($src) ?>" alt="" />
                  <label class="ib-field"><span>Replace image</span><input type="file" name="<?= e($key) ?>" accept="image/jpeg,image/png,image/webp,image/gif" /></label>
                  <label class="ib-switch"><input type="checkbox" name="clear_<?= e($key) ?>" value="1" /><span>Use the original photo</span></label>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <?php foreach ($specs[$page] as $key => $label): ?>
              <label class="ib-field">
                <span><?= e($label) ?></span>
                <?php if (in_array($key, $long, true)): ?>
                  <textarea name="text[<?= e($key) ?>]" rows="4"><?= e(content_text($page, $key)) ?></textarea>
                <?php else: ?>
                  <input name="text[<?= e($key) ?>]" value="<?= e(content_text($page, $key)) ?>" />
                <?php endif; ?>
              </label>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ($page === 'home'): ?>
            <label class="ib-field"><span>Skills on the photo, one per line</span><textarea name="skills" rows="7"><?= e(implode("\n", content_lines('home', 'skills'))) ?></textarea></label>
            <label class="ib-field"><span>Client names, one per line</span><textarea name="clients" rows="8"><?= e(implode("\n", content_lines('home', 'clients'))) ?></textarea></label>
            <h2>Numbers</h2>
            <?php foreach (array_merge(content_home_metrics(), [['icon' => '', 'value' => '', 'label' => '', 'helper' => '']]) as $row): ?>
              <div class="ib-cardbox ib-grid2">
                <label class="ib-field"><span>Number</span><input name="m_value[]" value="<?= e($row['value']) ?>" /></label>
                <label class="ib-field"><span>Icon name</span><input name="m_icon[]" value="<?= e($row['icon']) ?>" placeholder="server, building, shield, phone2" /></label>
                <label class="ib-field"><span>Label</span><input name="m_label[]" value="<?= e($row['label']) ?>" /></label>
                <label class="ib-field"><span>Helper</span><input name="m_helper[]" value="<?= e($row['helper']) ?>" /></label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ($page === 'about'): ?>
            <label class="ib-field"><span>What makes me different, one line each</span><textarea name="points" rows="7"><?= e(implode("\n", content_lines('about', 'points'))) ?></textarea></label>
            <label class="ib-field"><span>Certificates, one per line</span><textarea name="certifications" rows="6"><?= e(implode("\n", certifications())) ?></textarea></label>
            <h2>Timeline</h2>
            <?php foreach (array_merge(timeline(), [['company' => '', 'role' => '', 'period' => '', 'description' => '', 'highlights' => []]]) as $row): ?>
              <div class="ib-cardbox">
                <div class="ib-grid2">
                  <label class="ib-field"><span>Company</span><input name="tl_company[]" value="<?= e($row['company']) ?>" /></label>
                  <label class="ib-field"><span>Role</span><input name="tl_role[]" value="<?= e($row['role']) ?>" /></label>
                  <label class="ib-field"><span>Period</span><input name="tl_period[]" value="<?= e($row['period']) ?>" /></label>
                </div>
                <label class="ib-field"><span>Description</span><textarea name="tl_description[]" rows="3"><?= e($row['description']) ?></textarea></label>
                <label class="ib-field"><span>Highlights, one per line</span><textarea name="tl_highlights[]" rows="3"><?= e(implode("\n", $row['highlights'] ?? [])) ?></textarea></label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ($page === 'services'): ?>
            <h2>Services</h2>
            <p class="ib-hint">Leave the title blank to remove a service. Icon names: server, phone2, target, building, spark, award, briefcase, mail, shield, file.</p>
            <?php foreach (array_merge(services(), [['icon' => 'spark', 'title' => '', 'summary' => '', 'bullets' => []]]) as $row): ?>
              <div class="ib-cardbox">
                <div class="ib-grid2">
                  <label class="ib-field"><span>Title</span><input name="svc_title[]" value="<?= e($row['title']) ?>" /></label>
                  <label class="ib-field"><span>Icon</span><input name="svc_icon[]" value="<?= e($row['icon']) ?>" /></label>
                </div>
                <label class="ib-field"><span>Summary</span><textarea name="svc_summary[]" rows="3"><?= e($row['summary']) ?></textarea></label>
                <label class="ib-field"><span>Points, one per line</span><textarea name="svc_bullets[]" rows="4"><?= e(implode("\n", $row['bullets'] ?? [])) ?></textarea></label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <?php if ($page === 'cvs'): ?>
            <h2>CV files</h2>
            <p class="ib-hint">Upload a PDF to replace a file, or fill the blank card to add one. Clear the title and leave the file empty to drop a card.</p>
            <?php foreach (array_merge(cvs(), [['title' => '', 'bestFor' => '', 'pdf' => '', 'filename' => '']]) as $row): ?>
              <div class="ib-cardbox">
                <label class="ib-field"><span>Title</span><input name="cv_title[]" value="<?= e($row['title']) ?>" /></label>
                <label class="ib-field"><span>Who it is for</span><textarea name="cv_best[]" rows="3"><?= e($row['bestFor']) ?></textarea></label>
                <input type="hidden" name="cv_pdf[]" value="<?= e($row['pdf']) ?>" />
                <?php if ($row['pdf'] !== ''): ?><p class="ib-hint">Current file: <?= e($row['filename']) ?></p><?php endif; ?>
                <label class="ib-field"><span>PDF</span><input type="file" name="cv_file[]" accept="application/pdf,.pdf" /></label>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

          <div class="ib-actions">
            <button type="submit">Save page</button>
            <button type="submit" name="action" value="restore" class="ib-ghost">Restore original</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
