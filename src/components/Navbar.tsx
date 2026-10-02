"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useEffect, useState } from "react";
import { Menu, X } from "lucide-react";
import { site } from "@/lib/site";
import { Container } from "./Container";

export function Navbar() {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    setOpen(false);
  }, [pathname]);

  return (
    <header
      className={`sticky top-0 z-50 transition-all duration-300 ${
        scrolled
          ? "border-b border-white/[0.06] bg-[#060912]/80 backdrop-blur-md"
          : "bg-transparent"
      }`}
    >
      <Container className="flex h-16 items-center justify-between gap-3 sm:h-20 sm:gap-4">
        <Link
          href="/"
          className="group flex min-w-0 items-center gap-2"
          aria-label="Samuel Adewale — Home"
        >
          <span
            aria-hidden
            className="grid h-9 w-9 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.04] font-mono text-sm font-semibold text-cyan-brand transition-transform group-hover:-translate-y-0.5"
          >
            SA
          </span>
          <span className="flex min-w-0 flex-col leading-tight">
            <span className="truncate text-sm font-semibold text-white">
              Samuel Adewale
            </span>
            <span className="hidden truncate text-[11px] font-medium uppercase tracking-[0.14em] text-white/45 sm:inline">
              Full-Stack · Lead · Consultant
            </span>
          </span>
        </Link>

        <nav className="hidden lg:block" aria-label="Primary">
          <ul className="flex items-center gap-1">
            {site.navigation.map((item) => {
              const active =
                item.href === "/"
                  ? pathname === "/"
                  : pathname.startsWith(item.href);
              return (
                <li key={item.href}>
                  <Link
                    href={item.href}
                    className={`relative inline-flex h-9 items-center rounded-lg px-3 text-sm font-medium transition-colors ${
                      active
                        ? "text-white"
                        : "text-white/60 hover:text-white"
                    }`}
                  >
                    {item.label}
                    {active && (
                      <span className="absolute inset-x-3 -bottom-px h-0.5 rounded-full bg-cyan-brand" />
                    )}
                  </Link>
                </li>
              );
            })}
          </ul>
        </nav>

        <div className="hidden lg:block">
          <Link href="/contact" className="btn-primary">
            Hire Me
          </Link>
        </div>

        <button
          type="button"
          aria-label={open ? "Close menu" : "Open menu"}
          aria-expanded={open}
          onClick={() => setOpen((v) => !v)}
          className="grid h-10 w-10 place-items-center rounded-lg border border-white/15 bg-white/[0.04] text-white lg:hidden"
        >
          {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
        </button>
      </Container>

      {open && (
        <div className="lg:hidden">
          <Container className="pb-4">
            <nav
              className="surface flex flex-col gap-1 p-3"
              aria-label="Mobile primary"
            >
              {site.navigation.map((item) => {
                const active =
                  item.href === "/"
                    ? pathname === "/"
                    : pathname.startsWith(item.href);
                return (
                  <Link
                    key={item.href}
                    href={item.href}
                    className={`rounded-lg px-3 py-2 text-sm font-medium transition-colors ${
                      active
                        ? "bg-white/[0.06] text-white"
                        : "text-white/65 hover:bg-white/[0.04] hover:text-white"
                    }`}
                  >
                    {item.label}
                  </Link>
                );
              })}
              <Link href="/contact" className="btn-primary mt-2">
                Hire Me
              </Link>
            </nav>
          </Container>
        </div>
      )}
    </header>
  );
}
