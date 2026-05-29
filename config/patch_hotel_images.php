<?php
/**
 * patch_hotel_images.php
 *
 * Patches every hotel in Firestore whose 'image' field is blank or a local path
 * (e.g. "/assets/images/hotel1.jpg" from the MySQL migration) with a stable
 * picsum.photos URL matching the PHP web app's formula: seed = "reddoorz" + id.
 *
 * Run:  php patch_hotel_images.php
 * Safe to run multiple times — skips hotels that already have a valid HTTP image.
 */

require_once __DIR__ . '/firestore.php';

function is_valid_http_image(string $url): bool {
    return str_starts_with($url, 'http://') || str_starts_with($url, 'https://');
}

function picsum_url(int $hotelId): string {
    return "https://picsum.photos/seed/reddoorz{$hotelId}/800/500";
}

echo "<pre>\n";
echo "Fetching all hotels...\n";

// Use fs_all which goes through the proper _fs_run_query path with correct URL construction
$hotels = fs_all('hotels');

if (empty($hotels)) {
    echo "No hotels found. Check your service account key.\n";
    echo "</pre>";
    exit;
}

echo "Found " . count($hotels) . " hotels.\n\n";

$updated = 0; $skipped = 0; $errors = 0;

foreach ($hotels as $hotel) {
    $hotelId = (int)($hotel['id'] ?? 0);
    $name    = $hotel['name'] ?? "Hotel #{$hotelId}";
    $image   = trim($hotel['image'] ?? '');

    if (is_valid_http_image($image)) {
        echo "SKIP  [{$hotelId}] {$name}\n";
        $skipped++;
        continue;
    }

    $newImage = picsum_url($hotelId);

    try {
        fs_update('hotels', $hotelId, ['image' => $newImage]);
        echo "PATCH [{$hotelId}] {$name} → {$newImage}\n";
        $updated++;
    } catch (Exception $e) {
        echo "ERROR [{$hotelId}] {$name} — " . $e->getMessage() . "\n";
        $errors++;
    }
}

_fs_cache_bust('hotels');

echo "\n--- Done ---\n";
echo "Updated : {$updated}\n";
echo "Skipped : {$skipped} (already had valid HTTP image)\n";
echo "Errors  : {$errors}\n";
echo "</pre>";
