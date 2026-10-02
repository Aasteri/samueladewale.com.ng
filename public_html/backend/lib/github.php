<?php
declare(strict_types=1);

function github_http(string $url, array $headers = []): ?array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_HTTPHEADER => array_merge(['User-Agent: samueladewale-portfolio', 'Accept: application/vnd.github+json'], $headers),
    ]);
    $body = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false || $code >= 400) {
        return null;
    }
    $json = json_decode($body, true);
    return is_array($json) ? $json : null;
}

function github_http_many(array $urls, array $headers = []): array
{
    if (!$urls) {
        return [];
    }
    $multi = curl_multi_init();
    $handles = [];
    foreach ($urls as $i => $url) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_HTTPHEADER => array_merge(['User-Agent: samueladewale-portfolio', 'Accept: application/vnd.github+json'], $headers),
        ]);
        curl_multi_add_handle($multi, $ch);
        $handles[$i] = $ch;
    }
    do {
        $status = curl_multi_exec($multi, $running);
        if ($running) {
            curl_multi_select($multi, 1.0);
        }
    } while ($running && $status === CURLM_OK);
    $out = [];
    foreach ($handles as $i => $ch) {
        $body = curl_multi_getcontent($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $json = ($body && $code < 400) ? json_decode($body, true) : null;
        $out[$i] = is_array($json) ? $json : null;
        curl_multi_remove_handle($multi, $ch);
        curl_close($ch);
    }
    curl_multi_close($multi);
    return $out;
}

function github_color(string $name): string
{
    $colors = [
        'PHP' => '#777BB4',
        'JavaScript' => '#F1E05A',
        'TypeScript' => '#3178C6',
        'Python' => '#3572A5',
        'Dart' => '#00B4AB',
        'HTML' => '#E34C26',
        'CSS' => '#563D7C',
        'Vue' => '#41B883',
        'Java' => '#B07219',
        'Go' => '#00ADD8',
        'C#' => '#178600',
        'Blade' => '#F7523F',
        'Shell' => '#89E051',
        'Kotlin' => '#A97BFF',
        'Swift' => '#F05138',
    ];
    return $colors[$name] ?? '#22D3EE';
}

function github_weeks(array $days): array
{
    $weeks = [];
    $current = [];
    foreach ($days as $day) {
        $weekday = (int) date('w', strtotime((string) ($day['date'] ?? 'now')));
        if ($weekday === 0 && $current) {
            $weeks[] = $current;
            $current = [];
        }
        $current[] = $day;
    }
    if ($current) {
        $weeks[] = $current;
    }
    if ($weeks && count($weeks[0]) < 7) {
        $pad = 7 - count($weeks[0]);
        $weeks[0] = array_merge(array_fill(0, $pad, ['date' => '', 'count' => 0, 'level' => 0]), $weeks[0]);
    }
    return $weeks;
}

function github_streaks(array $days): array
{
    $longest = 0;
    $running = 0;
    foreach ($days as $day) {
        if ((int) ($day['count'] ?? 0) > 0) {
            $running++;
            $longest = max($longest, $running);
        } else {
            $running = 0;
        }
    }
    $today = gmdate('Y-m-d');
    $current = 0;
    for ($i = count($days) - 1; $i >= 0; $i--) {
        $date = (string) ($days[$i]['date'] ?? '');
        if ($date > $today) {
            continue;
        }
        if ((int) ($days[$i]['count'] ?? 0) > 0) {
            $current++;
        } else {
            break;
        }
    }
    return [$current, $longest];
}

