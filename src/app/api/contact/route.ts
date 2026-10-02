import { NextResponse } from "next/server";
import nodemailer, { type Transporter } from "nodemailer";

export const runtime = "nodejs";
export const dynamic = "force-dynamic";

type Body = {
  name?: string;
  email?: string;
  company?: string;
  opportunity?: string;
  message?: string;
  /** Honeypot — bots fill this in. Real users never see it. */
  website?: string;
};

function isEmail(value: string) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
}

function escapeHtml(value: string): string {
  return value
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}

// ─── SMTP transporter (lazily cached per server instance) ────────────────
let cachedTransporter: Transporter | null = null;

function getTransporter(): Transporter | null {
  if (cachedTransporter) return cachedTransporter;

  const host = process.env.SMTP_HOST;
  const port = Number(process.env.SMTP_PORT || 465);
  const user = process.env.SMTP_USER;
  const pass = process.env.SMTP_PASS;

  if (!host || !user || !pass) return null;

  cachedTransporter = nodemailer.createTransport({
    host,
    port,
    // cPanel SSL/TLS on 465 needs `secure: true`. STARTTLS on 587 → `false`.
    secure: port === 465,
    auth: { user, pass },
    // cPanel-hosted SMTP servers occasionally serve self-signed intermediate
    // certs in dev/staging. Strict verification is the default; flip via env
    // only when the deployer explicitly opts in.
    tls:
      process.env.SMTP_INSECURE === "1"
        ? { rejectUnauthorized: false }
        : undefined,
  });

  return cachedTransporter;
}

// ─── Light per-process rate limit (best-effort, in-memory) ───────────────
const RATE_WINDOW_MS = 60_000;
const RATE_MAX = 4;
const hits = new Map<string, number[]>();

function tooManyRequests(key: string): boolean {
  const now = Date.now();
  const list = hits.get(key) ?? [];
  const recent = list.filter((t) => now - t < RATE_WINDOW_MS);
  recent.push(now);
  hits.set(key, recent);
  return recent.length > RATE_MAX;
}

function clientKey(request: Request): string {
  // Vercel forwards the client IP via x-forwarded-for; fall back to anything
  // useful for local development.
  return (
    request.headers.get("x-forwarded-for")?.split(",")[0]?.trim() ||
    request.headers.get("x-real-ip") ||
    "anonymous"
  );
}

// ─── Email body builders ─────────────────────────────────────────────────
function buildText(
  fields: Required<Omit<Body, "website">>,
  meta: { ip: string; userAgent: string }
) {
  const { name, email, company, opportunity, message } = fields;
  return [
    `New enquiry via samueladewale.com.ng`,
    ``,
    `Name:        ${name}`,
    `Email:       ${email}`,
    `Company:     ${company || "—"}`,
    `Opportunity: ${opportunity || "—"}`,
    ``,
    `Message:`,
    message,
    ``,
    `——`,
    `IP:          ${meta.ip}`,
    `User-Agent:  ${meta.userAgent}`,
    `Sent automatically from the portfolio contact form.`,
  ].join("\n");
}

function buildHtml(
  fields: Required<Omit<Body, "website">>,
  meta: { ip: string; userAgent: string }
) {
  const { name, email, company, opportunity, message } = fields;
  const row = (label: string, value: string) => `
    <tr>
      <td style="padding:8px 12px;color:#64748B;font-size:12px;text-transform:uppercase;letter-spacing:0.12em;width:120px;border-bottom:1px solid #E2E8F0;vertical-align:top;">${label}</td>
      <td style="padding:8px 12px;color:#0B1220;font-size:14px;border-bottom:1px solid #E2E8F0;">${value || "—"}</td>
    </tr>`;

  return `<!doctype html>
<html><body style="margin:0;background:#F8FAFC;font-family:-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica,Arial,sans-serif;color:#0B1220;">
  <div style="max-width:640px;margin:24px auto;background:#FFFFFF;border:1px solid #E2E8F0;border-radius:16px;overflow:hidden;">
    <div style="padding:20px 24px;background:#0B1220;color:#FFFFFF;">
      <p style="margin:0 0 4px;font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:#06B6D4;">samueladewale.com.ng</p>
      <h1 style="margin:0;font-size:20px;font-weight:600;">New enquiry from ${escapeHtml(name)}</h1>
    </div>
    <table style="width:100%;border-collapse:collapse;">
      ${row("Name", escapeHtml(name))}
      ${row(
        "Email",
        `<a href="mailto:${escapeHtml(email)}" style="color:#0B1220;">${escapeHtml(email)}</a>`
      )}
      ${row("Company", escapeHtml(company))}
      ${row("Opportunity", escapeHtml(opportunity))}
    </table>
    <div style="padding:16px 24px;">
      <p style="margin:0 0 6px;font-size:12px;text-transform:uppercase;letter-spacing:0.12em;color:#64748B;">Message</p>
      <div style="white-space:pre-wrap;line-height:1.55;font-size:14px;color:#111827;">${escapeHtml(message)}</div>
    </div>
    <div style="padding:12px 24px;background:#F1F5F9;font-size:11px;color:#64748B;border-top:1px solid #E2E8F0;">
      IP: ${escapeHtml(meta.ip)} · UA: ${escapeHtml(meta.userAgent.slice(0, 160))}
    </div>
  </div>
</body></html>`;
}

