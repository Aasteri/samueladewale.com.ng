import fs from "fs";

const s = fs.readFileSync("src/data/projects.ts", "utf8");

function grab(name) {
  const re = new RegExp("export const " + name + "[\\s\\S]*?= ");
  const m = s.match(re);
  if (!m) throw new Error("missing " + name);
  const start = m.index + m[0].length;
  const open = s[start];
  const close = open === "[" ? "]" : "}";
  let depth = 0;
  for (let i = start; i < s.length; i++) {
    if (s[i] === open) depth++;
    else if (s[i] === close) {
      depth--;
      if (depth === 0) return s.slice(start, i + 1);
    }
  }
  throw new Error("unclosed " + name);
}

const projects = eval("(" + grab("projects") + ")");
const projectGroups = eval("(" + grab("projectGroups") + ")");
const noteMatch = s.match(/const CONFIDENTIAL_NOTE =\s*"([\s\S]*?)";/);
const note = noteMatch ? noteMatch[1] : "";

function phpVal(v) {
  if (v === null || v === undefined) return "null";
  if (typeof v === "boolean") return v ? "true" : "false";
  if (typeof v === "number") return String(v);
  if (typeof v === "string") {
    return "'" + v.replace(/\\/g, "\\\\").replace(/'/g, "\\'") + "'";
  }
  if (Array.isArray(v)) {
    if (!v.length) return "[]";
    return (
      "[\n" +
      v.map((x) => "  " + phpVal(x).replace(/\n/g, "\n  ")).join(",\n") +
      ",\n]"
    );
  }
  return (
    "[\n" +
    Object.keys(v)
      .map(
        (k) =>
          "  " + phpVal(k) + " => " + phpVal(v[k]).replace(/\n/g, "\n  ")
      )
      .join(",\n") +
    ",\n]"
  );
}

fs.mkdirSync("public_html/backend/data", { recursive: true });
fs.writeFileSync(
  "public_html/backend/data/projects.php",
  "<?php\nreturn " + phpVal(projects) + ";\n"
);
fs.writeFileSync(
  "public_html/backend/data/groups.php",
  "<?php\nreturn " + phpVal(projectGroups) + ";\n"
);
fs.writeFileSync(
  "public_html/backend/data/confidential.php",
  "<?php\nreturn " + phpVal(note) + ";\n"
);
console.log("projects", projects.length);
