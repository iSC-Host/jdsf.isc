var path = require('path')
var fs = require('fs')
var assert = require('assert')
var should = require('should');
var fork = require('child_process').fork;
describe('Load Config test', function () {
  this.slow('1s')
  it('should load valid config.json files that be found', function (done) {
    var cmd = path.join(__dirname, '..', 'load-cli.js')
    var configFilePath = path.join(__dirname, 'config.json')
    assert.ok(fs.existsSync(configFilePath), 'config file not found at path: ' + configFilePath)
    var opts = ['--config', configFilePath]
    var child = fork(cmd, opts)
    child.on('exit', function (code) {
      code.should.eql(0)
      done()
    });
  })
})
