var input  = require('./names.json');
var output = [];

for (var i = 0, l = input.length; i < l; i++) {
  var element = input[i];

  output.push({
    country  : element[0].country,
    male     : element[1],
    female   : element[2],
    surnames : element[3]
  });
}

var file = JSON.stringify(output, null, 4);

// remove newlines from arrays
file = file
  .replace(/\n[ ]{12}/gm, '')
  .replace(/"\n[ ]{8}/gm, '"')
  .replace(/","/gm, '", "');

// write to console
console.log(file);