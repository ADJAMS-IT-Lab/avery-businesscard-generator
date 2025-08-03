<?php

//********************************************************************
// Customize HERE

$firstNames = ['John', 'Alice', 'Emma', 'Ali', 'Fatima', 'Liam', 'Sophia'];
$lastNames = ['Smith', 'Doe', 'Khan', 'Nguyen', 'Dupont'];
$streets = ['Peace Street', 'Champs-Élysées Ave', 'Roma Street'];
$countries = ['France', 'Switzerland', 'Italy'];
$cities = ['Paris', 'Zurich', 'Rome'];
//********************************************************************


// Constants for Avery 10-card layout on A4 at 300 DPI
$dpi = 300;
$pageWidthMM = 210;
$pageHeightMM = 297;

$pageWidthPx = (int)round($pageWidthMM / 25.4 * $dpi);  // ˜ 2480 px
$pageHeightPx = (int)round($pageHeightMM / 25.4 * $dpi); // ˜ 3508 px

$cardWidthMM = 89;
$cardHeightMM = 51;
$cardWidthPx = (int)round($cardWidthMM / 25.4 * $dpi);  // ˜ 1051 px
$cardHeightPx = (int)round($cardHeightMM / 25.4 * $dpi); // ˜ 602 px

// Margins and spacing (Avery spec approx)
// Top margin ~12mm, left margin ~13mm, vertical gap ~5mm, horizontal gap ~7mm
$marginLeftPx = (int)round(13 / 25.4 * $dpi);
$marginTopPx = (int)round(12 / 25.4 * $dpi);
$verticalGapPx = (int)round(5 / 25.4 * $dpi);
$horizontalGapPx = (int)round(7 / 25.4 * $dpi);

$outputDir = "businesscards_images/";
if (!is_dir($outputDir)) mkdir($outputDir, 0777, true);

$fontPath = __DIR__ . '/fonts/arial.ttf';
if (!file_exists($fontPath)) die("Font file not found: $fontPath");

// Create A4 blank page white background
$image = imagecreatetruecolor($pageWidthPx, $pageHeightPx);
$white = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $white);

$black = imagecolorallocate($image, 0, 0, 0);
$gray = imagecolorallocate($image, 150, 150, 150);
$blue = imagecolorallocate($image, 0, 102, 204);


function generateRandomCardData() {
    global $firstNames, $lastNames, $streets, $countries, $cities;
    $firstName = $firstNames[array_rand($firstNames)];
    $lastName = $lastNames[array_rand($lastNames)];
    $fullName = "$firstName $lastName";
    $email = strtolower("$firstName.$lastName") . '@example.com';
    $street = $streets[array_rand($streets)];
    $streetNumber = rand(1, 99);
    $country = $countries[array_rand($countries)];
    $city = $cities[array_rand($cities)];
    $postalCode = rand(1000, 9999);
    $phone = '0' . rand(70, 79) . ' / ' . rand(100, 999) . '.' . rand(10, 99) . '.' . rand(10, 99);
    return compact('fullName', 'email', 'street', 'streetNumber', 'country', 'city', 'postalCode', 'phone');
}

function drawCard($img, $x, $y, $w, $h, $data, $font) {
    $black = imagecolorallocate($img, 0, 0, 0);
    $gray = imagecolorallocate($img, 100, 100, 100);
    $blue = imagecolorallocate($img, 0, 102, 204);
    $white = imagecolorallocate($img, 255, 255, 255);

    // Draw background and border for the card
    imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $white);
    imagerectangle($img, $x, $y, $x + $w - 1, $y + $h - 1, $black);

    $paddingLeft = 120; // Left padding for more right margin
    $posX = $x + $paddingLeft;

    // Starting Y position for text inside the card
    $posY = $y + 100;

    // Vertical space between lines
    $lineSpacing = 50;

    // Text lines with increased spacing
    imagettftext($img, 40, 0, $posX, $posY, $black, $font, "Mr. " . $data['fullName']);
    $posY += $lineSpacing;

    imagettftext($img, 24, 0, $posX+40, $posY, $gray, $font, "Programmer Analyst");
    $posY += $lineSpacing;

    imagettftext($img, 20, 0, $posX+40, $posY, $gray, $font, "Network, Internet & Mobile Dev.");
    $posY += $lineSpacing;

    imagettftext($img, 20, 0, $posX+40, $posY, $gray, $font, "Microsoft & Sun System Pro.");
    $posY += $lineSpacing;

    imagettftext($img, 26, 0, $posX, $posY, $black, $font, "Address:");
    $posY += $lineSpacing;

    imagettftext($img, 22, 0, $posX+40, $posY, $black, $font, "{$data['streetNumber']} {$data['street']}");
    $posY += $lineSpacing;

    imagettftext($img, 22, 0, $posX+40, $posY, $black, $font, "{$data['postalCode']} {$data['city']} - {$data['country']}");
    $posY += $lineSpacing;


$footerFontSize = 24;
    imagettftext($img, 26, 0, $posX, $posY, $blue, $font, "Phone: " . $data['phone']);
    $posY += $lineSpacing;

    imagettftext($img, 26, 0, $posX, $posY, $blue, $font, "Email: " . $data['email']);

    // Footer label - place near the bottom of the card, centered horizontally
//    $footerFontSize = 20;
    $footerFontSize = 14;
    $footerText = "Card size: 89mm × 51mm (Avery 5371)";
    // Calculate approximate text width (rough estimate)
    $textBox = imagettfbbox($footerFontSize, 0, $font, $footerText);
    $textWidth = abs($textBox[2] - $textBox[0]);
    $footerX = $x + ($w - $textWidth) / 2;  // center horizontally
    $footerY = $y + $h - 40;  // 40 px above bottom edge

    imagettftext($img, $footerFontSize, 0, $footerX, $footerY, $gray, $font, $footerText);
}


// Draw 2 columns × 5 rows of cards
for ($row = 0; $row < 5; $row++) {
    for ($col = 0; $col < 2; $col++) {
        $x = $marginLeftPx + $col * ($cardWidthPx + $horizontalGapPx);
        $y = $marginTopPx + $row * ($cardHeightPx + $verticalGapPx);
        $cardData = generateRandomCardData();
        drawCard($image, $x, $y, $cardWidthPx, $cardHeightPx, $cardData, $fontPath);
    }
}

// Save and output
$file = $outputDir . "avery_businesscards_" . date('Ymd_His') . ".png";
imagepng($image, $file);
imagedestroy($image);

echo "<h2>Avery Business Card Sheet (10 cards per A4 @ 300 DPI)</h2>";
echo "<img src='$file' style='width:100%; border:1px solid #ccc;'>";
