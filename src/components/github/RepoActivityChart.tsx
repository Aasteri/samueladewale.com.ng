"use client";

import {
  ResponsiveContainer,
  BarChart,
  Bar,
  CartesianGrid,
  XAxis,
  YAxis,
  Tooltip,
  Cell,
} from "recharts";
import type { RepoSummary } from "@/lib/github";

function daysSince(iso: string) {
  const then = new Date(iso).getTime();
  const now = Date.now();
  return Math.max(0, Math.floor((now - then) / (1000 * 60 * 60 * 24)));
}

export function RepoActivityChart({ repos }: { repos: RepoSummary[] }) {
  if (!repos.length) {
    return (
      <div className="grid h-72 place-items-center text-sm text-white/55">
        Recent repository activity will appear here.
      </div>
    );
  }
  const data = repos.map((r) => ({
    name: r.name,
    recencyScore: Math.max(1, 365 - daysSince(r.pushedAt)),
    daysAgo: daysSince(r.pushedAt),
    color: r.primaryLanguage?.color || "#22D3EE",
  }));

  return (
    <div className="h-72 w-full">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart
          data={data}
          margin={{ top: 8, right: 16, bottom: 32, left: 0 }}
        >
          <CartesianGrid
            stroke="rgba(255,255,255,0.08)"
            strokeDasharray="3 3"
            vertical={false}
          />
          <XAxis
            dataKey="name"
            stroke="rgba(255,255,255,0.45)"
            tickLine={false}
            axisLine={false}
            tick={{ fontSize: 11 }}
            angle={-20}
            textAnchor="end"
            interval={0}
            height={50}
          />
          <YAxis
            stroke="rgba(255,255,255,0.45)"
            tickLine={false}
            axisLine={false}
            tick={{ fontSize: 12 }}
            tickFormatter={(v: number) => (v >= 1 ? `${365 - v}d` : "")}
            domain={[0, 365]}
          />
          <Tooltip
            cursor={{ fill: "rgba(255,255,255,0.04)" }}
            contentStyle={{
              borderRadius: 12,
              border: "1px solid rgba(255,255,255,0.12)",
              backgroundColor: "rgba(10,14,26,0.95)",
              boxShadow: "0 8px 24px -8px rgba(0,0,0,0.6)",
              fontSize: 12,
              color: "#F1F5F9",
            }}
            itemStyle={{ color: "#F1F5F9" }}
            labelStyle={{ color: "#F1F5F9" }}
            formatter={(_v: number, _n, ctx) => {
              const days =
                (ctx?.payload as { daysAgo: number } | undefined)?.daysAgo ?? 0;
              return [`${days} day${days === 1 ? "" : "s"} ago`, "Last push"];
            }}
          />
          <Bar dataKey="recencyScore" radius={[6, 6, 0, 0]}>
            {data.map((d) => (
              <Cell key={d.name} fill={d.color} />
            ))}
          </Bar>
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
