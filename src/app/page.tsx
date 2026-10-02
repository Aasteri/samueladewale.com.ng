import type { Metadata } from "next";
import { Hero } from "@/components/home/Hero";
import { Outcomes } from "@/components/home/Outcomes";
import { FeaturedProjects } from "@/components/home/FeaturedProjects";
import { TimelinePreview } from "@/components/home/TimelinePreview";
import { ContactCta } from "@/components/home/ContactCta";
import { site } from "@/lib/site";

export const metadata: Metadata = {
  title: site.title,
  description: site.description,
  alternates: { canonical: "/" },
  openGraph: {
    title: site.title,
    description: site.description,
    url: site.url,
    type: "website",
  },
};

export default function HomePage() {
  return (
    <>
      <Hero />
      <Outcomes />
      <FeaturedProjects />
      <TimelinePreview />
      <ContactCta />
    </>
  );
}
