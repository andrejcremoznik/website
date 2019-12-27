import $ from 'jquery'

let styles = false

const Gist = (node) => {
  const { user, id, file } = node.dataset

  if (!user || !id || !file) {
    return
  }

  $.ajax({
    url: `https://gist.github.com/${user}/${id}.json?file=${file}&callback=?`,
    dataType: 'json',
    method: 'GET'
  })
    .done((rsp) => {
      $(node).replaceWith(rsp.div)
      if (!styles && rsp.stylesheet) {
        $(document.head).append($('<link/>', { rel: 'stylesheet', href: rsp.stylesheet }))
        styles = true
      }
    })
    .fail(() => { console.log('Error loading Gist.') })
}

export default Gist