// ─── Handler ─────────────────────────────────────────────────────────────
export async function POST(request: Request) {
  let body: Body;
  try {
    body = (await request.json()) as Body;
  } catch {
    return NextResponse.json(
      { ok: false, error: "Invalid request body." },
      { status: 400 }
    );
  }

  // Honeypot — bots fill a hidden "website" field; legitimate users don't.
  if (typeof body.website === "string" && body.website.trim().length > 0) {
    return NextResponse.json({
      ok: true,
      message: "Thanks — your message is queued. I'll get back to you shortly.",
    });
  }

  const name = (body.name || "").trim();
  const email = (body.email || "").trim();
  const company = (body.company || "").trim();
  const opportunity = (body.opportunity || "").trim();
  const message = (body.message || "").trim();

  if (!name || !email || !message) {
    return NextResponse.json(
      { ok: false, error: "Please fill in your name, email, and message." },
      { status: 400 }
    );
  }
  if (!isEmail(email)) {
    return NextResponse.json(
      { ok: false, error: "That doesn't look like a valid email address." },
      { status: 400 }
    );
  }
  if (name.length > 120 || message.length > 5000) {
    return NextResponse.json(
      { ok: false, error: "Message too long." },
      { status: 400 }
    );
  }

  const key = clientKey(request);
  if (tooManyRequests(key)) {
    return NextResponse.json(
      {
        ok: false,
        error:
          "You're sending messages a little fast — please wait a minute and try again.",
      },
      { status: 429 }
    );
  }

  const meta = {
    ip: key,
    userAgent: request.headers.get("user-agent") || "unknown",
  };
  const fields = { name, email, company, opportunity, message };
  const text = buildText(fields, meta);
  const html = buildHtml(fields, meta);

  const transporter = getTransporter();
  const toAddress =
    process.env.CONTACT_EMAIL ||
    process.env.SMTP_USER ||
    "hello@samueladewale.com.ng";
  const fromAddress =
    process.env.SMTP_FROM ||
    `Samuel Adewale Portfolio <${process.env.SMTP_USER ?? toAddress}>`;
  const subject = `New portfolio enquiry · ${name}`;

  if (!transporter) {
    // No SMTP configured — log and acknowledge so local dev still works.
    console.log("[contact:dev] SMTP not configured — would have sent:", {
      to: toAddress,
      from: fromAddress,
      subject,
      fields,
    });
    return NextResponse.json({
      ok: true,
      message:
        "Thanks — your message is queued. I'll get back to you shortly.",
    });
  }

  try {
    await transporter.sendMail({
      from: fromAddress,
      to: toAddress,
      replyTo: `"${name}" <${email}>`,
      subject,
      text,
      html,
      headers: {
        "X-Portfolio-Source": "samueladewale.com.ng",
        "X-Portfolio-IP": meta.ip,
      },
    });
    return NextResponse.json({
      ok: true,
      message:
        "Thanks — your message is on its way. I'll get back to you shortly.",
    });
  } catch (err) {
    console.error("[contact:smtp] send failed:", err);
    return NextResponse.json(
      {
        ok: false,
        error:
          "I couldn't deliver that message right now. Please email me directly at hello@samueladewale.com.ng.",
      },
      { status: 502 }
    );
  }
}
