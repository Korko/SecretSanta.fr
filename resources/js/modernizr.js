// Use module.exports to be handled by Modernizr itself but import with ES6 syntax
module.exports = {
  // Full list of supported options can be found in [config-all.json](https://github.com/Modernizr/Modernizr/blob/master/lib/config-all.json).
  options: [
    "setClasses"
  ],
  "feature-detects": [
    "test/file/filesystem",
    "test/inputtypes"
  ]
};
