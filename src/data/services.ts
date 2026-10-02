import type { LucideIcon } from "lucide-react";
import {
  Code2,
  Compass,
  Building2,
  Rocket,
  Lightbulb,
  Smartphone,
} from "lucide-react";

export type Service = {
  title: string;
  icon: LucideIcon;
  summary: string;
  bullets: string[];
};

export const services: Service[] = [
  {
    title: "Full-Stack Web Application Development",
    icon: Code2,
    summary:
      "React, Next.js, Node.js, Laravel, APIs, dashboards, portals, and admin systems.",
    bullets: [
      "Production-grade Next.js / React applications",
      "Robust Node.js and Laravel APIs",
      "Operational dashboards, portals, and admin systems",
      "Authentication, RBAC, and audit-friendly architectures",
    ],
  },
  {
    title: "Mobile App Development",
    icon: Smartphone,
    summary:
      "Cross-platform mobile apps for iOS and Android — Flutter and React Native — including offline-first field tools and customer apps.",
    bullets: [
      "Cross-platform Flutter and React Native delivery",
      "Offline-first sync for field and low-connectivity use cases",
      "Integrations with REST APIs, payments, push notifications",
      "App Store and Play Store release engineering",
    ],
  },
  {
    title: "Technical Leadership and Architecture",
    icon: Compass,
    summary:
      "System design, code review, team mentorship, delivery planning, and architecture documentation.",
    bullets: [
      "System architecture and technical decision support",
      "Code review and engineering standards",
      "Delivery planning and milestone management",
      "Mentorship and knowledge transfer to client teams",
    ],
  },
  {
    title: "Enterprise and Government Digital Platforms",
    icon: Building2,
    summary:
      "Workflow systems, compliance platforms, regulatory portals, ERP modules, and data visibility tools.",
    bullets: [
      "Workflow and approval systems",
      "Compliance and regulatory portals",
      "ERP-style modules and back-office automation",
      "Reporting, analytics, and data visibility tooling",
    ],
  },
  {
    title: "MVP Development",
    icon: Rocket,
    summary:
      "Fast product validation for startups, healthtech, fintech, and internal business tools.",
    bullets: [
      "From idea to working MVP under tight deadlines",
      "Lean, focused scoping and prioritisation",
      "Stack choices that won't block scale later",
      "Hand-off and training for internal teams",
    ],
  },
  {
    title: "Technical Consulting",
    icon: Lightbulb,
    summary:
      "Requirements gathering, digital transformation planning, system audits, and client training.",
    bullets: [
      "Requirements and stakeholder workshops",
      "Digital transformation roadmaps",
      "System audits and architecture reviews",
      "Hands-on client and end-user training",
    ],
  },
];
