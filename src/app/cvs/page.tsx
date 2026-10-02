import type { Metadata } from "next";
import Link from "next/link";
import { Download, FileText, Mail } from "lucide-react";
import { Container } from "@/components/Container";
import { PageHeader } from "@/components/PageHeader";
import { Reveal } from "@/components/Reveal";
import { SpotlightCard } from "@/components/SpotlightCard";
import { cvs } from "@/data/cvs";
import { site } from "@/lib/site";

export const metadata: Metadata = {
  title: "CVs and Professional Documents",
  description:
    "Download tailored CVs and a portfolio PDF — full-stack, frontend, technical lead, and consulting versions.",
  alternates: { canonical: "/cvs" },
};

export default function CvsPage() {
  return (
    <>
      <PageHeader
        eyebrow="CVs & documents"
        title="CVs and Professional Documents"
        description="One profile, multiple stories. Pick the version that matches the role or engagement you have in mind."
      />

      <Container className="py-12">
        <ul className="grid gap-6 md:grid-cols-2">
          {cvs.map((doc, i) => (
            <Reveal as="li" key={doc.title} delay={i * 0.04}>
              <SpotlightCard
                as="article"
                className="surface card-hover h-full rounded-2xl p-7"
              >
                <div className="grid h-12 w-12 place-items-center rounded-xl border border-white/10 bg-white/[0.04] text-cyan-brand">
                  <FileText className="h-6 w-6" aria-hidden />
                </div>
                <h2 className="mt-5 heading-3">{doc.title}</h2>
                <p className="mt-2 leading-relaxed text-white/65">
                  {doc.bestFor}
                </p>
                <div className="mt-6 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap">
                  <a
                    href={doc.pdf}
                    className="btn-primary w-full sm:w-auto"
                    download={doc.filename}
                  >
                    <Download className="h-4 w-4" aria-hidden />
                    Download PDF
                  </a>
                  {doc.docx && (
                    <a
                      href={doc.docx}
                      className="btn-ghost w-full sm:w-auto"
                      download
                    >
                      Download DOCX
                    </a>
                  )}
                  <Link
                    href="/contact"
                    className="btn-ghost w-full sm:w-auto"
                  >
                    <Mail className="h-4 w-4" aria-hidden />
                    Contact Samuel
                  </Link>
                </div>
              </SpotlightCard>
            </Reveal>
          ))}
        </ul>

        <Reveal>
          <aside className="mt-10 surface flex flex-col items-start justify-between gap-4 p-6 sm:mt-12 sm:flex-row sm:items-center">
            <p className="text-sm text-white/65">
              Not sure which CV fits? Send me the role brief and I&apos;ll
              send the best version back.
            </p>
            <a
              href={`mailto:${site.email}`}
              className="btn-primary w-full sm:w-auto"
            >
              <Mail className="h-4 w-4" aria-hidden /> Email Samuel
            </a>
          </aside>
        </Reveal>
      </Container>
    </>
  );
}
