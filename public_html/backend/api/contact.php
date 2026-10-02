<?php
declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__) . '/lib/smtp.php';
require dirname(__DIR__) . '/lib/inbox.php';
require dirname(__DIR__) . '/lib/messages.php';

header('Content-Type: application/json; charset=utf-8');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed.']);
    exit;
}

$raw = file_get_contents('php://input') ?: '';
$body = json_decode($raw, true);
if (!is_array($body)) {
    $body = $_POST;
}

// A hidden field named like a real input gets autofilled by browsers.
// Discard only when that field is filled and the page script did not send the form.
$trap = trim((string) ($body['company_website'] ?? $body['website'] ?? ''));
$fromPage = ($body['sent_from_page'] ?? '') === 'contact';
if ($trap !== '' && !$fromPage) {
    echo json_encode(['ok' => true, 'message' => "Thanks. Your message is queued. I'll get back to you shortly."]);
    exit;
}

$name = trim((string) ($body['name'] ?? ''));
$email = trim((string) ($body['email'] ?? ''));
$company = trim((string) ($body['company'] ?? ''));
$opportunity = trim((string) ($body['opportunity'] ?? ''));
$message = trim((string) ($body['message'] ?? ''));

if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Please fill in your name, email, and message.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => "That doesn't look like a valid email address."]);
    exit;
}
if (strlen($name) > 120 || strlen($message) > 5000) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Message too long.']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? 'anonymous';
$rateFile = dirname(__DIR__) . '/cache/rate.json';
$hits = is_file($rateFile) ? json_decode((string) file_get_contents($rateFile), true) : [];
if (!is_array($hits)) {
    $hits = [];
}
$now = time();
$hits[$ip] = array_values(array_filter($hits[$ip] ?? [], fn ($t) => $now - (int) $t < 60));
$hits[$ip][] = $now;
if (!is_dir(dirname($rateFile))) {
    mkdir(dirname($rateFile), 0755, true);
}
file_put_contents($rateFile, json_encode($hits));
if (count($hits[$ip]) > 4) {
    http_response_code(429);
    echo json_encode(['ok' => false, 'error' => "You're sending messages a little fast. Please wait a minute and try again."]);
    exit;
}

$record = [
    'id' => bin2hex(random_bytes(8)),
    'name' => $name,
    'email' => $email,
    'company' => $company,
    'opportunity' => $opportunity,
    'message' => $message,
    'ip' => $ip,
    'created_at' => date('c'),
    'replies' => [],
];
inbox_add($record);

$host = cfg('smtp_host');
$user = cfg('smtp_user');
$pass = cfg('smtp_pass');
if (!$host || !$user || !$pass) {
    echo json_encode(['ok' => true, 'message' => "Thanks. Your message is queued. I'll get back to you shortly."]);
    exit;
}

[$text, $html] = enquiry_email($record);
$replyTo = '"' . str_replace('"', '', $name) . '" <' . $email . '>';
$subject = 'New portfolio enquiry · ' . $name;
$recipients = array_unique(array_filter([
    cfg('contact_email', $user),
    cfg('notify_email', 'iamthesamuels@gmail.com'),
]));

try {
    foreach ($recipients as $to) {
        smtp_send(smtp_config(), $to, $subject, $text, $html, $replyTo);
    }
    echo json_encode(['ok' => true, 'message' => "Thanks. Your message is on its way. I'll get back to you shortly."]);
} catch (Throwable $err) {
    http_response_code(502);
    echo json_encode(['ok' => false, 'error' => "I couldn't deliver that message right now. Please email me directly at hello@samueladewale.com.ng."]);
}
