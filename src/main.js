import Vue from 'vue'
import DirectoryMain from './js/components/Directory/DirectoryMain'
import init from './js/lib/init-modules'
import { addClass, hasClass } from './js/lib/dom'

document.addEventListener('DOMContentLoaded', () => {
  // Init modules
  init({
    'plugin-module': 'plugin-modules'
  }).mount()

  // Add touch class if touch device and re-init modules
  window.addEventListener('touchstart', () => {
    if (!hasClass('is-touch', document.body)) {
      addClass('is-touch', document.body)
      init({
        'plugin-module': 'plugin-modules'
      }).mount()
    }
  })
})

// LoDash cuz its awesome
window._ = require('lodash')

// import vue
window.Vue = require('vue')

Vue.prototype.$_window = window

/**
 * Startup Vue
 */

const directory = document.querySelector('#profile-directory')

if (directory) {
  // set up vue
  new Vue({
    components: {
      DirectoryMain
    }
  }).$mount('#profile-directory')
}
