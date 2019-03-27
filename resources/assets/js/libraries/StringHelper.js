/**
 * Created by kinkop on 8/7/2017 AD.
 */
export default {
    htmlspecialcharDecode(text)
    {
        let str = $("<div/>").html(text).text();
        if (typeof str == 'string') {
            str = str.replace(/%27/gi, "'")
            str = str.replace(/%22/gi, '"')
        }

        return str
    }
}