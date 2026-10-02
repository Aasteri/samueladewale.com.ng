import fs from "fs";

const envPath = ".env.local";
const out = "public_html/backend/config.php";
const map = {
  NEXT_PUBLIC_SITE_URL: "site_url",
  NEXT_PUBLIC_GITHUB_USERNAME: "github_username",
  GITHUB_TOKEN: "github_token",
  CONTACT_EMAIL: "contact_email",
  SMTP_HOST: "smtp_host",
  SMTP_PORT: "smtp_port",
  SMTP_USER: "smtp_user",
  SMTP_PASS: "smtp_pass",
  SMTP_FROM: "smtp_from",
};
const cfg = {};
if (fs.existsSync(envPath)) {
  for (const line of fs.readFileSync(envPath, "utf8").split(/\r?\n/)) {
    const t = line.trim();
    if (!t || t.startsWith("#") || !t.includes("=")) continue;
    const i = t.indexOf("=");
    const key = t.slice(0, i).trim();
    let val = t.slice(i + 1).trim();
    if ((val.startsWith('"') && val.endsWith('"')) || (val.startsWith("'") && val.endsWith("'"))) {
      val = val.slice(1, -1);
    }
    val = val.replace(/\\\$/g, "$");
    if (map[key]) cfg[map[key]] = val;
  }
}
function phpString(s) {
  return "'" + String(s).replace(/\\/g, "\\\\").replace(/'/g, "\\'") + "'";
}
const lines = Object.entries(cfg).map(([k, v]) => `  ${phpString(k)} => ${phpString(v)},`);
const php = "<?php\nreturn [\n" + lines.join("\n") + "\n];\n";
fs.writeFileSync(out, php);
console.log("config keys", Object.keys(cfg).join(","));
