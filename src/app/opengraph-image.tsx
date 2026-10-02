import { ImageResponse } from "next/og";
import { site } from "@/lib/site";

export const runtime = "edge";
export const alt =
  "Samuel Adewale — Senior Full-Stack Developer, Technical Lead & Tech Consultant";
export const size = { width: 1200, height: 630 };
export const contentType = "image/png";

export default async function OpenGraphImage() {
  // Resolve the portrait as a data URL so the edge ImageResponse can embed
  // it without making a cross-host request at render time.
  let portraitDataUri: string | null = null;
  try {
    const portraitUrl = `${site.url}${site.portrait.primary}`;
    const res = await fetch(portraitUrl);
    if (res.ok) {
      const buffer = await res.arrayBuffer();
      const base64 = Buffer.from(buffer).toString("base64");
      const type = res.headers.get("content-type") || "image/jpeg";
      portraitDataUri = `data:${type};base64,${base64}`;
    }
  } catch {
    portraitDataUri = null;
  }

  return new ImageResponse(
    (
      <div
        style={{
          width: "100%",
          height: "100%",
          display: "flex",
          background:
            "linear-gradient(135deg, #0B1220 0%, #0B1220 60%, #1E293B 100%)",
          color: "white",
          fontFamily:
            "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif",
        }}
      >
        {/* Left: content */}
        <div
          style={{
            flex: 1,
            display: "flex",
            flexDirection: "column",
            justifyContent: "space-between",
            padding: "64px",
          }}
        >
          <div style={{ display: "flex", alignItems: "center", gap: 16 }}>
            <div
              style={{
                width: 56,
                height: 56,
                borderRadius: 14,
                background: "#06B6D4",
                color: "#0B1220",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                fontSize: 22,
                fontWeight: 700,
              }}
            >
              SA
            </div>
            <div style={{ display: "flex", flexDirection: "column" }}>
              <span style={{ fontSize: 18, fontWeight: 600 }}>
                {site.name}
              </span>
              <span
                style={{
                  fontSize: 12,
                  color: "#94A3B8",
                  letterSpacing: 2,
                  textTransform: "uppercase",
                }}
              >
                samueladewale.com.ng
              </span>
            </div>
          </div>

          <div style={{ display: "flex", flexDirection: "column", gap: 14 }}>
            <span style={{ fontSize: 22, color: "#06B6D4" }}>
              Senior Full-Stack Developer · Technical Lead · Tech Consultant
            </span>
            <span
              style={{
                fontSize: 46,
                lineHeight: 1.1,
                fontWeight: 700,
                letterSpacing: -1.2,
                maxWidth: 620,
              }}
            >
              I design, build, and lead enterprise, government, and digital
              transformation platforms.
            </span>
          </div>

          <div
            style={{
              display: "flex",
              gap: 10,
              color: "#94A3B8",
              fontSize: 16,
              flexWrap: "wrap",
            }}
          >
            <span>7+ years</span>
            <span>·</span>
            <span>Enterprise & government</span>
            <span>·</span>
            <span>Web & mobile</span>
            <span>·</span>
            <span>Flutter & React Native</span>
          </div>
        </div>

        {/* Right: portrait */}
        <div
          style={{
            width: 480,
            height: "100%",
            display: "flex",
            position: "relative",
          }}
        >
          {portraitDataUri ? (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={portraitDataUri}
              alt=""
              width={480}
              height={630}
              style={{
                width: "100%",
                height: "100%",
                objectFit: "cover",
                objectPosition: "center top",
              }}
            />
          ) : (
            <div
              style={{
                width: "100%",
                height: "100%",
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                background:
                  "linear-gradient(180deg, #1E293B 0%, #0B1220 100%)",
                color: "#06B6D4",
                fontSize: 96,
                fontWeight: 700,
              }}
            >
              SA
            </div>
          )}
          <div
            style={{
              position: "absolute",
              inset: 0,
              display: "flex",
              background:
                "linear-gradient(270deg, transparent 60%, rgba(11,18,32,0.85) 100%)",
            }}
          />
        </div>
      </div>
    ),
    { ...size }
  );
}
