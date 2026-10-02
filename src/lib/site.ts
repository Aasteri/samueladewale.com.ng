export const site = {
  name: "Samuel Adewale",
  shortName: "Samuel Adewale",
  portrait: {
    primary: "/img/Adewale_Samuel_Professional_Shot_2.jpg.jpeg",
    secondary: "/img/Adewale_Samuel_Professional_Shot.jpg.jpeg",
  },
  title:
    "Samuel Adewale | Senior Full-Stack Developer & Tech Consultant",
  description:
    "Portfolio of Samuel Adewale, a senior full-stack developer, technical lead, and tech consultant based in Abuja, Nigeria, experienced in enterprise, government, healthtech, ERP, and digital transformation platforms.",
  url:
    process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/$/, "") ||
    "https://samueladewale.com.ng",
  ogImage: "/opengraph-image",
  email: "hello@samueladewale.com.ng",
  phone: "+234 813 423 7131",
  location: "Abuja, Nigeria",
  github:
    process.env.NEXT_PUBLIC_GITHUB_USERNAME?.replace(/^@/, "") ||
    "aasteri",
  linkedin: "https://www.linkedin.com/in/samueladewale",
  availability:
    "Remote, Hybrid, Contract, Full-time, Consulting",
  tagline:
    "Senior Full-Stack Developer, Technical Lead, Tech Consultant",
  headline:
    "I build web and mobile platforms for enterprise, government, and healthtech teams. From national portals and ERPs to healthtech MVPs.",
  trustLine: [
    "7+ years",
    "Enterprise & government",
    "Web & mobile delivery",
  ],
  navigation: [
    { href: "/", label: "Home" },
    { href: "/about", label: "About" },
    { href: "/projects", label: "Projects" },
    { href: "/github-activity", label: "GitHub Activity" },
    { href: "/services", label: "Services" },
    { href: "/cvs", label: "CVs" },
    { href: "/contact", label: "Contact" },
  ],
  footerLinks: [
    { href: "/projects", label: "Projects" },
    { href: "/github-activity", label: "GitHub Activity" },
    { href: "/cvs", label: "CVs" },
    { href: "/contact", label: "Contact" },
  ],
};

export type SiteConfig = typeof site;
