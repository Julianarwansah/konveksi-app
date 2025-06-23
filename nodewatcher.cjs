// Import modul yang diperlukan
const { exec } = require("child_process");
const chokidar = require("chokidar");

// Fungsi untuk menjalankan perintah Git
function runGitCommands() {
  console.log("Perubahan terdeteksi, menjalankan Git commands...");

  // Jalankan perintah Gitini
  exec("git add .", (error, stdout, stderr) => {
    if (error) {
      console.error(`Error saat git add: ${error.message}`);
      return;
    }
    if (stderr) {
      console.error(`Stderr saat git add: ${stderr}`);
      return;
    }
    console.log("git add berhasil.");

    // Lanjutka ke git commit
    exec('git commit -m "Auto commit: Perubahan otomatis"', (error, stdout, stderr) => {
      if (error) {
        console.error(`Error saat git commit: ${error.message}`);
        return;
      }
      if (stderr) {
        console.error(`Stderr saat git commit: ${stderr}`);
        return;
      }
      console.log("git commit berhasil.");

      // Lanjutkan ke git push
      exec("git push origin main", (error, stdout, stderr) => {
        if (error) {
          console.error(`Error saat git push: ${error.message}`);
          return;
        }
        if (stderr) {
          console.error(`Stderr saat git push: ${stderr}`);
          return;
        }
        console.log("git push berhasil.");
      });
    });
  });
}

// Pantau perubahan file di direktori proyek
const watcher = chokidar.watch(".", {
  ignored: /(^|[\/\\])\../, // Abaikan file hidden (seperti .env, .git, dll)
  persistent: true, // Tetap aktif memantau
  ignoreInitial: true, // Abaikan perubahan saat pertama kali dijalankan
});

// Jalankan Git commands saat ada perubahan
watcher
  .on("add", (path) => {
    console.log(`File ditambahkan: ${path}`);
    runGitCommands();
  })
  .on("change", (path) => {
    console.log(`File diubah: ${path}`);
    runGitCommands();
  })
  .on("unlink", (path) => {
    console.log(`File dihapus: ${path}`);
    runGitCommands();
  });

console.log("Memantau perubahan file...");