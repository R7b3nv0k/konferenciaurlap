<!DOCTYPE html>
<html lang="hu">
<head>
    <link rel="stylesheet" href="stlye.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konferencia Űrlap</title>
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["kep"]) && $_FILES["kep"]["error"] == 0) {
        $cel_mappa = "adattar/";
        $cel_fajl = $cel_mappa . basename($_FILES["kep"]["name"]);

        // MIME típus ellenőrzése
        $tipus = mime_content_type($_FILES["kep"]["tmp_name"]);
        $meret = getimagesize($_FILES["kep"]["tmp_name"]);

        if ($tipus !== "image/png") {
            echo "<p style='color:red;'>❌ Csak PNG formátum engedélyezett!</p>";
            exit;
        }

        if ($meret[0] != 480 || $meret[1] != 480) {
            echo "<p style='color:red;'>❌ A kép méretének pontosan 480x480 pixelnek kell lennie!</p>";
            exit;
        }

        if (move_uploaded_file($_FILES["kep"]["tmp_name"], $cel_fajl)) {
            echo "<p style='color:green;'>✅ A kép sikeresen feltöltve!</p>";
            echo "<img src='$cel_fajl' width='200'>";
        } else {
            echo "<p style='color:red;'>❌ Hiba történt a feltöltés során.</p>";
        }
        if (isset($_FILES["kep"]) && $_FILES["kep"]["error"] === UPLOAD_ERR_OK) {
            // fájl elérhető és hibátlan
        }
    }
}
$nev = $_POST['nev'] ?? '';
    $evszam = $_POST['evszam'] ?? '';
    $email = $_POST['email'] ?? '';
    $szam = $_POST['szam'] ?? '';
    $munknev = $_POST['munknev'] ?? '';
    $munkcim = $_POST['munkcim'] ?? '';
    $munkor = $_POST['munkor'] ?? '';
    $beoszt = $_POST['beoszt'] ?? '';

    // Egyedi fájlnév generálása (időbélyeg és random szám)
    $fajlnev = "adattar/felh_" . time() . "_" . rand(1000, 9999) . ".txt";

    // Szöveg összeállítása
    $tartalom = "Név: $nev\n";
    $tartalom .= "Születési évszám: $evszam\n";
    $tartalom .= "Email: $email\n";
    $tartalom .= "Telefonszám: $szam\n";
    $tartalom .= "Munkahely neve: $munknev\n";
    $tartalom .= "Munkahely címe: $munkcim\n";
    $tartalom .= "Munkakör: $munkor\n";
    $tartalom .= "Beosztás: $beoszt\n";
    $tartalom .= "Feltöltött kép neve: " . $_FILES["kep"]["name"] . "\n";

    // Fájl mentése
    file_put_contents($fajlnev, $tartalom);

    echo "<p style='color:green;'>✅ Az adatok elmentve az adattar mappába: <code>$fajlnev</code></p>";
?>

<form method="POST" action="" enctype="multipart/form-data">
    <label for="nev">Név:</label>
    <input type="text" id="nev" name="nev"><br>

    <label for="evszam">Születési évszám:</label>
    <input type="number" id="evszam" name="evszam"><br>

    <label for="email">Email cím:</label>
    <input type="email" id="email" name="email"><br>

    <label for="szam">Telefonszám:</label>
    <input type="tel" id="szam" name="szam"><br>

    <label for="munknev">Munkahely neve:</label>
    <input type="text" id="munknev" name="munknev"><br>

    <label for="munkcim">Munkahely címe:</label>
    <input type="text" id="munkcim" name="munkcim"><br>

    <label for="munkor">Munkaköre:</label>
    <input type="text" id="munkor" name="munkor"><br>

    <label for="beoszt">Beosztása:</label>
    <input type="text" id="beoszt" name="beoszt"><br>

    <p>
        Válassz egy arcképet, amely a rendezvényre szóló belépőkártyán fog szerepelni.
        <strong>Csak PNG formátum és pontosan 480x480 pixel méret engedélyezett.</strong><br>
        Ha nincs PNG képed, itt átalakíthatod:
        <a href="https://cloudconvert.com/jpg-to-png" target="_blank">Katt ide!</a>
    </p>

    <label for="kep">Kép feltöltése:</label>
    <input type="file" id="kep" name="kep" accept="image/png"><br><br>

    <input type="submit" value="Küldés">
</form>

</body>
</html>
