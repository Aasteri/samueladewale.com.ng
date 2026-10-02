"use client";

import { useEffect, useRef, type ElementType, type ReactNode } from "react";

type Props = {
  as?: ElementType;
  className?: string;
  children: ReactNode;
};

/**
 * Wraps a card-like element and tracks the cursor *relative to the card*
 * on mousemove, writing `--card-x` and `--card-y` as inline styles.
 *
 * Combined with the `.spotlight-card` rules in globals.css, this gives
 * each card its own soft cyan light that follows the cursor on hover.
 *
 * Runs only when a fine pointer is present. Throttled by rAF so it stays
 * cheap even with many cards on the page.
 */
export function SpotlightCard({
  as: Tag = "div",
  className = "",
  children,
}: Props) {
  const ref = useRef<HTMLElement>(null);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    if (typeof window === "undefined") return;
    if (window.matchMedia && window.matchMedia("(hover: none)").matches) {
      return;
    }

    let raf = 0;
    let lastX = -200;
    let lastY = -200;

    const apply = () => {
      raf = 0;
      el.style.setProperty("--card-x", `${lastX}px`);
      el.style.setProperty("--card-y", `${lastY}px`);
    };

    const onMove = (e: MouseEvent) => {
      const rect = el.getBoundingClientRect();
      lastX = e.clientX - rect.left;
      lastY = e.clientY - rect.top;
      if (!raf) raf = requestAnimationFrame(apply);
    };

    el.addEventListener("mousemove", onMove);
    return () => {
      el.removeEventListener("mousemove", onMove);
      if (raf) cancelAnimationFrame(raf);
    };
  }, []);

  return (
    <Tag ref={ref} className={`spotlight-card ${className}`}>
      {children}
    </Tag>
  );
}
