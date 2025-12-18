<?php
// ===== CONFIG =====
$BOT_TOKEN = getenv("BOT_TOKEN");   // Railway Variable
$ADMIN_ID  = getenv("ADMIN_ID");    // Your Telegram ID
$API = "https://api.telegram.org/bot$BOT_TOKEN";

// ===== DATABASE FILE =====
$usersFile = __DIR__ . "/users.json";
$users = file_exists($usersFile)
    ? json_decode(file_get_contents($usersFile), true)
    : [];

// ===== GET UPDATE =====
$update = json_decode(file_get_contents("php://input"), true);
if (!isset($update["message"])) {
    echo "OK";
    exit;
}

$chat_id = $update["message"]["chat"]["id"];
$text = trim($update["message"]["text"] ?? "");

// ===== FUNCTIONS =====
function saveUsers($data, $file) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function sendMessage($chat_id, $text) {
    global $API;
    file_get_contents($API . "/sendMessage?" . http_build_query([
        "chat_id" => $chat_id,
        "text" => $text
    ]));
}

// ===== /start COMMAND =====
if ($text === "/start") {

    if (isset($users[$chat_id]) && $users[$chat_id]["approved"] === true) {
        sendMessage($chat_id, "✅ You are already approved\n👑 Welcome VIP");
        exit;
    }

    // New or pending user
    $users[$chat_id] = [
        "approved" => false,
        "time" => time()
    ];
    saveUsers($users, $usersFile);

    sendMessage($chat_id, "⏳ Your request is pending admin approval");

    sendMessage(
        $ADMIN_ID,
        "🔔 New Approval Request\n\nUser ID: $chat_id\n\nApprove:\n/approve $chat_id\nReject:\n/reject $chat_id"
    );
    exit;
}

// ===== ADMIN COMMANDS =====
if ((string)$chat_id === (string)$ADMIN_ID) {

    // APPROVE
    if (strpos($text, "/approve") === 0) {
        $id = trim(explode(" ", $text)[1] ?? "");
        if ($id && isset($users[$id])) {
            $users[$id]["approved"] = true;
            saveUsers($users, $usersFile);

            sendMessage($id, "✅ Approved!\n👑 You are now VIP");
            sendMessage($ADMIN_ID, "✔ User $id Approved");
        } else {
            sendMessage($ADMIN_ID, "❌ User not found");
        }
        exit;
    }

    // REJECT
    if (strpos($text, "/reject") === 0) {
        $id = trim(explode(" ", $text)[1] ?? "");
        if ($id && isset($users[$id])) {
            unset($users[$id]);
            saveUsers($users, $usersFile);

            sendMessage($id, "❌ Your access request was rejected");
            sendMessage($ADMIN_ID, "✖ User $id Rejected");
        } else {
            sendMessage($ADMIN_ID, "❌ User not found");
        }
        exit;
    }
}

// ===== BLOCK UNAPPROVED USERS =====
if (!isset($users[$chat_id]) || $users[$chat_id]["approved"] !== true) {
    sendMessage($chat_id, "⛔ You are not approved yet");
    exit;
}

// ===== APPROVED USER RESPONSE =====
sendMessage($chat_id, "👑 VIP Access Granted\nUse bot commands now");
