var input  = require('./names.json');
var output = [];

for (var i = 0, l = input.length; i < l; i++) {
  output.push(input[i]);
}

var file = JSON.stringify(output, null, 4);

// remove newlines from arrays
file = file
  .replace(/\n[ ]{12}/gm, '')
  .replace(/"\n[ ]{8}/gm, '"')
  .replace(/","/gm, '", "');

// write to console
console.log(file);