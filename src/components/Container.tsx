import type { ComponentPropsWithoutRef } from "react";

type ContainerProps = ComponentPropsWithoutRef<"div"> & {
  size?: "default" | "prose" | "wide";
};

export function Container({
  size = "default",
  className = "",
  ...rest
}: ContainerProps) {
  const sizes = {
    default: "container max-w-6xl",
    prose: "container max-w-4xl",
    wide: "container max-w-7xl",
  } as const;

  return <div className={`${sizes[size]} ${className}`} {...rest} />;
}
