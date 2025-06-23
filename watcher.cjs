const { exec } = require('child_process');
const watch = require('node-watch');

watch('./', { recursive: true }, (event, file) => {
  console.log(`File ${file} berubah. Melakukan commit otomatis...`);
  exec('auto-commit.bat', (err, stdout, stderr) => {
    if (err) {
      console.error(`Error: ${stderr}`);
      return;
    }
    console.log(`Output: ${stdout}`);
  });
});