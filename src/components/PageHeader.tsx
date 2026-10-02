import type { ReactNode } from "react";
import { Container } from "./Container";

type PageHeaderProps = {
  eyebrow?: string;
  title: string;
  description?: string;
  actions?: ReactNode;
};

export function PageHeader({
  eyebrow,
  title,
  description,
  actions,
}: PageHeaderProps) {
  return (
    <section className="relative overflow-hidden border-b border-white/[0.06] pt-12 pb-14 sm:pt-16 sm:pb-20">
      <div aria-hidden className="grid-bg pointer-events-none absolute inset-0" />
      <Container className="relative">
        {eyebrow && <p className="eyebrow">{eyebrow}</p>}
        <h1 className="mt-3 heading-1 text-balance">{title}</h1>
        {description && (
          <p className="mt-4 max-w-3xl lead text-pretty">{description}</p>
        )}
        {actions && <div className="mt-8 flex flex-wrap gap-3">{actions}</div>}
      </Container>
    </section>
  );
}
