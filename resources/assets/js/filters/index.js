import Vue from 'vue'
import { number_comma, money } from './accounting'

Vue.filter('number_comma', number_comma)
Vue.filter('money', money)
