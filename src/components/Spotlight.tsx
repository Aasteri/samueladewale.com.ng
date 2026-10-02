"use client";

import { useEffect } from "react";

/**
 * Spotlight effect: tracks the pointer and writes `--mouse-x` / `--mouse-y`
 * onto the `html` element. `globals.css` reads those vars in `body::after`
 * to paint a soft radial gradient that follows the cursor.
 *
 * No state, no re-renders. Disabled by CSS on touch and reduced-motion.
 */
export function Spotlight() {
  useEffect(() => {
    if (typeof window === "undefined") return;
    if (
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches
    ) {
      return;
    }
    if (
      window.matchMedia &&
      window.matchMedia("(hover: none)").matches
    ) {
      return;
    }

    const root = document.documentElement;
    let raf = 0;
    let lastX = 0;
    let lastY = 0;

    const apply = () => {
      raf = 0;
      root.style.setProperty("--mouse-x", `${lastX}px`);
      root.style.setProperty("--mouse-y", `${lastY}px`);
    };

    const onMove = (e: PointerEvent) => {
      lastX = e.clientX;
      lastY = e.clientY;
      if (!raf) raf = requestAnimationFrame(apply);
    };

    window.addEventListener("pointermove", onMove, { passive: true });
    return () => {
      window.removeEventListener("pointermove", onMove);
      if (raf) cancelAnimationFrame(raf);
    };
  }, []);

  return null;
}
