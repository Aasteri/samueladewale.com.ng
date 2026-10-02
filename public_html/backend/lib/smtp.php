<?php
declare(strict_types=1);

function smtp_send(array $cfg, string $to, string $subject, string $text, string $html, string $replyTo): void
{
    $host = $cfg['smtp_host'];
    $port = (int) ($cfg['smtp_port'] ?? 465);
    $user = $cfg['smtp_user'];
    $pass = $cfg['smtp_pass'];
    $from = $cfg['smtp_from'] ?: $user;
    $remote = ($port === 465 ? 'ssl://' : '') . $host . ':' . $port;
    $fp = stream_socket_client($remote, $errno, $errstr, 20, STREAM_CLIENT_CONNECT);
    if (!$fp) {
        throw new RuntimeException("SMTP connect failed: $errstr");
    }
    stream_set_timeout($fp, 20);

    $read = function () use ($fp): string {
        $data = '';
        while ($line = fgets($fp, 515)) {
            $data .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $data;
    };
    $cmd = function (string $command) use ($fp, $read): string {
        fwrite($fp, $command . "\r\n");
        return $read();
    };

    $read();
    $cmd('EHLO samueladewale.com.ng');
    if ($port !== 465) {
        $cmd('STARTTLS');
        stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $cmd('EHLO samueladewale.com.ng');
    }
    $cmd('AUTH LOGIN');
    $cmd(base64_encode($user));
    $auth = $cmd(base64_encode($pass));
    if (!str_starts_with($auth, '235')) {
        fclose($fp);
        throw new RuntimeException('SMTP authentication failed.');
    }

    if (!preg_match('/<([^>]+)>/', $from, $m)) {
        $fromAddr = $user;
        $fromHeader = $from;
    } else {
        $fromAddr = $m[1];
        $fromHeader = $from;
    }

    $cmd('MAIL FROM:<' . $fromAddr . '>');
    $cmd('RCPT TO:<' . $to . '>');
    $cmd('DATA');

    $boundary = 'bnd_' . bin2hex(random_bytes(8));
    $message = implode("\r\n", [
        'From: ' . $fromHeader,
        'To: <' . $to . '>',
        'Reply-To: ' . $replyTo,
        'Subject: ' . $subject,
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        '',
        '--' . $boundary,
        'Content-Type: text/plain; charset=UTF-8',
        '',
        $text,
        '',
        '--' . $boundary,
        'Content-Type: text/html; charset=UTF-8',
        '',
        $html,
        '',
        '--' . $boundary . '--',
        '.',
    ]);
    $result = $cmd($message);
    $cmd('QUIT');
    fclose($fp);
    if (!str_starts_with($result, '250')) {
        throw new RuntimeException('SMTP rejected the message.');
    }
}
