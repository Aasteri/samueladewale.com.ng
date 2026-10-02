<?php
declare(strict_types=1);

function catalog_file(): string
{
    return dirname(__DIR__) . '/storage/projects.json';
}

function catalog_all(): array
{
    $file = catalog_file();
    if (!is_file($file)) {
        return [];
    }
    $rows = json_decode((string) file_get_contents($file), true);
    return is_array($rows) ? array_values($rows) : [];
}

function catalog_mutate(callable $mutator): mixed
{
    $dir = dirname(catalog_file());
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $fp = fopen(catalog_file(), 'c+');
    if (!$fp) {
        throw new RuntimeException('Could not store the project.');
    }
    flock($fp, LOCK_EX);
    $raw = stream_get_contents($fp);
    $rows = json_decode($raw ?: '[]', true);
    if (!is_array($rows)) {
        $rows = [];
    }
    $result = $mutator($rows);
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode(array_values($rows), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return $result;
}

function catalog_merge(array $base): array
{
    $fresh = [];
    foreach (catalog_all() as $project) {
        $slug = (string) ($project['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $found = false;
        foreach ($base as $i => $row) {
            if (($row['slug'] ?? '') === $slug) {
                $base[$i] = array_merge($row, $project);
                $found = true;
                break;
            }
        }
        if (!$found) {
            $fresh[] = $project;
        }
    }
    return array_merge($fresh, $base);
}

function catalog_lines(string $text): array
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

function catalog_slug(string $name, array $taken): string
{
    $base = strtolower((string) preg_replace('/[^a-z0-9]+/i', '-', $name));
    $base = trim($base, '-');
    if ($base === '') {
        $base = 'project';
    }
    $slug = $base;
    $n = 2;
    while (in_array($slug, $taken, true)) {
        $slug = $base . '-' . $n;
        $n++;
    }
    return $slug;
}

function catalog_builtin_slugs(): array
{
    $source = (string) file_get_contents(dirname(__DIR__) . '/data/projects.php');
    preg_match_all("/'slug' => '([^']+)'/", $source, $matches);
    return $matches[1] ?? [];
}

function catalog_store_image(array $file, string $slug): ?string
{
    $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('The image did not upload. Try a file under 5 MB.');
    }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Use an image under 5 MB.');
    }
    $info = @getimagesize((string) ($file['tmp_name'] ?? ''));
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'];
    $ext = $types[$info[2] ?? 0] ?? null;
    if (!$info || $ext === null) {
        throw new RuntimeException('Use a JPG, PNG, WEBP, or GIF.');
    }
    $dir = dirname(__DIR__, 2) . '/img/portfolio';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $dest = $dir . '/' . $slug . '.' . $ext;
    foreach (['jpg', 'jpeg', 'png', 'webp', 'gif'] as $old) {
        $previous = $dir . '/' . $slug . '.' . $old;
        if ($previous !== $dest && is_file($previous)) {
            unlink($previous);
        }
    }
    if (!move_uploaded_file((string) $file['tmp_name'], $dest)) {
        throw new RuntimeException('Could not save the image on the server.');
    }
    return '/img/portfolio/' . $slug . '.' . $ext;
}

function catalog_to_form(array $row): array
{
    return [
        'name' => $row['name'] ?? '',
        'client' => $row['client'] ?? '',
        'role' => $row['role'] ?? 'Full-Stack Developer',
        'live_url' => $row['liveUrl'] ?? '',
        'category' => $row['category'] ?? 'Commercial',
        'group' => $row['group'] ?? 'Corporate & Commercial',
        'year' => $row['year'] ?? date('Y'),
        'summary' => $row['summary'] ?? '',
        'problem' => $row['problem'] ?? '',
        'solution' => $row['solution'] ?? '',
        'features' => implode("\n", $row['features'] ?? []),
        'stack' => implode(', ', $row['stack'] ?? []),
        'impact' => implode("\n", $row['impact'] ?? []),
        'featured' => !empty($row['featured']),
        'confidential' => !empty($row['confidential']),
        'image' => $row['imageUrl'] ?? '',
    ];
}

function catalog_from_post(array $post, string $slug): array
{
    $url = trim((string) ($post['live_url'] ?? ''));
    $host = '';
    if ($url !== '') {
        $host = (string) parse_url($url, PHP_URL_HOST);
        $host = preg_replace('/^www\./', '', $host) ?: '';
    }
    $stack = catalog_lines(str_replace(',', "\n", (string) ($post['stack'] ?? '')));
    return [
        'slug' => $slug,
        'name' => trim((string) ($post['name'] ?? '')),
        'category' => trim((string) ($post['category'] ?? 'Commercial')),
        'group' => trim((string) ($post['group'] ?? 'Corporate & Commercial')),
        'role' => trim((string) ($post['role'] ?? '')) ?: 'Full-Stack Developer',
        'client' => trim((string) ($post['client'] ?? '')),
        'summary' => trim((string) ($post['summary'] ?? '')),
        'problem' => trim((string) ($post['problem'] ?? '')),
        'solution' => trim((string) ($post['solution'] ?? '')),
        'features' => catalog_lines((string) ($post['features'] ?? '')),
        'stack' => $stack,
        'impact' => catalog_lines((string) ($post['impact'] ?? '')),
        'liveUrl' => $url,
        'previewLabel' => $host,
        'year' => trim((string) ($post['year'] ?? '')),
        'imageUrl' => trim((string) ($post['image_url'] ?? '')),
        'featured' => !empty($post['featured']),
        'confidential' => !empty($post['confidential']),
    ];
}
