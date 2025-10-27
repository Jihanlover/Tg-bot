<?php
// api/bot.php

// --------------------------------------------------
// বট টোকেন
// --------------------------------------------------
$botToken = "8448651142:AAGs0u1DrD6OFlhuLDBbpimkBzGTd9TNdRU";
$apiBaseUrl = "https://api.telegram.org/bot" . $botToken;

// --------------------------------------------------
// টেলিগ্রাম থেকে আসা ডেটা গ্রহণ করুন
// --------------------------------------------------
$update = json_decode(file_get_contents("php://input"), TRUE);

if (isset($update["callback_query"])) {
    // -----------------
    // বাটন ক্লিক (Callback Query) হ্যান্ডেল করুন
    // -----------------
    $callbackQuery = $update["callback_query"];
    $chat_id = $callbackQuery["message"]["chat"]["id"];
    $callback_data = $callbackQuery["data"];
    $callback_query_id = $callbackQuery["id"];
    $responseText = "";
    if ($callback_data == "btn_1") {
        $responseText = "আপনি 'বাটন ১' চেপেছেন।";
    } elseif ($callback_data == "btn_2") {
        $responseText = "আপনি 'বাটন ২' চেপেছেন।";
    } elseif ($callback_data == "btn_url") {
        answerCallbackQuery($callback_query_id, "গুগল খুলছে...");
        exit;
    }
    sendMessage($chat_id, $responseText);
    answerCallbackQuery($callback_query_id, "সম্পন্ন!");

} elseif (isset($update["message"])) {
    // -----------------
    // সাধারণ মেসেজ হ্যান্ডেল করুন
    // -----------------
    $message = $update["message"];
    $chat_id = $message["chat"]["id"];
    $text = $message["text"];

    if ($text == "/start") {
        // -----------------
        // /start কমান্ডের জন্য কিবোর্ড তৈরি করুন
        // -----------------
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ বাটন ১', 'callback_data' => 'btn_1'],
                    ['text' => '❌ বাটন ২', 'callback_data' => 'btn_2']
                ],
                [
                    ['text' => '🌐 গুগল ভিজিট করুন', 'url' => 'https://www.google.com']
                ]
            ]
        ];
        $replyMarkup = json_encode($keyboard);
        $welcomeMessage = "স্বাগতম! এটি Vercel থেকে চলছে। নিচের একটি বাটন সিলেক্ট করুন:";
        sendMessage($chat_id, $welcomeMessage, $replyMarkup);
    } else {
        $replyText = "আপনি বলেছেন: " . $text . "\n\nকিবোর্ড দেখতে /start টাইপ করুন।";
        sendMessage($chat_id, $replyText);
    }
}

// --------------------------------------------------
// ফাংশন: টেলিগ্রামে মেসেজ পাঠানোর জন্য
// --------------------------------------------------
function sendMessage($chat_id, $text, $reply_markup = null) {
    global $apiBaseUrl;
    $url = $apiBaseUrl . "/sendMessage";
    $postData = ['chat_id' => $chat_id, 'text' => $text, 'parse_mode' => 'HTML'];
    if ($reply_markup) {
        $postData['reply_markup'] = $reply_markup;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

// --------------------------------------------------
// ফাংশন: বাটন ক্লিকের পর লোডিং আইকন বন্ধ করার জন্য
// --------------------------------------------------
function answerCallbackQuery($callback_query_id, $text = "") {
    global $apiBaseUrl;
    $url = $apiBaseUrl . "/answerCallbackQuery";
    $postData = ['callback_query_id' => $callback_query_id, 'text' => $text, 'show_alert' => false];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
?>
