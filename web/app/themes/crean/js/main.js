/*! crean | MIT License | https://keybase.io/andrejcremoznik */

import Lightbox from './modules/lightbox'

const runners = {
  // default: function () {
  //   console.log('Run on every page')
  // },
  'single-post': function () {
    Lightbox()
  }
}

document.addEventListener('DOMContentLoaded', () => {
  Object
    .keys(runners)
    .filter(key => key !== 'default')
    .forEach(key => {
      if (document.body.classList.contains(key)) {
        runners[key]()
      }
    })
  // runners.default()
})
