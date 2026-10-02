"use client";

import { useState } from "react";
import { Send, CheckCircle2, AlertCircle, Loader2 } from "lucide-react";

type Status = "idle" | "submitting" | "success" | "error";

export function ContactForm() {
  const [status, setStatus] = useState<Status>("idle");
  const [message, setMessage] = useState<string>("");

  async function onSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    if (status === "submitting") return;

    const form = e.currentTarget;
    setStatus("submitting");
    setMessage("");

    const payload = Object.fromEntries(new FormData(form).entries());

    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), 30000);

    try {
      const res = await fetch("/api/contact", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload),
        signal: controller.signal,
      });
      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        setStatus("error");
        setMessage(
          data?.error ||
            "Something went wrong. Please email me directly at hello@samueladewale.com.ng."
        );
        return;
      }

      setStatus("success");
      setMessage(
        data?.message ||
          "Thanks — your message is queued. I'll get back to you shortly."
      );
      form.reset();
    } catch (err) {
      const aborted =
        err instanceof DOMException && err.name === "AbortError";
      setStatus("error");
      setMessage(
        aborted
          ? "The server took too long to respond. Please try again or email me at hello@samueladewale.com.ng."
          : "Couldn't reach the contact server. Please email me directly at hello@samueladewale.com.ng."
      );
    } finally {
      clearTimeout(timer);
    }
  }

  return (
    <form
      onSubmit={onSubmit}
      className="relative surface p-5 sm:p-7"
      noValidate
    >
      {/* Honeypot — hidden from real users; bots gleefully fill it in. */}
      <div
        aria-hidden="true"
        className="absolute -left-[9999px] top-auto h-0 w-0 overflow-hidden"
      >
        <label htmlFor="website">Leave this field empty</label>
        <input
          id="website"
          name="website"
          type="text"
          tabIndex={-1}
          autoComplete="off"
        />
      </div>

      <div className="grid gap-4 sm:grid-cols-2">
        <Field label="Name" name="name" required type="text" autoComplete="name" />
        <Field
          label="Email"
          name="email"
          required
          type="email"
          autoComplete="email"
        />
        <Field
          label="Company"
          name="company"
          type="text"
          autoComplete="organization"
        />
        <Field
          label="Role or opportunity type"
          name="opportunity"
          type="text"
          placeholder="e.g. Senior Full-Stack role, MVP build, Consulting"
        />
      </div>

      <div className="mt-4">
        <label
          htmlFor="message"
          className="block text-sm font-medium text-white"
        >
          Message <span className="text-cyan-brand">*</span>
        </label>
        <textarea
          id="message"
          name="message"
          required
          rows={6}
          className="mt-1.5 w-full resize-y rounded-xl border border-white/10 bg-white/[0.04] px-3.5 py-3 text-base text-white placeholder:text-white/40 focus:border-cyan-brand/60 focus:outline-none focus:ring-2 focus:ring-cyan-brand/30 sm:text-sm"
          placeholder="Tell me a little about your team, project, or role…"
        />
      </div>

      <div className="mt-6 flex flex-col items-stretch gap-3 sm:flex-row sm:items-center sm:justify-between">
        <button
          type="submit"
          disabled={status === "submitting"}
          aria-busy={status === "submitting"}
          className="btn-primary w-full sm:w-auto"
        >
          {status === "submitting" ? (
            <Loader2 className="h-4 w-4 animate-spin" aria-hidden />
          ) : (
            <Send className="h-4 w-4" aria-hidden />
          )}
          {status === "submitting" ? "Sending message…" : "Send Message"}
        </button>

        {status === "submitting" && (
          <p
            role="status"
            className="text-sm text-white/60"
            aria-live="polite"
          >
            Delivering to my inbox — this can take a few seconds.
          </p>
        )}

        {status === "success" && (
          <p
            role="status"
            className="inline-flex items-start gap-2 text-sm text-emerald-400"
          >
            <CheckCircle2 className="mt-0.5 h-4 w-4" aria-hidden />
            <span>{message}</span>
          </p>
        )}
        {status === "error" && (
          <p
            role="alert"
            className="inline-flex items-start gap-2 text-sm text-red-400"
          >
            <AlertCircle className="mt-0.5 h-4 w-4" aria-hidden />
            <span>{message}</span>
          </p>
        )}
      </div>
    </form>
  );
}

function Field({
  label,
  name,
  type = "text",
  required,
  placeholder,
  autoComplete,
}: {
  label: string;
  name: string;
  type?: string;
  required?: boolean;
  placeholder?: string;
  autoComplete?: string;
}) {
  const id = `f-${name}`;
  return (
    <div>
      <label htmlFor={id} className="block text-sm font-medium text-white">
        {label} {required && <span className="text-cyan-brand">*</span>}
      </label>
      <input
        id={id}
        name={name}
        type={type}
        required={required}
        placeholder={placeholder}
        autoComplete={autoComplete}
        className="mt-1.5 h-12 w-full rounded-xl border border-white/10 bg-white/[0.04] px-3.5 text-base text-white placeholder:text-white/40 focus:border-cyan-brand/60 focus:outline-none focus:ring-2 focus:ring-cyan-brand/30 sm:h-11 sm:text-sm"
      />
    </div>
  );
}
