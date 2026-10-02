export type CvDoc = {
  title: string;
  bestFor: string;
  pdf: string;
  /** Filename for the download attribute (so browsers save with a clean name). */
  filename?: string;
  docx?: string;
};

export const cvs: CvDoc[] = [
  {
    title: "Senior Full-Stack Developer CV",
    bestFor:
      "Best for software engineer, full-stack developer, backend/frontend hybrid, and web application roles.",
    pdf: "/cvs/Samuel_Adewale_Senior_Full_Stack_Developer_CV.pdf",
    filename: "Samuel_Adewale_Senior_Full_Stack_Developer_CV.pdf",
  },
  {
    title: "Senior Frontend / React Developer CV",
    bestFor:
      "Best for React, Next.js, frontend engineer, UI engineer, and product engineer roles.",
    pdf: "/cvs/Samuel_Adewale_Senior_Frontend_React_Developer_CV.pdf",
    filename: "Samuel_Adewale_Senior_Frontend_React_Developer_CV.pdf",
  },
  {
    title: "Technical Lead / Solutions Architect CV",
    bestFor:
      "Best for technical lead, engineering manager, solutions architect, and platform leadership roles.",
    pdf: "/cvs/Samuel_Adewale_Technical_Lead_Solutions_Architect_CV.pdf",
    filename: "Samuel_Adewale_Technical_Lead_Solutions_Architect_CV.pdf",
  },
  {
    title: "Tech Consultant / Technical Project Manager CV",
    bestFor:
      "Best for technical consulting, technical project manager, delivery lead, and transformation roles.",
    pdf: "/cvs/Samuel_Adewale_Tech_Consultant_Technical_Project_Manager_CV.pdf",
    filename: "Samuel_Adewale_Tech_Consultant_Technical_Project_Manager_CV.pdf",
  },
  {
    title: "Executive Tech Portfolio PDF",
    bestFor:
      "High-level portfolio summary of projects, impact, and capabilities — ideal for executives and decision-makers.",
    pdf: "/cvs/Samuel_Adewale_Executive_Tech_Portfolio.pdf",
    filename: "Samuel_Adewale_Executive_Tech_Portfolio.pdf",
  },
];

export const primaryCv = cvs[0];
