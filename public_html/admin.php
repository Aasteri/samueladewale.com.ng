<?php
declare(strict_types=1);

require __DIR__ . '/backend/bootstrap.php';
require __DIR__ . '/backend/lib/smtp.php';
require __DIR__ . '/backend/lib/inbox.php';
require __DIR__ . '/backend/lib/messages.php';
require __DIR__ . '/backend/lib/content.php';

session_start();

function admin_logged_in(): bool
{
    return !empty($_SESSION['admin']);
}

function admin_pick(string $key, array $allowed, string $default): string
{
    $value = (string) ($_POST[$key] ?? $_GET[$key] ?? $default);
    return in_array($value, $allowed, true) ? $value : $default;
}

function admin_href(array $extra = []): string
{
    global $inboxQuery;
    $params = array_merge($inboxQuery, $extra);
    foreach ($params as $key => $value) {
        if ($value === '' || $value === null) {
            unset($params[$key]);
        }
        if ($key === 'status' && $value === 'all') {
            unset($params[$key]);
        }
        if ($key === 'view' && $value === 'chat') {
            unset($params[$key]);
        }
        if ($key === 'page' && (string) $value === '1') {
            unset($params[$key]);
        }
    }
    $query = http_build_query($params);
    return '/admin' . ($query !== '' ? '?' . $query : '');
}

function admin_initials(string $name): string
{
    $parts = preg_split('/\s+/', trim($name)) ?: [];
    $letters = '';
    foreach (array_slice($parts, 0, 2) as $part) {
        $letters .= strtoupper(substr($part, 0, 1));
    }
    return $letters !== '' ? $letters : '?';
}

function admin_hue(string $name): int
{
    return hexdec(substr(md5($name), 0, 2)) % 360;
}

function admin_when(?string $iso, bool $short = true): string
{
    $time = strtotime((string) $iso);
    if (!$time) {
        return '';
    }
    if (!$short) {
        return date('j M Y, H:i', $time);
    }
    if (date('Y-m-d', $time) === date('Y-m-d')) {
        return date('H:i', $time);
    }
    if (date('Y', $time) === date('Y')) {
        return date('j M', $time);
    }
    return date('j M Y', $time);
}

function admin_day(?string $iso): string
{
    $time = strtotime((string) $iso);
    if (!$time) {
        return '';
    }
    if (date('Y-m-d', $time) === date('Y-m-d')) {
        return 'Today';
    }
    if (date('Y-m-d', $time) === date('Y-m-d', strtotime('-1 day'))) {
        return 'Yesterday';
    }
    return date('l, j M Y', $time);
}

function admin_preview(array $row): string
{
    $replies = $row['replies'] ?? [];
    if ($replies) {
        $last = $replies[count($replies) - 1];
        return 'You: ' . (string) ($last['body'] ?? '');
    }
    return (string) ($row['message'] ?? '');
}

function admin_activity(array $row): string
{
    $replies = $row['replies'] ?? [];
    if ($replies) {
        return (string) ($replies[count($replies) - 1]['sent_at'] ?? $row['created_at'] ?? '');
    }
    return (string) ($row['created_at'] ?? '');
}

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: /admin');
    exit;
}

$view = admin_pick('view', ['chat', 'table'], 'chat');
$status = admin_pick('status', ['all', 'open', 'replied'], 'all');
$q = trim((string) ($_GET['q'] ?? $_POST['q'] ?? ''));
$id = trim((string) ($_GET['id'] ?? $_POST['id'] ?? ''));
$page = max(1, (int) ($_GET['page'] ?? $_POST['page'] ?? 1));
$inboxQuery = [
    'view' => $view,
    'status' => $status,
    'q' => $q,
    'page' => (string) $page,
    'id' => $id,
];

$error = '';
$notice = isset($_GET['sent']) ? 'Reply emailed.' : '';
$draft = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && !admin_logged_in()) {
    $user = trim((string) ($_POST['username'] ?? ''));
    $pass = (string) ($_POST['password'] ?? '');
    $hash = cfg('admin_pass_hash', '');
    if ($user === cfg('admin_user', 'samuel') && $hash && password_verify($pass, $hash)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        header('Location: /admin');
        exit;
    }
    $error = 'Those details did not match.';
}

