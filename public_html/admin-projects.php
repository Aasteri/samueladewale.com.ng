<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/catalog.php';
require __DIR__ . '/backend/lib/content.php';

session_start();

if (empty($_SESSION['admin'])) {
    header('Location: /admin');
    exit;
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$groups = array_map(fn ($g) => $g['group'], project_groups());
$categories = ['Government', 'Enterprise', 'Commercial', 'Healthtech', 'Non-profit'];
$error = '';
$notice = isset($_GET['saved']) ? 'Project saved. It is on the site now.' : '';
if (isset($_GET['removed'])) {
    $notice = 'Project removed.';
}

$editing = trim((string) ($_GET['edit'] ?? ''));
$form = [
    'name' => '',
    'client' => '',
    'role' => 'Full-Stack Developer',
    'live_url' => '',
    'category' => 'Commercial',
    'group' => 'Corporate & Commercial',
    'year' => date('Y'),
    'summary' => '',
    'problem' => '',
    'solution' => '',
    'features' => '',
    'stack' => '',
    'impact' => '',
    'featured' => false,
    'confidential' => false,
    'image' => '',
];

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $token = (string) ($_POST['csrf'] ?? '');
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        $error = 'The form expired. Refresh and try again.';
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $slug = trim((string) ($_POST['slug'] ?? ''));
        catalog_mutate(function (array &$rows) use ($slug) {
            $rows = array_values(array_filter($rows, fn ($row) => ($row['slug'] ?? '') !== $slug));
        });
        header('Location: /admin/projects?removed=1');
        exit;
    } else {
        $form = array_merge($form, [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'client' => trim((string) ($_POST['client'] ?? '')),
            'role' => trim((string) ($_POST['role'] ?? '')),
            'live_url' => trim((string) ($_POST['live_url'] ?? '')),
            'category' => trim((string) ($_POST['category'] ?? '')),
            'group' => trim((string) ($_POST['group'] ?? '')),
            'year' => trim((string) ($_POST['year'] ?? '')),
            'summary' => trim((string) ($_POST['summary'] ?? '')),
            'problem' => trim((string) ($_POST['problem'] ?? '')),
            'solution' => trim((string) ($_POST['solution'] ?? '')),
            'features' => trim((string) ($_POST['features'] ?? '')),
            'stack' => trim((string) ($_POST['stack'] ?? '')),
            'impact' => trim((string) ($_POST['impact'] ?? '')),
            'featured' => !empty($_POST['featured']),
            'confidential' => !empty($_POST['confidential']),
            'image' => trim((string) ($_POST['current_image'] ?? '')),
        ]);
        $url = $form['live_url'];
        if ($form['name'] === '' || $form['summary'] === '') {
            $error = 'Add a name and a short summary.';
        } elseif ($url !== '' && !filter_var($url, FILTER_VALIDATE_URL)) {
            $error = 'That live link does not look like a full web address.';
        } elseif (!in_array($form['group'], $groups, true)) {
            $error = 'Pick a project group.';
        } else {
            $existing = trim((string) ($_POST['slug'] ?? ''));
            $taken = array_map(fn ($p) => $p['slug'], projects());
            if ($existing !== '' && in_array($existing, $taken, true)) {
                $slug = $existing;
            } else {
                $slug = catalog_slug($form['name'], $taken);
            }
            try {
                $uploaded = catalog_store_image($_FILES['image'] ?? [], $slug);
            } catch (Throwable $err) {
                $error = $err->getMessage();
                $editing = trim((string) ($_POST['slug'] ?? ''));
                $uploaded = null;
            }
            if ($error === '') {
            if (!empty($_POST['remove_image'])) {
                $form['image'] = '';
            } elseif ($uploaded) {
                $form['image'] = $uploaded;
            } else {
                $form['image'] = trim((string) ($_POST['current_image'] ?? ''));
            }
            $record = catalog_from_post(array_merge($form, ['image_url' => $form['image']]), $slug);
            catalog_mutate(function (array &$rows) use ($record) {
                foreach ($rows as $i => $row) {
                    if (($row['slug'] ?? '') === $record['slug']) {
                        $rows[$i] = $record;
                        return;
                    }
                }
                array_unshift($rows, $record);
            });
            header('Location: /admin/projects?saved=1&edit=' . rawurlencode($slug));
            exit;
            }
        }
        $editing = trim((string) ($_POST['slug'] ?? ''));
    }
}

if ($editing !== '' && $error === '' && ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    $existingProject = project_by_slug($editing);
    if ($existingProject) {
        $form = catalog_to_form($existingProject);
    }
}

