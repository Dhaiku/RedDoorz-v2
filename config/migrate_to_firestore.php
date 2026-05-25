<?php
/**
 * migrate_to_firestore.php
 * Run ONCE from the command line to copy all MySQL data into Firestore.
 *
 * Usage (with MySQL still running):
 *   C:\xampp\php\php.exe C:\RedDoorz\config\migrate_to_firestore.php
 *
 * What it does:
 *   1. Connects to MySQL
 *   2. Reads every table
 *   3. Writes each row as a Firestore document (using integer MySQL ID as document ID)
 *   4. Sets counters so fs_next_id() starts above the highest existing ID
 */

set_time_limit(0);
require_once __DIR__ . '/firestore.php';

// ── MySQL connection ──────────────────────────────────────────────────────────
$mysql = new mysqli("127.0.0.1", "root", "", "reddoorz", 3307);
if ($mysql->connect_error) {
    die("MySQL connection failed: " . $mysql->connect_error . "\n");
}
echo "MySQL connected.\n";

// ── Field maps: MySQL column → Firestore field ──────────────────────────────
$maps = [
    'accounts' => [
        'table'  => 'Accounts',
        'idCol'  => 'Acct_Id',
        'fields' => [
            'email'             => 'Acct_Email',
            'password'          => 'Acct_Password',
            'role'              => 'Acct_Role',
            'status'            => 'Acct_Status',
            'mustChangePassword'=> 'Acct_MustChangePassword',
            'firebaseUid'       => 'Acct_FirebaseUid',
            'createdAt'         => 'Acct_CreatedAt',
        ],
    ],
    'customers' => [
        'table'  => 'Customers',
        'idCol'  => 'Cust_Id',
        'fields' => [
            'acctId'    => 'Cust_AcctId',
            'firstName' => 'Cust_FName',
            'lastName'  => 'Cust_LName',
            'phone'     => 'Cust_Phone',
        ],
    ],
    'hotels' => [
        'table'  => 'Hotels',
        'idCol'  => 'Hotel_Id',
        'fields' => [
            'name'        => 'Hotel_Name',
            'city'        => 'Hotel_City',
            'address'     => 'Hotel_Address',
            'description' => 'Hotel_Description',
            'amenities'   => 'Hotel_Amenities',
            'image'       => 'Hotel_Image',
            'rating'      => 'Hotel_Rating',
            'status'      => 'Hotel_Status',
            'ownerId'     => 'Hotel_OwnerId',
            'createdAt'   => 'Hotel_CreatedAt',
        ],
    ],
    'rooms' => [
        'table'  => 'Rooms',
        'idCol'  => 'Room_Id',
        'fields' => [
            'hotelId'     => 'Room_HotelId',
            'type'        => 'Room_Type',
            'price'       => 'Room_Price',
            'capacity'    => 'Room_Capacity',
            'description' => 'Room_Description',
            'status'      => 'Room_Status',
        ],
    ],
    'bookings' => [
        'table'  => 'Bookings',
        'idCol'  => 'Book_Id',
        'fields' => [
            'custId'     => 'Book_CustId',
            'hotelId'    => 'Book_HotelId',
            'roomId'     => 'Book_RoomId',
            'checkIn'    => 'Book_CheckIn',
            'checkOut'   => 'Book_CheckOut',
            'guests'     => 'Book_Guests',
            'totalPrice' => 'Book_TotalPrice',
            'status'     => 'Book_Status',
            'refCode'    => 'Book_RefCode',
            'createdAt'  => 'Book_CreatedAt',
        ],
    ],
    'payments' => [
        'table'  => 'Payments',
        'idCol'  => 'Paymt_Id',
        'fields' => [
            'bookId'   => 'Paymt_BookId',
            'method'   => 'Paymt_Method',
            'status'   => 'Paymt_Status',
            'refCode'  => 'Paymt_RefCode',
            'amount'   => 'Paymt_Amount',
            'createdAt'=> 'Paymt_Date',
        ],
    ],
    'earnings' => [
        'table'  => 'Earnings',
        'idCol'  => 'Earn_Id',
        'fields' => [
            'bookId'      => 'Earn_BookId',
            'hotelId'     => 'Earn_HotelId',
            'ownerId'     => 'Earn_OwnerId',
            'totalAmount' => 'Earn_TotalAmount',
            'ownerShare'  => 'Earn_OwnerShare',
            'platformFee' => 'Earn_PlatformFee',
            'status'      => 'Earn_Status',
            'createdAt'   => 'Earn_CreatedAt',
        ],
    ],
    'reviews' => [
        'table'  => 'Reviews',
        'idCol'  => 'Review_Id',
        'fields' => [
            'bookId'    => 'Review_BookId',
            'hotelId'   => 'Review_HotelId',
            'custId'    => 'Review_CustId',
            'rating'    => 'Review_Rating',
            'comment'   => 'Review_Comment',
            'createdAt' => 'Review_CreatedAt',
        ],
    ],
    'hotelstaff' => [
        'table'  => 'HotelStaff',
        'idCol'  => 'Staff_Id',
        'fields' => [
            'hotelId'   => 'Staff_HotelId',
            'name'      => 'Staff_Name',
            'role'      => 'Staff_Role',
            'email'     => 'Staff_Email',
            'phone'     => 'Staff_Phone',
            'status'    => 'Staff_Status',
            'createdAt' => 'Staff_CreatedAt',
        ],
    ],
    'fcmtokens' => [
        'table'  => 'FcmTokens',
        'idCol'  => 'Token_Id',
        'fields' => [
            'acctId'    => 'Token_AcctId',
            'token'     => 'Token_Value',
            'updatedAt' => 'Token_UpdatedAt',
        ],
    ],
    'payoutrequests' => [
        'table'  => 'PayoutRequests',
        'idCol'  => 'Payout_Id',
        'fields' => [
            'ownerId'   => 'Payout_OwnerId',
            'hotelId'   => 'Payout_HotelId',
            'amount'    => 'Payout_Amount',
            'method'    => 'Payout_Method',
            'accountNo' => 'Payout_AccountNo',
            'status'    => 'Payout_Status',
            'createdAt' => 'Payout_CreatedAt',
        ],
    ],
    'blockeddates' => [
        'table'  => 'BlockedDates',
        'idCol'  => 'Block_Id',
        'fields' => [
            'roomId'    => 'Block_RoomId',
            'hotelId'   => 'Block_HotelId',
            'dateFrom'  => 'Block_DateFrom',
            'dateTo'    => 'Block_DateTo',
            'reason'    => 'Block_Reason',
            'createdAt' => 'Block_CreatedAt',
        ],
    ],
    'ownerapplications' => [
        'table'  => 'OwnerApplications',
        'idCol'  => 'App_Id',
        'fields' => [
            'acctId'      => 'App_AcctId',
            'hotelName'   => 'App_HotelName',
            'city'        => 'App_City',
            'address'     => 'App_Address',
            'description' => 'App_Description',
            'status'      => 'App_Status',
            'createdAt'   => 'App_CreatedAt',
        ],
    ],
];

