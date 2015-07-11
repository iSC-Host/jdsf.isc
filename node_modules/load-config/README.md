# Load Config
Load an nconf configuration by using a file path passed as a command line option

# Installation
```bash
npm install load-config
```

# Usage

In this example **test/config.json** is a path to the configuration file you wish to load
```bash
cd <project root>
node load.js --config test/config.json
```

**load.js**
```javascript
var loadConfig = require('load-config')
loadConfig(function(err, config) {
  should.not.exist(err, 'error loading config: ' + JSON.stringify(err))
  inspect('config loaded')
  var role = config.get('role')
  inspect(role, 'config role')
})
```
