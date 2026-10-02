import type { LucideIcon } from "lucide-react";
import {
  Calendar,
  Building2,
  Layers,
  Rocket,
  Compass,
  Smartphone,
} from "lucide-react";

export type ImpactCard = {
  icon: LucideIcon;
  metric: string;
  label: string;
};

export const impactCards: ImpactCard[] = [
  {
    icon: Calendar,
    metric: "7+ years",
    label: "software development experience",
  },
  {
    icon: Building2,
    metric: "Enterprise & government",
    label: "platforms delivered end-to-end",
  },
  {
    icon: Layers,
    metric: "Full-stack web",
    label: "Next.js, React, Node.js, Laravel, APIs, ERP, compliance",
  },
  {
    icon: Smartphone,
    metric: "Mobile apps",
    label: "Flutter and React Native — iOS, Android, offline-first",
  },
  {
    icon: Rocket,
    metric: "MVPs",
    label: "delivered under tight launch deadlines",
  },
  {
    icon: Compass,
    metric: "Technical leadership",
    label: "across teams, clients, and stakeholders",
  },
];

export const strengths: Array<{
  title: string;
  description: string;
}> = [
  {
    title: "Frontend Engineering",
    description:
      "Next.js, React, TypeScript, and Tailwind for fast, accessible, conversion-focused UIs.",
  },
  {
    title: "Mobile Engineering",
    description:
      "Flutter and React Native for cross-platform iOS / Android apps with offline-first patterns.",
  },
  {
    title: "Backend & APIs",
    description:
      "Node.js and Laravel for resilient APIs, workflows, and integrations.",
  },
  {
    title: "Data & Persistence",
    description:
      "PostgreSQL, MySQL, and reporting-ready data models for enterprise workloads.",
  },
  {
    title: "Cloud & DevOps",
    description:
      "Vercel-native delivery, environment hygiene, and CI/CD pipelines.",
  },
  {
    title: "Architecture & Leadership",
    description:
      "System design, code review, mentorship, and stakeholder communication.",
  },
];