$builtin = catalog_builtin_slugs();
$find = strtolower(trim((string) ($_GET['find'] ?? '')));
$listed = projects();
if ($find !== '') {
    $listed = array_values(array_filter($listed, function (array $row) use ($find): bool {
        $haystack = strtolower(implode(' ', [$row['name'] ?? '', $row['client'] ?? '', $row['summary'] ?? '', $row['liveUrl'] ?? '']));
        return str_contains($haystack, $find);
    }));
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Projects | Inbox</title>
  <link rel="stylesheet" href="/assets/admin.css?v=6" />
</head>
<body class="ib">
  <div class="ib-shell">
    <?php admin_side('catalog'); ?>
    <div class="ib-stage">
  <div class="ib-app">
    <header class="ib-top">
      <div class="ib-brand">
        <strong>Projects</strong>
        <span>Add a project, or change any existing one</span>
      </div>
      <a class="ib-logout" href="/admin?logout=1">Log out</a>
    </header>
    <?php if ($error): ?><p class="ib-banner bad"><?= e($error) ?></p><?php endif; ?>
    <?php if ($notice): ?><p class="ib-banner ok"><?= e($notice) ?></p><?php endif; ?>
    <div class="ib-studio">
      <form class="ib-form" method="post" action="/admin/projects" enctype="multipart/form-data">
        <h2><?= $editing !== '' ? 'Edit project' : 'New project' ?></h2>
        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>" />
        <input type="hidden" name="slug" value="<?= e($editing) ?>" />
        <input type="hidden" name="current_image" value="<?= e($form['image']) ?>" />
        <label>Card image
          <?php if ($form['image'] !== ''): ?>
            <img class="ib-preview" src="<?= e($form['image']) ?>" alt="Current project image" />
          <?php endif; ?>
          <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif" />
        </label>
        <p class="ib-file-note">JPG, PNG, WEBP, or GIF, under 5 MB. Uploading a new file replaces the picture on the card.</p>
        <?php if ($form['image'] !== ''): ?>
          <label class="ib-checks"><input type="checkbox" name="remove_image" value="1" /> Remove the current image</label>
        <?php endif; ?>
        <label>Name<input name="name" required value="<?= e($form['name']) ?>" placeholder="Project or product name" /></label>
        <label>Client<input name="client" value="<?= e($form['client']) ?>" placeholder="Who it was for" /></label>
        <label>Your role<input name="role" value="<?= e($form['role']) ?>" /></label>
        <label>Live link<input name="live_url" value="<?= e($form['live_url']) ?>" placeholder="https://" /></label>
        <div class="ib-split">
          <label>Group
            <select name="group">
              <?php foreach ($groups as $group): ?>
                <option value="<?= e($group) ?>" <?= $form['group'] === $group ? 'selected' : '' ?>><?= e($group) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Category
            <select name="category">
              <?php foreach ($categories as $category): ?>
                <option value="<?= e($category) ?>" <?= $form['category'] === $category ? 'selected' : '' ?>><?= e($category) ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
        <label>Year<input name="year" value="<?= e((string) $form['year']) ?>" /></label>
        <label>Summary<textarea name="summary" required rows="3" placeholder="One or two sentences a visitor can read on the card."><?= e($form['summary']) ?></textarea></label>
        <label>Problem<textarea name="problem" rows="3"><?= e($form['problem']) ?></textarea></label>
        <label>Solution<textarea name="solution" rows="3"><?= e($form['solution']) ?></textarea></label>
        <label>Features, one per line<textarea name="features" rows="4"><?= e($form['features']) ?></textarea></label>
        <label>Stack, separated by commas<input name="stack" value="<?= e($form['stack']) ?>" placeholder="Next.js, Laravel, PHP" /></label>
        <label>Impact, one per line<textarea name="impact" rows="3"><?= e($form['impact']) ?></textarea></label>
        <div class="ib-checks">
          <label><input type="checkbox" name="featured" value="1" <?= $form['featured'] ? 'checked' : '' ?> /> Show on the home page</label>
          <label><input type="checkbox" name="confidential" value="1" <?= $form['confidential'] ? 'checked' : '' ?> /> Confidential</label>
        </div>
        <div class="ib-form-actions">
          <button class="ib-send" type="submit"><?= $editing !== '' ? 'Save changes' : 'Add project' ?></button>
          <?php if ($editing !== ''): ?><a class="ib-navlink" href="/admin/projects">Cancel</a><?php endif; ?>
        </div>
      </form>
      <div class="ib-table-wrap">
        <form class="ib-find" method="get" action="/admin/projects">
          <input type="search" name="find" value="<?= e($find) ?>" placeholder="Find a project to edit" />
          <button type="submit">Search</button>
        </form>
        <table class="ib-table">
          <thead>
            <tr>
              <th>Image</th>
              <th>Project</th>
              <th>Group</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$listed): ?>
              <tr><td colspan="4"><p class="ib-empty">No projects match that search.</p></td></tr>
            <?php endif; ?>
            <?php foreach ($listed as $row): ?>
              <?php $customOnly = !in_array($row['slug'], $builtin, true); ?>
              <tr>
                <td><?php if (!empty($row['imageUrl'])): ?><img class="ib-thumb" src="<?= e($row['imageUrl']) ?>" alt="" /><?php else: ?><span class="ib-thumb ib-thumb-empty">No image</span><?php endif; ?></td>
                <td class="ib-person">
                  <a href="/projects/<?= e($row['slug']) ?>" target="_blank" rel="noreferrer"><?= e($row['name']) ?></a>
                  <small><?= e($row['previewLabel'] ?? ($row['liveUrl'] ?? '')) ?></small>
                </td>
                <td><?= e($row['group'] ?? '') ?></td>
                <td>
                  <a href="/admin/projects?edit=<?= e($row['slug']) ?>">Edit</a>
                  <?php if ($customOnly): ?>
                    <form method="post" action="/admin/projects" class="ib-inline" onsubmit="return confirm('Remove this project from the site?');">
                      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>" />
                      <input type="hidden" name="action" value="delete" />
                      <input type="hidden" name="slug" value="<?= e($row['slug']) ?>" />
                      <button type="submit">Remove</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
    </div>
  </div>
</body>
</html>
