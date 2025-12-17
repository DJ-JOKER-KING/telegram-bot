<?php

$BOT_TOKEN = getenv("BOT_TOKEN");
$API = "https://api.telegram.org/bot$BOT_TOKEN/";

$update = json_decode(file_get_contents("php://input"), true);

if(isset($update["message"])){

    $chat_id = $update["message"]["chat"]["id"];
    $text = $update["message"]["text"] ?? "";

    if($text == "/start"){
        send("✅ Bot is Live on Railway!", $chat_id);
    } else {
        send("🤖 You said: $text", $chat_id);
    }
}

function send($msg, $chat_id){
    global $API;
    file_get_contents($API."sendMessage?chat_id=$chat_id&text=".urlencode($msg));
}
