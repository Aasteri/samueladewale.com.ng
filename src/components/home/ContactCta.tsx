import Link from "next/link";
import { Download, Mail, Briefcase, FileText } from "lucide-react";
import { Container } from "@/components/Container";
import { primaryCv } from "@/data/cvs";
import { site } from "@/lib/site";

export function ContactCta() {
  return (
    <section className="section">
      <Container>
        <div className="surface-strong relative overflow-hidden p-7 sm:p-14">
          <div
            aria-hidden
            className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-cyan-brand to-transparent"
          />
          <div
            aria-hidden
            className="pointer-events-none absolute -bottom-32 -right-16 h-72 w-72 rounded-full bg-cyan-brand/[0.06] blur-3xl"
          />
          <div className="relative grid gap-10 lg:grid-cols-[1.4fr_1fr] lg:items-center">
            <div className="max-w-2xl">
              <p className="eyebrow">Let&apos;s build something</p>
              <h2 className="mt-3 heading-2 text-balance">
                Need a senior engineer who can lead, build, and ship?
              </h2>
              <p className="mt-4 text-base leading-relaxed text-white/70 sm:text-lg">
                I work with teams that need a hands-on developer who can also
                talk to stakeholders, scope realistic delivery, and get to
                production.
              </p>

              <div className="mt-7 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                <Link href="/contact" className="btn-primary w-full sm:w-auto">
                  <Briefcase className="h-4 w-4" aria-hidden />
                  Hire Me
                </Link>
                <a
                  href={`mailto:${site.email}`}
                  className="btn-ghost w-full sm:w-auto"
                >
                  <Mail className="h-4 w-4" aria-hidden />
                  Send Email
                </a>
              </div>
            </div>

            <div className="surface p-6 sm:p-7">
              <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
                CV & documents
              </p>
              <p className="mt-3 text-base font-semibold text-white">
                Grab the CV that fits the role.
              </p>
              <p className="mt-2 text-sm leading-relaxed text-white/60">
                Full-stack, frontend, technical lead, and consulting —
                ready to download.
              </p>
              <div className="mt-5 flex flex-col gap-2.5">
                <a
                  href={primaryCv.pdf}
                  className="btn-ghost w-full"
                  download={primaryCv.filename}
                >
                  <Download className="h-4 w-4" aria-hidden />
                  Download CV (PDF)
                </a>
                <Link href="/cvs" className="btn-ghost w-full">
                  <FileText className="h-4 w-4" aria-hidden />
                  Browse all CVs
                </Link>
              </div>
            </div>
          </div>
        </div>
      </Container>
    </section>
  );
}
