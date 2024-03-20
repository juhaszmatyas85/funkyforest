const fs = require('fs');
const path = require('path');
const request = require('request');
const unzipper = require('unzipper');

const wordpressURL = 'https://hu.wordpress.org/latest-hu_HU.zip';
const extractPath = path.join(__dirname, '../web');

if (!fs.existsSync(extractPath)) {
    fs.mkdirSync(extractPath, { recursive: true });
}

console.log('Megkezdem a WordPress letöltését és kicsomagolását a célhelyre...');

request(wordpressURL)
    .pipe(unzipper.Parse())
    .on('entry', function (entry) {
        const fileName = entry.path;
        const type = entry.type;
        const fullPath = path.join(extractPath, fileName);

        if (fileName.startsWith('wordpress/wp-content/') || fileName === 'wordpress/wp-config.php') {
            // Ha a fájl a wp-content mappában van, vagy a wp-config.php, akkor kihagyjuk
            entry.autodrain();
        } else if (type === 'File') {
            // Fájlokat kicsomagoljuk a megfelelő helyre
            entry.pipe(fs.createWriteStream(fullPath));
        } else {
            // Könyvtárak esetén ellenőrizzük, hogy létezik-e, ha nem, létrehozzuk
            if (!fs.existsSync(fullPath)) {
                fs.mkdirSync(fullPath, { recursive: true });
            }
            entry.autodrain();
        }
    })
    .on('error', function (err) {
        console.error('Hiba történt a letöltés vagy kicsomagolás során:', err);
    })
    .on('close', function () {
        console.log('A WordPress sikeresen letöltve és kicsomagolva, kivéve a verziókövetett wp-content mappát és a wp-config.php fájlt.');
    });
