var should = require('should');
var loadConfig = require('./index')
loadConfig(function(err, config) {
  should.not.exist(err, 'error loading config: ' + JSON.stringify(err))
  var role = config.get('role')
  should.exist(role)
})
