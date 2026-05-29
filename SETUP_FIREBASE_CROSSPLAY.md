# Firebase Cross-Platform Setup Guide
## RedDoorz — PHP Website ↔ Android App

This guide walks you through the one-time steps to make the website and Android app
share the same Firebase Authentication + Firestore database.

---

## Step 1 — Enable Email/Password Sign-In in Firebase Console

1. Go to → https://console.firebase.google.com/project/reddoorz-8f605/authentication/providers
2. Click **Email/Password**
3. Toggle **Enable** → Save

---

## Step 2 — Deploy Firestore Security Rules

**Option A — Firebase Console (easiest):**
1. Go to → https://console.firebase.google.com/project/reddoorz-8f605/firestore/rules
2. Copy the contents of `config/firestore.rules`
3. Paste into the editor → click **Publish**

**Option B — Firebase CLI:**
```bash
# Install CLI if not already installed
npm install -g firebase-tools

# Login
firebase login

# From the RedDoorz folder:
firebase use reddoorz-8f605
firebase deploy --only firestore:rules
```

---

## Step 3 — Run the Migration Script (ONE TIME ONLY)

This creates Firebase Auth users for all existing Firestore accounts.

Open a Command Prompt and run:
```
C:\xampp\php\php.exe C:\RedDoorz\config\migrate_auth_to_firebase.php
```

Expected output:
```
=== Firebase Auth Migration ===
Fetching accounts from Firestore...
Found 3 account(s).

  CREATE  admin@reddoorz.com  →  uid=abc123...
  CREATE  maria@hotel.com     →  uid=def456...
  CREATE  juan@gmail.com      →  uid=ghi789...

=== Done ===
  Migrated : 3
  Skipped  : 0
  Errors   : 0

All existing accounts have been given the temporary password: RedDoorz2024!
```

**Then immediately run the second script to require password resets:**
```
C:\xampp\php\php.exe C:\RedDoorz\config\set_must_change.php
```

---

## Step 4 — Verify the Migration

Go to Firebase Console:
- **Authentication** → https://console.firebase.google.com/project/reddoorz-8f605/authentication/users
  → You should see all accounts listed with their emails

- **Firestore** → https://console.firebase.google.com/project/reddoorz-8f605/firestore/data
  → Open `accounts` collection → find a document whose ID is a Firebase UID (long string like `abc123xyz...`)
  → It should have: `email`, `role`, `status`, `firebaseUid`, `mustChangePassword`

---

## Step 5 — Build & Run the Android App

1. Open Android Studio → sync Gradle
2. Run the app on an emulator or device
3. On the Login screen, use:
   - **Email:** `admin@reddoorz.com`
   - **Password:** `RedDoorz2024!`  (the migration temp password)
4. You'll be redirected to the Change Password screen (mustChangePassword=true)
5. Set a new password → you'll land on the Admin Dashboard
6. The Admin Dashboard will now show real users and bookings from Firestore ✅

---

## How it works after setup

```
Android App (Firebase SDK)           PHP Website (Admin SDK)
────────────────────────────         ──────────────────────────
signInWithEmailAndPassword()  ──→    Firebase Auth (shared pool)
                                             ↓
                              Firestore accounts/{firebaseUid}
                              Firestore customers/{firebaseUid}
                                             ↑
login.php / register.php      ──→    writes firebaseUid mirror docs
```

- **New accounts** created on either platform are immediately available on the other
- **Website login** continues using bcrypt (unchanged for web users)
- **Android login** uses Firebase Auth (no bcrypt needed)
- **Firestore data** (hotels, bookings, reviews) is shared in real-time

---

## Troubleshooting

| Problem | Solution |
|---|---|
| Migration script says "Kreait\Firebase\Exception\Auth\EmailExists" | Normal — account already in Firebase Auth, UID will be fetched |
| Android login says "Account not found in database" | Run the migration script, then verify the firebaseUid-keyed doc exists in Firestore |
| Android shows empty hotel list | Check Firestore `hotels` collection — make sure status field = "active" |
| Firestore rules error in Android | Re-check Step 2 — rules must be published |