function github_activity(string $username): array
{
    $cacheFile = __DIR__ . '/../cache/github.json';
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < 21600) {
        $cached = json_decode((string) file_get_contents($cacheFile), true);
        if (is_array($cached) && ($cached['version'] ?? 0) === 2) {
            return $cached;
        }
    }

    $token = cfg('github_token', '');
    $auth = $token ? ['Authorization: Bearer ' . $token] : [];
    $user = github_http('https://api.github.com/users/' . rawurlencode($username), $auth);
    $repos = github_http('https://api.github.com/users/' . rawurlencode($username) . '/repos?sort=pushed&per_page=30&type=owner', $auth);
    $calendar = github_http('https://github-contributions-api.jogruber.de/v4/' . rawurlencode($username));
    $pulls = github_http('https://api.github.com/search/issues?q=' . rawurlencode('author:' . $username . ' type:pr') . '&per_page=1', $auth);
    $issues = github_http('https://api.github.com/search/issues?q=' . rawurlencode('author:' . $username . ' type:issue') . '&per_page=1', $auth);

    $days = ($calendar && !empty($calendar['contributions'])) ? $calendar['contributions'] : [];
    $weeks = github_weeks($days);
    [$currentStreak, $longestStreak] = github_streaks($days);
    $cutoff = gmdate('Y-m-d', time() - 90 * 86400);
    $last90 = 0;
    $months = [];
    foreach ($days as $day) {
        $date = (string) ($day['date'] ?? '');
        $count = (int) ($day['count'] ?? 0);
        if ($date >= $cutoff) {
            $last90 += $count;
        }
        if ($date === '') {
            continue;
        }
        $key = substr($date, 0, 7);
        $months[$key] = ($months[$key] ?? 0) + $count;
    }
    ksort($months);
    $months = array_slice($months, -12, 12, true);
    $monthly = [];
    foreach ($months as $key => $count) {
        $monthly[] = ['label' => date('M', strtotime($key . '-01')), 'count' => $count];
    }
    $total = array_sum(array_map(fn ($d) => (int) ($d['count'] ?? 0), $days));

    $owned = [];
    if (is_array($repos)) {
        foreach ($repos as $repo) {
            if (!empty($repo['fork']) || !empty($repo['archived'])) {
                continue;
            }
            $owned[] = $repo;
        }
    }
    $recent = [];
    foreach (array_slice($owned, 0, 9) as $repo) {
        $recent[] = [
            'name' => $repo['name'] ?? '',
            'description' => $repo['description'] ?? '',
            'url' => $repo['html_url'] ?? '',
            'stars' => (int) ($repo['stargazers_count'] ?? 0),
            'forks' => (int) ($repo['forks_count'] ?? 0),
            'language' => $repo['language'] ?? '',
            'pushed' => $repo['pushed_at'] ?? '',
        ];
    }

    $langUrls = [];
    foreach (array_slice($owned, 0, 8) as $repo) {
        $name = rawurlencode((string) ($repo['name'] ?? ''));
        $langUrls[] = 'https://api.github.com/repos/' . rawurlencode($username) . '/' . $name . '/languages';
    }
    $totals = [];
    foreach (github_http_many($langUrls, $auth) as $payload) {
        if (!is_array($payload)) {
            continue;
        }
        foreach ($payload as $name => $bytes) {
            if ($name === '' || !is_numeric($bytes)) {
                continue;
            }
            $totals[$name] = ($totals[$name] ?? 0) + (int) $bytes;
        }
    }
    if (!$totals) {
        foreach ($owned as $repo) {
            $name = (string) ($repo['language'] ?? '');
            if ($name !== '') {
                $totals[$name] = ($totals[$name] ?? 0) + 1;
            }
        }
    }
    arsort($totals);
    $sum = array_sum($totals) ?: 1;
    $languages = [];
    foreach (array_slice($totals, 0, 8, true) as $name => $size) {
        $languages[] = [
            'name' => $name,
            'size' => $size,
            'percent' => round($size / $sum * 100, 1),
            'color' => github_color($name),
        ];
    }

    $busiest = $monthly ? array_reduce($monthly, fn ($best, $row) => $row['count'] > ($best['count'] ?? -1) ? $row : $best, $monthly[0]) : null;
    $topLanguage = $languages[0]['name'] ?? '';
    $analysis = 'Public activity covers the last year of commits on GitHub.';
    if ($topLanguage !== '') {
        $analysis = $topLanguage . ' is the largest share of the public code';
        if ($busiest) {
            $analysis .= ', and ' . $busiest['label'] . ' was the busiest month with ' . number_format((int) $busiest['count']) . ' contributions';
        }
        $analysis .= '.';
    }

    $data = [
        'version' => 2,
        'ok' => (bool) $user,
        'name' => $user['name'] ?? $username,
        'url' => $user['html_url'] ?? ('https://github.com/' . $username),
        'repos' => (int) ($user['public_repos'] ?? 0),
        'followers' => (int) ($user['followers'] ?? 0),
        'following' => (int) ($user['following'] ?? 0),
        'total' => $total,
        'last90' => $last90,
        'pulls' => (int) ($pulls['total_count'] ?? 0),
        'issues' => (int) ($issues['total_count'] ?? 0),
        'currentStreak' => $currentStreak,
        'longestStreak' => $longestStreak,
        'weeks' => $weeks,
        'monthly' => $monthly,
        'languages' => $languages,
        'recent' => $recent,
        'analysis' => $analysis,
        'generated' => date('c'),
    ];

    if (!is_dir(dirname($cacheFile))) {
        mkdir(dirname($cacheFile), 0755, true);
    }
    if ($data['ok']) {
        file_put_contents($cacheFile, json_encode($data));
    }
    return $data;
}
