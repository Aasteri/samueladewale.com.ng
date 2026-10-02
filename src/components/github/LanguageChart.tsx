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
import type { LanguageBreakdown } from "@/lib/github";

export function LanguageChart({ data }: { data: LanguageBreakdown[] }) {
  if (!data.length) {
    return (
      <div className="grid h-72 place-items-center text-sm text-white/55">
        Language data will appear once GitHub data is available.
      </div>
    );
  }

  return (
    <div className="h-72 w-full">
      <ResponsiveContainer width="100%" height="100%">
        <BarChart
          data={data}
          layout="vertical"
          margin={{ top: 8, right: 16, bottom: 0, left: 8 }}
        >
          <CartesianGrid
            stroke="rgba(255,255,255,0.08)"
            strokeDasharray="3 3"
            horizontal={false}
          />
          <XAxis
            type="number"
            stroke="rgba(255,255,255,0.45)"
            tickLine={false}
            axisLine={false}
            tick={{ fontSize: 12 }}
            domain={[0, "dataMax"]}
            tickFormatter={(v: number) => `${Math.round(v)}%`}
          />
          <YAxis
            type="category"
            dataKey="name"
            stroke="rgba(255,255,255,0.45)"
            tickLine={false}
            axisLine={false}
            width={92}
            tick={{ fontSize: 12 }}
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
            formatter={(value: number) => [
              `${value.toFixed(1)}%`,
              "Share",
            ]}
          />
          <Bar dataKey="percent" radius={[0, 6, 6, 0]}>
            {data.map((d) => (
              <Cell key={d.name} fill={d.color} />
            ))}
          </Bar>
        </BarChart>
      </ResponsiveContainer>
    </div>
  );
}
