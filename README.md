# Telepítés

Inicializáljuk a Node-os script-eket:
```bash
yarn install
```
A WordPress letöltése és kicsomagolása a `/web` mappába úgy, hogy a `/web/wp-content` mappa és a `/web/wp-config.php` fájl érintetlenül marad, tehát nem lesz felülírva a verziókövetett tartalom:
```bash
yarn downloadWP
```

A `dev`es környezet indítása ([ehhez szükséges telepíteni a Lando-t](https://lando.dev/download/)):
```bash
lando start
```
Az oldalt a [http://funkyforest.lndo.site](http://funkyforest.lndo.site) linken éred el (ha minden rendben lefutott).

# Alap adatbázis hozzáférések

```
database name: wordpress
database username: wordpress
database password: wordpress
database host: database
```

# Éles oldali tartalom áthozása
Az `All-in-One WP Migration` pluginnel hozzuk át az éles tartalmat, ha nincs szükség az éles sablonokra, pluginekre, akkor azok mellől exportáláskor szedjük ki a pipát.

# Verziókövetési terv
Egyelőre verziókövetve van a WordPress tekintetében a teljes `wp-content` mappa és a `wp-config.php` fájl. Ha több 3rd party pluginra lesz/van szükség, illetve nagy mennyiségű média tartalom szerepel az oldalon, akkor azokat szükséges leválasztani, illetve végeredményben csak azt érdemes követni, amiben változtatásokat eszközölnénk...

# WordPress Coding Standards
A PHP kódot tudjuk automatikusan formázni a [WordPress kódolási sztenderd](https://github.com/WordPress/WordPress-Coding-Standards)jeinek megfelelően.

Globálisan telepítsük composer-rel (ha WSL2-t használunk, akkor ott, ne Windows alatt):
```bash
composer global config allow-plugins.dealerdirect/phpcodesniffer-composer-installer true
composer global require --dev wp-coding-standards/wpcs:"^3.0"
```
Telepítsük VSCode-hoz az alábbi kiegészítőt:
- [PHP Sniffer & Beautifier](https://marketplace.visualstudio.com/items?itemName=ValeryanM.vscode-phpsab)

A projektet a **Remote Explorer**-el **WSL**-en keresztül a `.vscode/funkyforest.code-workspace` projektfájl segítségével indítsuk, ekkor a megfelelő beállításokat használhatjuk. Itt írjuk át a globális elérési utat, pl:
```json
{
    "settings": {
        // PHP Sniffer & Beautifier
        "phpsab.standard": "WordPress",
        "phpsab.fixerEnable": true,
        "phpsab.snifferEnable": true,
        "phpsab.snifferShowSources": true,
        "phpsab.executablePathCBF": "/home/USERNAME/.config/composer/vendor/bin/phpcbf",
        "phpsab.executablePathCS": "/home/USERNAME/.config/composer/vendor/bin/phpcs"
    }
}
```
ahol a `USERNAME` a felhasználóneved az adott Linux rendszeren (WSL2), de működik Windows alatt is.

Mentéskor vagy `Ctrl+Alt+F`-el formázzuk a kódot.
