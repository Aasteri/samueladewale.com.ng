import type { Config } from "tailwindcss";

const config: Config = {
  content: ["./src/**/*.{ts,tsx,mdx}"],
  theme: {
    container: {
      center: true,
      padding: {
        DEFAULT: "1rem",
        sm: "1.5rem",
        lg: "2rem",
      },
      screens: {
        sm: "640px",
        md: "768px",
        lg: "1024px",
        xl: "1200px",
        "2xl": "1280px",
      },
    },
    extend: {
      colors: {
        navy: {
          DEFAULT: "#0B1220",
          50: "#E6E8EE",
          100: "#C0C5D2",
          200: "#9AA1B6",
          300: "#737D9A",
          400: "#4D597D",
          500: "#273561",
          600: "#1E293B",
          700: "#152040",
          800: "#0B1220",
          900: "#060A14",
        },
        slate: {
          ink: "#1E293B",
        },
        cyan: {
          brand: "#22D3EE",
        },
        emerald: {
          impact: "#34D399",
        },
        ink: "#F1F5F9",
        muted: "#94A3B8",
        canvas: "#060912",
      },
      fontFamily: {
        sans: ["var(--font-sans)", "system-ui", "sans-serif"],
        display: ["var(--font-display)", "system-ui", "sans-serif"],
        mono: ["var(--font-mono)", "ui-monospace", "monospace"],
      },
      boxShadow: {
        soft: "0 1px 2px rgba(15, 23, 42, 0.04), 0 8px 24px -8px rgba(15, 23, 42, 0.08)",
        ring: "0 0 0 1px rgba(15, 23, 42, 0.06), 0 8px 32px -12px rgba(15, 23, 42, 0.16)",
        glow: "0 0 0 1px rgba(6, 182, 212, 0.25), 0 12px 40px -16px rgba(6, 182, 212, 0.45)",
      },
      backgroundImage: {
        "grid-light":
          "linear-gradient(to right, rgba(15, 23, 42, 0.06) 1px, transparent 1px), linear-gradient(to bottom, rgba(15, 23, 42, 0.06) 1px, transparent 1px)",
        "radial-spot":
          "radial-gradient(circle at top right, rgba(6, 182, 212, 0.18), transparent 60%)",
      },
      keyframes: {
        "fade-up": {
          "0%": { opacity: "0", transform: "translateY(8px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        shimmer: {
          "0%": { backgroundPosition: "-200% 0" },
          "100%": { backgroundPosition: "200% 0" },
        },
      },
      animation: {
        "fade-up": "fade-up 0.5s ease-out both",
        shimmer: "shimmer 2.4s linear infinite",
      },
    },
  },
  plugins: [],
};

export default config;
