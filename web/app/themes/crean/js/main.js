/*! crean | MIT License | https://keybase.io/andrejcremoznik */

import Lightbox from './modules/lightbox'
import Gist from './modules/gist'

const runners = {
  // default: function () {
  //   console.log('Run on every page')
  // },
  'single-post': function () {
    Lightbox()
    document.querySelectorAll('.gist').forEach(Gist)
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
