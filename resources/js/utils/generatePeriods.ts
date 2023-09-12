type Period = {
    startDate: string;
    endDate: string;
};

/**
 * @param string startDate - 開始日
 * @param string endDate - 終了日
 * @param number days - 1期間の日数
 * @returns Period[] - 期間の配列
 * @description 開始日から終了日までの期間を日数で分割する
 * @example generatePeriods('2020/01/01', '2020/01/31', 7) => [
 * {startDate: '2020/01/01', endDate: '2020/01/07'},
 * {startDate: '2020/01/08', endDate: '2020/01/14'},
 * {startDate: '2020/01/15', endDate: '2020/01/21'},
 * {startDate: '2020/01/22', endDate: '2020/01/28'},
 * {startDate: '2020/01/29', endDate: '2020/01/31'}
 * ]
 */
const generatePeriods = (
    start: string,
    end: string,
    days: number,
): Period[] => {
    const periods: Period[] = [];
    const currentDate = new Date(start);
    const endDate = new Date(end);
    while (currentDate.getTime() <= endDate.getTime()) {
        const endDate = new Date(currentDate);
        endDate.setDate(endDate.getDate() + days - 1);
        periods.push({
            startDate: currentDate.toLocaleDateString("ja-JP", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
            }),
            endDate:
                new Date(end) <= endDate
                    ? end
                    : endDate.toLocaleDateString("ja-JP", {
                        year: "numeric",
                        month: "2-digit",
                        day: "2-digit",
                    }),
        });
        currentDate.setDate(currentDate.getDate() + days);
    }
    return periods;
};

export default generatePeriods;
