import dayjs, { extend } from "dayjs";

import relativeTime from "dayjs/plugin/relativeTime";
extend(relativeTime);

// 日本語化
import "dayjs/locale/ja";
dayjs.locale("ja");

// しきい値の設定
const thresholds = [
    { l: "s", r: 1 },
    { l: "m", r: 1 },
    { l: "mm", r: 59, d: "minute" },
    { l: "h", r: 1 },
    { l: "hh", r: 23, d: "hour" },
    { l: "d", r: 1 },
    { l: "dd", r: 29, d: "day" },
    { l: "M", r: 1 },
    { l: "MM", r: 11, d: "month" },
    { l: "y" },
    { l: "yy", d: "year" },
];
dayjs.extend(relativeTime, {
    thresholds,
});

/**
 * @param date - 日付
 * @returns string - 相対時間
 * @description 日付を相対時間に変換する
 * @example convertRelativeTime("2021-01-01 00:00:00") => "1年前"
 */
const convertRelativeTime = (date: string) => {
    return dayjs(date).fromNow();
};

export default convertRelativeTime;