if (admin_logged_in() && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['reply'], $_POST['id'])) {
    $token = (string) ($_POST['csrf'] ?? '');
    $item = inbox_find((string) $_POST['id']);
    $reply = trim((string) $_POST['reply']);
    $draft = $reply;
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        $error = 'The form expired. Refresh and try again.';
    } elseif (!$item) {
        $error = 'That message was not found.';
    } elseif ($reply === '') {
        $error = 'Write a reply first.';
    } else {
        [$text, $html] = reply_email($item, $reply);
        try {
            smtp_send(
                smtp_config(),
                $item['email'],
                'Re: your message to Samuel Adewale',
                $text,
                $html,
                cfg('smtp_user', 'hello@samueladewale.com.ng')
            );
            inbox_update($item['id'], function (array $row) use ($reply) {
                $row['replies'][] = ['body' => $reply, 'sent_at' => date('c')];
                return $row;
            });
            header('Location: ' . admin_href(['id' => $item['id'], 'sent' => '1']));
            exit;
        } catch (Throwable $err) {
            $error = 'The reply could not be sent. Check the mailbox password in backend/config.php.';
        }
    }
}

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$messages = inbox_all();
$needle = strtolower($q);
$searched = array_values(array_filter($messages, function (array $row) use ($needle): bool {
    if ($needle === '') {
        return true;
    }
    $haystack = strtolower(implode(' ', [
        $row['name'] ?? '',
        $row['email'] ?? '',
        $row['company'] ?? '',
        $row['opportunity'] ?? '',
        $row['message'] ?? '',
    ]));
    return str_contains($haystack, $needle);
}));
$counts = ['all' => count($searched), 'open' => 0, 'replied' => 0];
foreach ($searched as $row) {
    $counts[count($row['replies'] ?? []) > 0 ? 'replied' : 'open']++;
}
$filtered = array_values(array_filter($searched, function (array $row) use ($status): bool {
    $replied = count($row['replies'] ?? []) > 0;
    if ($status === 'open') {
        return !$replied;
    }
    if ($status === 'replied') {
        return $replied;
    }
    return true;
}));

$perPage = $view === 'table' ? 10 : 15;
$pages = max(1, (int) ceil(count($filtered) / $perPage));
$pageGiven = isset($_GET['page']) || isset($_POST['page']);
if ($id !== '' && !$pageGiven) {
    foreach ($filtered as $index => $row) {
        if (($row['id'] ?? '') === $id) {
            $page = (int) floor($index / $perPage) + 1;
            break;
        }
    }
}
$page = min($page, $pages);
$inboxQuery['page'] = (string) $page;
$slice = array_slice($filtered, ($page - 1) * $perPage, $perPage);
$from = $filtered ? (($page - 1) * $perPage) + 1 : 0;
$to = min(count($filtered), $page * $perPage);
$open = $id !== '' ? inbox_find($id) : null;

function admin_pager(int $page, int $pages): void
{
    $windowStart = max(1, $page - 2);
    $windowEnd = min($pages, $windowStart + 4);
    $windowStart = max(1, $windowEnd - 4);
    $prev = max(1, $page - 1);
    $next = min($pages, $page + 1);
    echo '<nav class="ib-pager" aria-label="Pages">';
    echo '<a class="' . ($page <= 1 ? 'is-off' : '') . '" href="' . e(admin_href(['page' => (string) $prev, 'sent' => ''])) . '">Prev</a>';
    for ($i = $windowStart; $i <= $windowEnd; $i++) {
        $class = $i === $page ? ' class="is-on"' : '';
        echo '<a' . $class . ' href="' . e(admin_href(['page' => (string) $i, 'sent' => ''])) . '">' . $i . '</a>';
    }
    echo '<a class="' . ($page >= $pages ? 'is-off' : '') . '" href="' . e(admin_href(['page' => (string) $next, 'sent' => ''])) . '">Next</a>';
    echo '</nav>';
}

$bodyClass = 'ib' . ($open ? ' is-thread' : '') . ($view === 'table' ? ' is-table' : '');
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex" />
  <title>Inbox | Samuel Adewale</title>
  <link rel="stylesheet" href="/assets/admin.css?v=6" />
</head>
<body class="<?= e($bodyClass) ?>">
<?php if (!admin_logged_in()): ?>
  <div class="ib-login">
    <form method="post" class="ib-card">
      <p class="ib-kicker">Private</p>
      <h1>Inbox</h1>
      <?php if ($error): ?><p class="ib-banner bad"><?= e($error) ?></p><?php endif; ?>
      <label for="username">Username</label>
      <input id="username" name="username" required autocomplete="username" />
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required autocomplete="current-password" />
      <button type="submit">Open inbox</button>
    </form>
  </div>
