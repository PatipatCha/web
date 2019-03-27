/**
 * Created by kinkop on 8/7/2017 AD.
 */

import moment from 'moment'

class DateTime
{
    isValidDate(date)
    {
        var validDate = false
        var dateObj = new Date(date);
        if ( Object.prototype.toString.call(dateObj) === "[object Date]" ) {
            // it is a date
            if ( isNaN( dateObj.getTime() ) ) {
                // date is not valid

            } else {
                // date is valid
                validDate = true
            }
        } else {
            // not a date
        }

        return validDate
    }

    displayDate(date, locale = 'th')
    {
        moment.locale(locale)
        date = date.replace(/ /, 'T')
        if (this.isValidDate(date)) {
            var addYear = 0
            if (locale == 'th') {
                addYear = 543
            }
            return moment(date).format('D MMM ')
                    + moment(date).add(addYear, 'year').format('YYYY');
        }

        return false
    }

}

export default DateTime