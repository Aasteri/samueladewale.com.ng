"use client";

import type { ContributionWeek } from "@/lib/github";

const LEVEL_CLASSES: Record<0 | 1 | 2 | 3 | 4, string> = {
  0: "bg-white/[0.05] border border-white/[0.05]",
  1: "bg-cyan-brand/25",
  2: "bg-cyan-brand/45",
  3: "bg-cyan-brand/70",
  4: "bg-cyan-brand",
};

const MONTH_LABELS = [
  "Jan",
  "Feb",
  "Mar",
  "Apr",
  "May",
  "Jun",
  "Jul",
  "Aug",
  "Sep",
  "Oct",
  "Nov",
  "Dec",
];

export function ContributionHeatmap({
  weeks,
}: {
  weeks: ContributionWeek[];
}) {
  // Compute month label positions from week starts
  const monthMarkers: Array<{ index: number; label: string }> = [];
  let lastMonth = -1;
  weeks.forEach((w, i) => {
    const m = new Date(w.startDate).getUTCMonth();
    if (m !== lastMonth) {
      monthMarkers.push({ index: i, label: MONTH_LABELS[m] });
      lastMonth = m;
    }
  });

  return (
    <div className="relative">
      <div className="overflow-x-auto pb-2 [scrollbar-width:thin]">
        <div className="inline-block min-w-full">
          <div className="flex gap-1 pl-7 text-[10px] uppercase tracking-[0.18em] text-white/45">
            {weeks.map((_w, i) => {
              const marker = monthMarkers.find((m) => m.index === i);
              return (
                <span
                  key={i}
                  className="block w-[10px] text-left sm:w-[12px]"
                  aria-hidden
                >
                  {marker?.label ?? ""}
                </span>
              );
            })}
          </div>

          <div className="mt-1 flex gap-2">
            <div
              className="grid grid-rows-7 gap-1 pt-[2px] text-[10px] uppercase tracking-[0.16em] text-white/45"
              aria-hidden
            >
              <span />
              <span>Mon</span>
              <span />
              <span>Wed</span>
              <span />
              <span>Fri</span>
              <span />
            </div>

            <div className="flex gap-1">
              {weeks.map((w, wi) => (
                <div key={wi} className="grid grid-rows-7 gap-1">
                  {Array.from({ length: 7 }).map((_, di) => {
                    const day = w.days.find((d) => d.weekday === di);
                    if (!day) {
                      return (
                        <span
                          key={di}
                          className="block h-[10px] w-[10px] rounded-sm bg-transparent sm:h-[12px] sm:w-[12px]"
                        />
                      );
                    }
                    return (
                      <span
                        key={di}
                        title={`${day.date}: ${day.count} contribution${
                          day.count === 1 ? "" : "s"
                        }`}
                        className={`block h-[10px] w-[10px] rounded-sm sm:h-[12px] sm:w-[12px] ${
                          LEVEL_CLASSES[day.level]
                        }`}
                      />
                    );
                  })}
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <div
        aria-hidden
        className="pointer-events-none absolute right-0 top-0 h-full w-8 bg-gradient-to-l from-[#060912] to-transparent sm:hidden"
      />

      <div className="mt-4 flex flex-wrap items-center gap-2 text-xs text-white/55">
        <span>Less</span>
        {[0, 1, 2, 3, 4].map((lvl) => (
          <span
            key={lvl}
            className={`block h-[10px] w-[10px] rounded-sm sm:h-[12px] sm:w-[12px] ${
              LEVEL_CLASSES[lvl as 0 | 1 | 2 | 3 | 4]
            }`}
          />
        ))}
        <span>More</span>
        <span className="ml-auto text-[10px] uppercase tracking-[0.14em] text-white/35 sm:hidden">
          Swipe →
        </span>
      </div>
    </div>
  );
}
