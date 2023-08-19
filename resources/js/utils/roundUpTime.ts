/**
 * @description 指定した単位まで時刻を切り上げる
 * @param step - 切り上げる単位 (秒)
 * @param date - 切り上げる時刻
 * @returns 切り上げた時刻 (HH:MM)
 * @example roundUpTime(1800, new Date("2021-01-01 12:00:10")) // => "12:30"
 */
const roundUpTime = (step: number, date: Date) => {
    const stepMinutes = step / 60;
    const minutes = date.getMinutes();
    const minutesCeil = Math.ceil(minutes / stepMinutes) * stepMinutes;
    const ceil = date.setMinutes(minutesCeil);
    const roundUpTIme = new Date(ceil).toLocaleTimeString("ja-JP", {
        hour12: false,
        hour: "2-digit",
        minute: "2-digit",
    });
    return roundUpTIme;
};

export default roundUpTime;
