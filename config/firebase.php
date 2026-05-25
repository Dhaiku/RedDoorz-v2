<?php
/**
 * Firebase Admin SDK initializer.
 * Used for: FCM push notifications, Firestore real-time updates, ID token verification.
 *
 * Service account key must stay OUTSIDE the web root and git repo.
 * Default path: C:\Users\bever\Downloads\  (set FIREBASE_KEY_PATH env to override)
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Kreait\Firebase\Factory;

$keyPath = getenv('FIREBASE_KEY_PATH') ?: 'C:/Users/bever/Downloads/reddoorz-8f605-firebase-adminsdk-fbsvc-bb9a5b8ce6.json';

$factory = (new Factory)->withServiceAccount($keyPath);

// Expose individual services as needed
function getFirebaseMessaging() {
    global $factory;
    return $factory->createMessaging();
}

function getFirebaseAuth() {
    global $factory;
    return $factory->createAuth();
}

function getFirestore() {
    global $factory;
    return $factory->createFirestore();
}
