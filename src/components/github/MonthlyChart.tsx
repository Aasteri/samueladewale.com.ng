"use client";

import {
  ResponsiveContainer,
  AreaChart,
  Area,
  CartesianGrid,
  XAxis,
  YAxis,
  Tooltip,
} from "recharts";
import type { MonthlyActivity } from "@/lib/github";

export function MonthlyChart({ data }: { data: MonthlyActivity[] }) {
  return (
    <div className="h-72 w-full">
      <ResponsiveContainer width="100%" height="100%">
        <AreaChart
          data={data}
          margin={{ top: 8, right: 12, bottom: 0, left: 0 }}
        >
          <defs>
            <linearGradient id="g-cyan" x1="0" y1="0" x2="0" y2="1">
              <stop offset="0%" stopColor="#22D3EE" stopOpacity={0.45} />
              <stop offset="100%" stopColor="#22D3EE" stopOpacity={0} />
            </linearGradient>
          </defs>
          <CartesianGrid
            stroke="rgba(255,255,255,0.08)"
            strokeDasharray="3 3"
            vertical={false}
          />
          <XAxis
            dataKey="month"
            stroke="rgba(255,255,255,0.45)"
            tickLine={false}
            axisLine={false}
            tick={{ fontSize: 12 }}
          />
          <YAxis
            stroke="rgba(255,255,255,0.45)"
            tickLine={false}
            axisLine={false}
            tick={{ fontSize: 12 }}
            allowDecimals={false}
          />
          <Tooltip
            contentStyle={{
              borderRadius: 12,
              border: "1px solid rgba(255,255,255,0.12)",
              backgroundColor: "rgba(10,14,26,0.95)",
              boxShadow: "0 8px 24px -8px rgba(0,0,0,0.6)",
              fontSize: 12,
              color: "#F1F5F9",
            }}
            labelStyle={{ color: "#F1F5F9", fontWeight: 600 }}
            itemStyle={{ color: "#F1F5F9" }}
            cursor={{ stroke: "rgba(255,255,255,0.15)" }}
            formatter={(value: number) => [value, "Contributions"]}
          />
          <Area
            type="monotone"
            dataKey="contributions"
            stroke="#22D3EE"
            strokeWidth={2}
            fill="url(#g-cyan)"
          />
        </AreaChart>
      </ResponsiveContainer>
    </div>
  );
}
