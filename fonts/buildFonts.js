const fs = require('fs');
const path = require('path');

function ttf2base64(filename) {
  const fontData = fs.readFileSync(path.resolve(__dirname, filename));
  return fontData.toString('base64');
}

const base64Font = ttf2base64("Roboto-Regular.ttf");

const jsContent = `
var RobotoRegular = "${base64Font}";
`;

fs.writeFileSync("Roboto-normal.js", jsContent);
console.log("✅ Font đã được convert thành Roboto-normal.js");
