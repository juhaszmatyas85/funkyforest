

# Telepítés

Inicializáljuk a Node-os script-eket:
```bash
yarn install
```
A WordPress letöltése és kicsomagolása a `/web` mappába úgy, hogy a `/web/wp-content` mappa és a `/web/wp-config.php` fájl érintetlenül marad, tehát nem lesz felülírva a verziókövetett tartalom:
```bash
yarn downloadWP
```

A `dev`es környezet indítása:
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
