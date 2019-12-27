const sh = require('shelljs')
const path = require('path')

sh.find(path.resolve('./web/app'))
  .filter(file => file.match(/\.po$/) && (file.match('plugins/crean') || file.match('themes/crean')))
  .forEach(lang => {
    const name = lang.substring(0, lang.length - 3)
    sh.exec(`msgfmt -o ${name}.mo ${name}.po`)
  })
