var optimist = require('optimist');
var nconf = require('nconf')
var fs = require('fs')
module.exports = function getConfig(data, cb) {
  if (!cb) {
    cb = data
    data = {}
  }
  if (data.config) {
    return cb(null, data.config)
  }
  // var argv = optimist.demand(['config']).argv;
  var argv = optimist.argv;
  var configFilePath = argv.config

  fs.exists(configFilePath, function (exists) {
    if (!exists) {
      return cb({
        message: 'failed to spinup server',
        error: 'config file not found at path: ' + configFilePath,
        stack: new Error().stack
      })
    }
    var config = nconf.argv().env().file({file: argv.config});
    cb(null, config)
  })
}
