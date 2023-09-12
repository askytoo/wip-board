/**
 * @param string startedAt - 開始時刻
 * @param string endedAt - 終了時刻
 * @returns number - 時間差（分）
 * @description 開始時刻と終了時刻の時間差を分で返す
 * @example calculateTimeDifferenceInMinutes('2020/01/01 00:00', '2020/01/01 00:01') => 1
 */
const calculateTimeDifferenceInMinutes = (startedAt: string, endedAt: string): number => {
    const startedAtDate = new Date(startedAt);
    const endedAtDate = new Date(endedAt);
    const timeDifferenceInMilliseconds = endedAtDate.getTime() - startedAtDate.getTime();
    const timeDifferenceInMinutes = timeDifferenceInMilliseconds / 1000 / 60;
    return timeDifferenceInMinutes;
}

export default calculateTimeDifferenceInMinutes;
