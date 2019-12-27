const Lightbox = () => {
  const head = document.querySelector('head')

  const js = document.createElement('script')
  js.src = 'https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/1.0.1/Luminous.min.js'
  js.async = true
  js.onload = execute
  head.appendChild(js)

  const css = document.createElement('link')
  css.href = 'https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/1.0.1/luminous-basic.min.css'
  css.rel = 'stylesheet'
  css.onload = execute
  head.appendChild(css)

  let loadedDeps = 0
  function execute () {
    loadedDeps++
    if (loadedDeps === 2) {
      let allImageLinks = Array.from(document.querySelectorAll('a[href$=".jpg"], a[href$=".png"]'))
      if (allImageLinks.length) {
        allImageLinks.forEach(imgLink => new window.Luminous(imgLink))
      }
    }
  }
}

export default Lightbox
