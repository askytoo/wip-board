import generatePeriods from "./generatePeriods";
import { describe, expect, test } from "vitest";

describe("generatePeriods", () => {
    test("開始日から終了日までの期間を日数で分割する", () => {
        const result = generatePeriods("2020/01/01", "2020/01/31", 7);
        expect(result).toEqual([
            { startDate: "2020/01/01", endDate: "2020/01/07" },
            { startDate: "2020/01/08", endDate: "2020/01/14" },
            { startDate: "2020/01/15", endDate: "2020/01/21" },
            { startDate: "2020/01/22", endDate: "2020/01/28" },
            { startDate: "2020/01/29", endDate: "2020/01/31" },
        ]);
    });
});
