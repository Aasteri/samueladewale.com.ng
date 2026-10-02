/**
 * CloudLinux / Phusion Passenger startup file.
 * Uses the lightweight Next.js standalone server (lower RAM than full next start).
 */
const path = require("path");
const fs = require("fs");

const root = __dirname;
const standaloneDir = path.join(root, ".next", "standalone");
const standaloneServer = path.join(standaloneDir, "server.js");

if (!fs.existsSync(standaloneServer)) {
  console.error(
    "[samueladewale] Standalone server missing.\n" +
      "Build on your PC: npm run build\n" +
      "Then upload the whole project including .next/standalone/\n" +
      "Expected: " +
      standaloneServer
  );
  process.exit(1);
}

process.chdir(standaloneDir);
require(standaloneServer);
