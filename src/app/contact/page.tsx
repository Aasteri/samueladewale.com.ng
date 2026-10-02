import type { Metadata } from "next";
import Image from "next/image";
import {
  Mail,
  Phone,
  Linkedin,
  Github,
  MapPin,
  CalendarClock,
} from "lucide-react";
import { Container } from "@/components/Container";
import { PageHeader } from "@/components/PageHeader";
import { Reveal } from "@/components/Reveal";
import { ContactForm } from "@/components/ContactForm";
import { site } from "@/lib/site";

export const metadata: Metadata = {
  title: "Contact",
  description:
    "Get in touch with Samuel Adewale for roles, partnerships, consulting, or technical leadership opportunities.",
  alternates: { canonical: "/contact" },
};

export default function ContactPage() {
  return (
    <>
      <PageHeader
        eyebrow="Contact"
        title="Let's talk."
        description="For roles, partnerships, consulting, or technical leadership opportunities, send a message and I will respond as soon as possible."
      />

      <Container className="py-12">
        <div className="grid gap-8 lg:grid-cols-[1.1fr_0.9fr]">
          <Reveal>
            <ContactForm />
          </Reveal>

          <Reveal delay={0.05}>
            <aside className="space-y-4">
              <div className="surface relative overflow-hidden p-5">
                <div className="flex items-center gap-4">
                  <div className="media-frame h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-white/10 bg-[#0a0e1a]">
                    <Image
                      src={site.portrait.primary}
                      alt={`Portrait of ${site.name}`}
                      fill
                      sizes="64px"
                      className="object-cover object-center"
                    />
                  </div>
                  <div className="min-w-0 flex-1">
                    <p className="truncate text-sm font-semibold text-white">
                      {site.name}
                    </p>
                    <p className="truncate text-xs text-cyan-brand">
                      {site.tagline}
                    </p>
                    <p className="mt-1 inline-flex items-center gap-1.5 text-[11px] text-white/55">
                      <span className="relative flex h-1.5 w-1.5 shrink-0">
                        <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-brand opacity-75" />
                        <span className="relative inline-flex h-1.5 w-1.5 rounded-full bg-cyan-brand" />
                      </span>
                      <span className="truncate">
                        Usually replies within a day
                      </span>
                    </p>
                  </div>
                </div>
              </div>

              <ContactRow
                icon={Mail}
                label="Email"
                value={site.email}
                href={`mailto:${site.email}`}
              />
              <ContactRow
                icon={Phone}
                label="Phone"
                value={site.phone}
                href={`tel:${site.phone.replace(/\s+/g, "")}`}
              />
              <ContactRow
                icon={Linkedin}
                label="LinkedIn"
                value="linkedin.com/in/samueladewale"
                href={site.linkedin}
              />
              <ContactRow
                icon={Github}
                label="GitHub"
                value={`github.com/${site.github}`}
                href={`https://github.com/${site.github}`}
              />
              <ContactRow
                icon={MapPin}
                label="Location"
                value={site.location}
              />
              <ContactRow
                icon={CalendarClock}
                label="Availability"
                value={site.availability}
              />
            </aside>
          </Reveal>
        </div>
      </Container>
    </>
  );
}

function ContactRow({
  icon: Icon,
  label,
  value,
  href,
}: {
  icon: React.ComponentType<{ className?: string; "aria-hidden"?: boolean }>;
  label: string;
  value: string;
  href?: string;
}) {
  const content = (
    <div className="surface flex items-start gap-4 p-5">
      <span className="grid h-10 w-10 shrink-0 place-items-center rounded-lg border border-white/10 bg-white/[0.04] text-cyan-brand">
        <Icon className="h-5 w-5" aria-hidden />
      </span>
      <div className="min-w-0 flex-1">
        <p className="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-brand">
          {label}
        </p>
        <p className="mt-1 break-words text-sm font-medium text-white">
          {value}
        </p>
      </div>
    </div>
  );

  if (href) {
    return (
      <a
        href={href}
        target={href.startsWith("http") ? "_blank" : undefined}
        rel={href.startsWith("http") ? "noreferrer noopener" : undefined}
        className="block transition-transform hover:-translate-y-0.5"
      >
        {content}
      </a>
    );
  }
  return content;
}
