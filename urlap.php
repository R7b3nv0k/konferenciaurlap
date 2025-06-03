<!DOCTYPE html>
<html lang="hu">
<head>
    <link rel="stylesheet" href="stlye.css">
    <script src="script.js"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konferencia Űrlap</title>
</head>
<body>
<center>
<h1 style="color: white;">SEO Konferencia</h1>
</center>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $recaptcha_secret = '6LcJxFQrAAAAAIosKPUIif6FziKsX5lDqFInfm8T';
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';
    
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    ];
    
    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($recaptcha_url, false, $context);
    $responseData = json_decode($result);
    
    if (!$responseData->success) {
        echo "<p style='color:red;'>❌ CAPTCHA ellenőrzés sikertelen. Kérlek próbáld újra!</p>";
        exit;
    }
//fentiekrol fogalmam sincs, de van "nem vagyok robot" ellenorzo
    
    if (isset($_FILES["kep"]) && $_FILES["kep"]["error"] == 0) {
        $cel_mappa = "adattar/";
        $cel_fajl = $cel_mappa . basename($_FILES["kep"]["name"]);

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
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nev'])) {
    $nev = $_POST['nev'] ?? '';
    $evszam = $_POST['evszam'] ?? '';
    $email = $_POST['email'] ?? '';
    $orszagkod = $_POST['orszagkod'] ?? '';
    $telefon = $_POST['telefon'] ?? '';
    $szam = $orszagkod . ' ' . $telefon;
    $munknev = $_POST['munknev'] ?? '';
    $munkcim = $_POST['munkcim'] ?? '';
    $munkor = $_POST['munkor'] ?? '';
    $beoszt = $_POST['beoszt'] ?? '';

    // Egyedi mappa létrehozása
    $mappa_nev = "adattar/felh_" . time() . "_" . rand(1000, 9999);
    if (!is_dir($mappa_nev)) {
        mkdir($mappa_nev, 0777, true); // mappa létrehozása
    }

    // Adatok összegyűjtése
    $tartalom = "Név: $nev\n";
    $tartalom .= "Születési évszám: $evszam\n";
    $tartalom .= "Email: $email\n";
    $tartalom .= "Telefonszám: $szam\n";
    $tartalom .= "Munkahely neve: $munknev\n";
    $tartalom .= "Munkahely címe: $munkcim\n";
    $tartalom .= "Munkakör: $munkor\n";
    $tartalom .= "Beosztás: $beoszt\n";

    // Kép áthelyezése az új mappába
    if (isset($_FILES["kep"]) && $_FILES["kep"]["error"] == 0) {
        $kep_cel = $mappa_nev . "/" . basename($_FILES["kep"]["name"]);
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

        if (move_uploaded_file($_FILES["kep"]["tmp_name"], $kep_cel)) {
            $tartalom .= "Kép: " . basename($_FILES["kep"]["name"]) . "\n";
            echo "<p style='color:green;'>✅ A kép sikeresen feltöltve!</p>";
            echo "<img src='$kep_cel' width='200'>";
        } else {
            echo "<p style='color:red;'>❌ Hiba történt a kép feltöltése során.</p>";
            $tartalom .= "Kép: hiba a feltöltéskor\n";
        }
    } else {
        $tartalom .= "Kép: nincs feltöltve\n";
    }

    // TXT fájl mentése
    file_put_contents($mappa_nev . "/adatok.txt", $tartalom);

    echo "<p style='color:green;'>✅ Az adatok elmentve a(z) <code>$mappa_nev</code> mappába.</p>";
}
?>

<form method="POST" action="" enctype="multipart/form-data">
    <label for="nev">Név:</label>
    <input type="text" id="nev" name="nev"><br>

    <label for="evszam">Születési évszám:</label>
    <input type="number" id="evszam" name="evszam"><br>

    <label for="email">Email cím:</label>
    <input type="email" id="email" name="email"><br>

    <label for="szam">Telefonszám:</label>
<div class="telefon-csoport">
  <select id="orszagkod" name="orszagkod">
    <option value="+36" selected>🇭🇺 +36</option>
    <option value="+44">🇬🇧 +44</option>
    <option value="+49">🇩🇪 +49</option>
    <option value="+1">🇺🇸 +1</option>
  </select>
  <input type="tel" id="szam" name="szam" placeholder="Pl.:20 123 4567">
</div>

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

    <div class="g-recaptcha" data-sitekey="6LcJxFQrAAAAAJNHrYmxprbjmcpcirgwgWFwbTpL"></div>


    <input type="submit" value="Küldés">
</form>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</body>
</html>
