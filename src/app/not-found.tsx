import Link from "next/link";
import { Container } from "@/components/Container";

export default function NotFound() {
  return (
    <Container className="grid min-h-[60vh] place-items-center py-20 text-center">
      <div>
        <p className="font-mono text-sm uppercase tracking-[0.18em] text-cyan-brand">
          404
        </p>
        <h1 className="mt-3 heading-1">This page wasn&apos;t found.</h1>
        <p className="mt-3 text-white/65">
          The link may be broken or the page may have moved. Try one of these
          instead.
        </p>
        <div className="mt-8 grid grid-cols-1 gap-3 sm:flex sm:flex-wrap sm:justify-center">
          <Link href="/" className="btn-primary w-full sm:w-auto">
            Go home
          </Link>
          <Link href="/projects" className="btn-ghost w-full sm:w-auto">
            View Projects
          </Link>
          <Link href="/contact" className="btn-ghost w-full sm:w-auto">
            Contact Me
          </Link>
        </div>
      </div>
    </Container>
  );
}
