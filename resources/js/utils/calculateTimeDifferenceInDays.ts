/**
 * @param string startDate - 開始日
 * @param string endDate - 終了日
 * @returns number - 差分日数
 * @description 開始日と終了日の差分日数を計算する
 * @example calculateTimeDifferenceInDays('2021-01-01', '2021-01-31') // 30
 */
const calculateTimeDifferenceInDays = (startDate: string, endDate: string): number => {
    const start = new Date(startDate);
    const end = new Date(endDate);

    const difference = end.getTime() - start.getTime();
    const days = Math.ceil(difference / (1000 * 3600 * 24));

    return days;
};

export default calculateTimeDifferenceInDays;
