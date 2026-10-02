import { NextResponse } from "next/server";
import { getGithubActivity } from "@/lib/github";

export const runtime = "nodejs";
export const revalidate = 21600; // 6 hours

export async function GET() {
  const data = await getGithubActivity();
  return NextResponse.json(data, {
    headers: {
      "Cache-Control": "public, s-maxage=21600, stale-while-revalidate=86400",
    },
  });
}
