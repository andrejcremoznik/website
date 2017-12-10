module.exports = {
  defaultDeployEnv: 'production',
  deployEnvSSH: {
    production: {
      host: 'cremoznik.si',
      port: 52022,
      username: 'skye',
      agent: process.env.SSH_AUTH_SOCK
    }
  },
  deployEnvPaths: {
    production: '/srv/http/cremoznik.si'
  }
}
