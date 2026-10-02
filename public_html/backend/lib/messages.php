<?php
declare(strict_types=1);

function mail_shell(string $kicker, string $title, string $bodyHtml): string
{
    return '<!doctype html><html><body style="margin:0;background:#060912;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#f1f5f9;">'
        . '<div style="max-width:640px;margin:24px auto;background:#0b1124;border:1px solid rgba(255,255,255,0.08);border-radius:16px;overflow:hidden;">'
        . '<div style="padding:22px 24px;background:#0b1220;border-bottom:1px solid rgba(255,255,255,0.08);">'
        . '<p style="margin:0 0 6px;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#22d3ee;">' . $kicker . '</p>'
        . '<h1 style="margin:0;font-size:22px;line-height:1.3;font-weight:600;color:#ffffff;">' . $title . '</h1>'
        . '</div>'
        . '<div style="padding:22px 24px;font-size:15px;line-height:1.6;color:#e2e8f0;">' . $bodyHtml . '</div>'
        . '<div style="padding:14px 24px;background:#040710;font-size:12px;color:#94a3b8;border-top:1px solid rgba(255,255,255,0.06);">Samuel Adewale · samueladewale.com.ng</div>'
        . '</div></body></html>';
}

function mail_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function enquiry_email(array $fields): array
{
    $name = mail_escape($fields['name']);
    $email = mail_escape($fields['email']);
    $company = mail_escape($fields['company'] ?: 'Not given');
    $opportunity = mail_escape($fields['opportunity'] ?: 'Not given');
    $message = nl2br(mail_escape($fields['message']));
    $html = mail_shell(
        'samueladewale.com.ng',
        'New enquiry from ' . $name,
        '<table style="width:100%;border-collapse:collapse;margin-bottom:16px;">'
        . row_html('Name', $name)
        . row_html('Email', '<a href="mailto:' . $email . '" style="color:#22d3ee;">' . $email . '</a>')
        . row_html('Company', $company)
        . row_html('Opportunity', $opportunity)
        . '</table><p style="margin:0 0 8px;font-size:12px;letter-spacing:0.14em;text-transform:uppercase;color:#22d3ee;">Message</p><div style="white-space:pre-wrap;">' . $message . '</div>'
    );
    $text = "New enquiry via samueladewale.com.ng\n\nName: {$fields['name']}\nEmail: {$fields['email']}\nCompany: {$fields['company']}\nOpportunity: {$fields['opportunity']}\n\n{$fields['message']}\n";
    return [$text, $html];
}

function reply_email(array $enquiry, string $reply): array
{
    $name = mail_escape($enquiry['name']);
    $replyHtml = nl2br(mail_escape($reply));
    $original = nl2br(mail_escape($enquiry['message']));
    $html = mail_shell(
        'Samuel Adewale',
        'Reply to your message',
        '<p style="margin:0 0 16px;">Hi ' . $name . ',</p><div style="margin:0 0 20px;">' . $replyHtml . '</div>'
        . '<div style="border-left:3px solid #22d3ee;padding:12px 14px;background:rgba(255,255,255,0.03);color:#cbd5e1;"><p style="margin:0 0 8px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#22d3ee;">Your message</p>' . $original . '</div>'
    );
    $text = "Hi {$enquiry['name']},\n\n{$reply}\n\nYour message:\n{$enquiry['message']}\n";
    return [$text, $html];
}

function row_html(string $label, string $value): string
{
    return '<tr><td style="padding:8px 0;color:#94a3b8;font-size:12px;text-transform:uppercase;letter-spacing:0.12em;width:120px;vertical-align:top;">' . $label . '</td><td style="padding:8px 0;color:#f8fafc;">' . $value . '</td></tr>';
}

function smtp_config(): array
{
    return [
        'smtp_host' => cfg('smtp_host'),
        'smtp_port' => cfg('smtp_port', '465'),
        'smtp_user' => cfg('smtp_user'),
        'smtp_pass' => cfg('smtp_pass'),
        'smtp_from' => cfg('smtp_from', cfg('smtp_user')),
    ];
}