// ── Migrate each table ───────────────────────────────────────────────────────
foreach ($maps as $fsCollection => $cfg) {
    $table  = $cfg['table'];
    $idCol  = $cfg['idCol'];
    $fields = $cfg['fields'];

    $res = $mysql->query("SELECT * FROM `$table`");
    if (!$res) {
        echo "  Skipping $table (table may not exist)\n";
        continue;
    }

    $count  = 0;
    $maxId  = 0;

    while ($row = $res->fetch_assoc()) {
        $id  = (int) $row[$idCol];
        $doc = ['id' => $id];
        foreach ($fields as $fsField => $sqlCol) {
            $val = $row[$sqlCol] ?? null;
            // Cast numeric fields
            if (in_array($fsField, ['acctId','hotelId','roomId','custId','ownerId','bookId','staffId'])) {
                $val = $val !== null ? (int)$val : null;
            } elseif (in_array($fsField, ['price','totalPrice','totalAmount','ownerShare','platformFee','amount','rating'])) {
                $val = $val !== null ? (float)$val : null;
            } elseif (in_array($fsField, ['mustChangePassword'])) {
                $val = (bool)$val;
            } elseif (in_array($fsField, ['capacity','guests'])) {
                $val = $val !== null ? (int)$val : null;
            }
            if ($val !== null) $doc[$fsField] = $val;
        }

        // Write directly with integer document ID
        global $firestore;
        $firestore->collection($fsCollection)->document((string)$id)->set($doc);

        $count++;
        $maxId = max($maxId, $id);
        if ($count % 50 === 0) echo "  $fsCollection: $count rows...\n";
    }

    // Set counter above the highest existing ID
    if ($maxId > 0) {
        $firestore->collection('counters')->document($fsCollection)->set(['next' => $maxId + 1]);
    }

    echo "✓ $fsCollection: $count documents migrated (next ID = " . ($maxId + 1) . ")\n";
}

echo "\n✅ Migration complete.\n";
echo "You can now stop MySQL and run the site on Firestore only.\n";