<?php else: ?>
  <div class="ib-shell">
    <?php admin_side('inbox'); ?>
    <div class="ib-stage">
  <div class="ib-app">
    <header class="ib-top">
      <div class="ib-brand">
        <strong>Inbox</strong>
        <span><?= count($messages) ?> conversation<?= count($messages) === 1 ? '' : 's' ?></span>
      </div>
      <nav class="ib-views" aria-label="Layout">
        <a class="<?= $view === 'chat' ? 'is-on' : '' ?>" href="<?= e(admin_href(['view' => 'chat', 'page' => '1', 'sent' => ''])) ?>">Chats</a>
        <a class="<?= $view === 'table' ? 'is-on' : '' ?>" href="<?= e(admin_href(['view' => 'table', 'page' => '1', 'sent' => ''])) ?>">Table</a>
      </nav>
      <a class="ib-logout" href="/admin?logout=1">Log out</a>
    </header>

    <?php if ($error): ?><p class="ib-banner bad"><?= e($error) ?></p><?php endif; ?>
    <?php if ($notice): ?><p class="ib-banner ok"><?= e($notice) ?></p><?php endif; ?>

    <form class="ib-tools" method="get" action="/admin">
      <input type="hidden" name="view" value="<?= e($view) ?>" />
      <input type="hidden" name="status" value="<?= e($status) ?>" />
      <?php if ($id !== ''): ?><input type="hidden" name="id" value="<?= e($id) ?>" /><?php endif; ?>
      <label class="ib-search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3-3"/></svg>
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, email, company, or message" />
      </label>
      <div class="ib-filters">
        <?php foreach (['all' => 'All', 'open' => 'Awaiting', 'replied' => 'Replied'] as $key => $label): ?>
          <a class="<?= $status === $key ? 'is-on' : '' ?>" href="<?= e(admin_href(['status' => $key, 'page' => '1', 'sent' => ''])) ?>"><?= e($label) ?> <b><?= (int) $counts[$key] ?></b></a>
        <?php endforeach; ?>
      </div>
      <p class="ib-count"><?php if ($filtered): ?>Showing <?= $from ?> to <?= $to ?> of <?= count($filtered) ?><?php else: ?>No matches<?php endif; ?></p>
    </form>

    <div class="ib-main">
      <?php if ($view === 'table'): ?>
        <div class="ib-table-wrap">
          <table class="ib-table">
            <thead>
              <tr>
                <th>From</th>
                <th>Company</th>
                <th>Opportunity</th>
                <th>Message</th>
                <th>Received</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$slice): ?>
                <tr><td colspan="6"><p class="ib-empty">No messages match this search.</p></td></tr>
              <?php endif; ?>
              <?php foreach ($slice as $row): ?>
                <?php $replied = count($row['replies'] ?? []) > 0; ?>
                <tr>
                  <td class="ib-person">
                    <a href="<?= e(admin_href(['view' => 'chat', 'id' => $row['id'], 'page' => '', 'sent' => ''])) ?>"><?= e($row['name']) ?></a>
                    <small><?= e($row['email']) ?></small>
                  </td>
                  <td><?= e($row['company'] ?: 'Not given') ?></td>
                  <td><?= e($row['opportunity'] ?: 'Not given') ?></td>
                  <td><span class="ib-excerpt"><?= e($row['message']) ?></span></td>
                  <td><?= e(admin_when($row['created_at'] ?? '', false)) ?></td>
                  <td><span class="ib-pill <?= $replied ? 'done' : 'open' ?>"><?= $replied ? count($row['replies']) . ' replied' : 'Awaiting' ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <?php if ($pages > 1) admin_pager($page, $pages); ?>
        </div>
      <?php else: ?>
        <aside class="ib-list">
          <div class="ib-threads">
            <?php if (!$slice): ?>
              <p class="ib-empty">No conversations yet. New contact-form messages will show up here.</p>
            <?php endif; ?>
            <?php foreach ($slice as $row): ?>
              <?php $replied = count($row['replies'] ?? []) > 0; ?>
              <a class="ib-thread<?= ($open['id'] ?? '') === $row['id'] ? ' is-on' : '' ?><?= $replied ? '' : ' is-new' ?>" href="<?= e(admin_href(['id' => $row['id'], 'sent' => ''])) ?>">
                <span class="ib-avatar" style="background: hsl(<?= admin_hue($row['name'] ?? '') ?> 42% 32%)"><?= e(admin_initials($row['name'] ?? '')) ?></span>
                <span>
                  <span class="ib-thread-name"><strong><?= e($row['name'] ?? '') ?></strong></span>
                  <p><?= e(admin_preview($row)) ?></p>
                </span>
                <span class="ib-meta">
                  <time><?= e(admin_when(admin_activity($row))) ?></time>
                  <?php if (!$replied): ?><span class="ib-dot"></span><?php endif; ?>
                </span>
              </a>
            <?php endforeach; ?>
          </div>
          <?php if ($pages > 1) admin_pager($page, $pages); ?>
        </aside>
        <section class="ib-chat">
          <?php if (!$open): ?>
            <div class="ib-blank">
              <h2>Select a conversation</h2>
              <p>Messages from the contact form appear on the left. Replies are emailed back in the site design.</p>
            </div>
          <?php else: ?>
            <?php $replied = count($open['replies'] ?? []) > 0; ?>
            <header class="ib-chat-head">
              <a class="ib-back" href="<?= e(admin_href(['id' => '', 'sent' => ''])) ?>">Back</a>
              <span class="ib-avatar" style="background: hsl(<?= admin_hue($open['name'] ?? '') ?> 42% 32%)"><?= e(admin_initials($open['name'] ?? '')) ?></span>
              <div class="ib-who">
                <h2><?= e($open['name']) ?></h2>
                <a href="mailto:<?= e($open['email']) ?>"><?= e($open['email']) ?></a>
              </div>
              <div class="ib-tags">
                <span><?= e($open['company'] ?: 'No company') ?></span>
                <span><?= e($open['opportunity'] ?: 'No role given') ?></span>
                <span class="ib-pill <?= $replied ? 'done' : 'open' ?>"><?= $replied ? 'Replied' : 'Awaiting reply' ?></span>
              </div>
            </header>
            <div class="ib-transcript" id="transcript">
              <?php
                $items = [['kind' => 'in', 'body' => $open['message'], 'at' => $open['created_at'] ?? '']];
                foreach ($open['replies'] ?? [] as $sent) {
                    $items[] = ['kind' => 'out', 'body' => $sent['body'] ?? '', 'at' => $sent['sent_at'] ?? ''];
                }
                $lastDay = '';
                foreach ($items as $item):
                    $day = admin_day($item['at']);
                    if ($day !== $lastDay):
                        $lastDay = $day;
              ?>
                <p class="ib-day"><?= e($day) ?></p>
              <?php endif; ?>
                <div class="ib-bubble <?= $item['kind'] === 'out' ? 'out' : 'in' ?>">
                  <div class="bubble"><?= e($item['body']) ?><small><?= $item['kind'] === 'out' ? 'You · ' : '' ?><?= e(admin_when($item['at'], false)) ?></small></div>
                </div>
              <?php endforeach; ?>
            </div>
            <form class="ib-composer" method="post" action="<?= e(admin_href(['sent' => ''])) ?>">
              <input type="hidden" name="id" value="<?= e($open['id']) ?>" />
              <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>" />
              <input type="hidden" name="view" value="<?= e($view) ?>" />
              <input type="hidden" name="status" value="<?= e($status) ?>" />
              <input type="hidden" name="q" value="<?= e($q) ?>" />
              <input type="hidden" name="page" value="<?= (int) $page ?>" />
              <textarea name="reply" rows="1" required placeholder="Write a reply. Enter sends it. Shift+Enter adds a line."><?= e($draft) ?></textarea>
              <button class="ib-send" type="submit">Send</button>
            </form>
          <?php endif; ?>
        </section>
      <?php endif; ?>
    </div>
  </div>
    </div>
  </div>
  <script>
    var box = document.querySelector(".ib-composer textarea");
    var thread = document.getElementById("transcript");
    if (thread) thread.scrollTop = thread.scrollHeight;
    if (box) {
      var fit = function () { box.style.height = "auto"; box.style.height = Math.min(box.scrollHeight, 140) + "px"; };
      box.addEventListener("input", fit);
      box.addEventListener("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); box.form.requestSubmit(); }
      });
      fit();
    }
  </script>
<?php endif; ?>
</body>
</html>
