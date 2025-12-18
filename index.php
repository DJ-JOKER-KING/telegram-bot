<?php
$BOT_TOKEN = getenv("BOT_TOKEN");
$ADMIN_ID  = getenv("ADMIN_ID");
$API = "https://api.telegram.org/bot$BOT_TOKEN";

$usersFile = __DIR__ . "/users.json";
$users = file_exists($usersFile)
    ? json_decode(file_get_contents($usersFile), true)
    : [];

$update = json_decode(file_get_contents("php://input"), true);
if (!isset($update["message"])) { echo "OK"; exit; }

$chat_id = $update["message"]["chat"]["id"];
$text = trim($update["message"]["text"] ?? "");

/* ===== DEVICE ID ===== */
$userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "unknown";
$device_id = md5($chat_id . $userAgent);

function saveUsers($data, $file){
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}
function sendMessage($chat_id, $text){
    global $API;
    file_get_contents($API."/sendMessage?".http_build_query([
        "chat_id"=>$chat_id,
        "text"=>$text
    ]));
}

/* ===== AUTO EXPIRE ===== */
if (isset($users[$chat_id]["expire"]) && time() > $users[$chat_id]["expire"]) {
    unset($users[$chat_id]);
    saveUsers($users, $usersFile);
    sendMessage($chat_id, "⏳ Your VIP expired");
    exit;
}

/* ===== /start ===== */
if ($text === "/start") {

    if (isset($users[$chat_id]) && $users[$chat_id]["approved"] === true) {

        // DEVICE CHECK
        if (!isset($users[$chat_id]["device"])) {
            $users[$chat_id]["device"] = $device_id;
            saveUsers($users, $usersFile);
        } elseif ($users[$chat_id]["device"] !== $device_id) {
            sendMessage($chat_id, "🚫 Device changed!\nContact admin");
            exit;
        }

        sendMessage(
            $chat_id,
            "👑 VIP Active\nExpires: ".date("d M Y", $users[$chat_id]["expire"])
        );
        exit;
    }

    $users[$chat_id] = ["approved"=>false];
    saveUsers($users, $usersFile);

    sendMessage($chat_id, "⏳ Waiting for admin approval");

    sendMessage(
        $ADMIN_ID,
        "🔔 New Request\nUser ID: $chat_id\n\nApprove:\n/approve $chat_id DAYS"
    );
    exit;
}

/* ===== ADMIN ===== */
if ((string)$chat_id === (string)$ADMIN_ID) {

    // APPROVE ID DAYS
    if (strpos($text, "/approve") === 0) {
        $p = explode(" ", $text);
        $id = $p[1] ?? null;
        $days = $p[2] ?? 0;

        if ($id && $days > 0) {
            $users[$id] = [
                "approved" => true,
                "expire" => time() + ($days * 86400),
                "device" => null
            ];
            saveUsers($users, $usersFile);

            sendMessage($id, "✅ VIP Approved\nValid $days days");
            sendMessage($ADMIN_ID, "✔ Approved");
        } else {
            sendMessage($ADMIN_ID, "❌ Use:\n/approve USER_ID DAYS");
        }
        exit;
    }

    // RESET DEVICE
    if (strpos($text, "/resetdevice") === 0) {
        $id = explode(" ", $text)[1] ?? null;
        if ($id && isset($users[$id])) {
            $users[$id]["device"] = null;
            saveUsers($users, $usersFile);
            sendMessage($ADMIN_ID, "🔓 Device reset done");
            sendMessage($id, "🔄 Device reset\nLogin again");
        }
        exit;
    }
}

/* ===== BLOCK ===== */
if (!isset($users[$chat_id]) || $users[$chat_id]["approved"] !== true) {
    sendMessage($chat_id, "⛔ Not approved");
    exit;
}

/* ===== VIP ===== */
sendMessage($chat_id, "👑 VIP Access OK");
