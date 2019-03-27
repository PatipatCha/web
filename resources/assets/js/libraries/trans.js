import Lang from '../../../../node_modules/lang.js/src/lang'
import messages from '../messages'

const lang = new Lang({
    messages: messages,
    locale: GLOBAL_SETTING.locale,
    fallback: 'en'
})

export const trans = function (key, replace = {}) {
    return lang.get('messages.' + key, replace)
}