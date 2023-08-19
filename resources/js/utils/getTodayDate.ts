/**
 * @description 今日の日付を取得する
 * @returns 今日の日付 (YYYY-MM-DD)
 */
const getTodayDate = () => {
    const date = new Date();
    const yyyy = date.getFullYear();
    const mm = ("0" + (date.getMonth() + 1)).slice(-2);
    const dd = ("0" + date.getDate()).slice(-2);
    const today = yyyy + "-" + mm + "-" + dd;
    return today;
};

export default getTodayDate;
