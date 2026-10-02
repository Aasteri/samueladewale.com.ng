import type { ReactNode } from "react";

type Props = {
  eyebrow?: string;
  title: string;
  description?: string;
  align?: "left" | "center";
  action?: ReactNode;
};

export function SectionHeading({
  eyebrow,
  title,
  description,
  align = "left",
  action,
}: Props) {
  const isCenter = align === "center";
  return (
    <div
      className={`flex flex-col gap-4 ${
        isCenter
          ? "items-center text-center"
          : "items-start sm:flex-row sm:items-end sm:justify-between"
      }`}
    >
      <div className={isCenter ? "max-w-2xl" : "max-w-2xl"}>
        {eyebrow && <p className="eyebrow">{eyebrow}</p>}
        <h2 className="mt-2 heading-2 text-balance">{title}</h2>
        {description && (
          <p className="mt-3 text-base leading-relaxed text-white/65 sm:text-lg">
            {description}
          </p>
        )}
      </div>
      {action && <div className="shrink-0">{action}</div>}
    </div>
  );
}
