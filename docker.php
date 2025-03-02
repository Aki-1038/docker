<?php
// 用來存放 OpenAI API Key，請根據您的需求替換
$openai_api_key = 'OpenAI API Key';

// 使用 OpenAI API - php

// 從前端接收 JSON 格式的輸入數據
$input_data = json_decode(file_get_contents('php://input'), true);
$user_message = $input_data['message']; // 獲取使用者輸入的消息
$chat_history = $input_data['history']; // 獲取聊天歷史記錄

// 合併之前的對話歷史（如果有）
$messages = [];

// 加入系統提示
$messages[] = ['role' => 'system', 'content' => '
請給我一個詳細的指南，教我如何使用Docker創建和管理容器。
我希望能了解基本的Docker命令、容器管理和如何在本地環境中運行一個簡單的應用程式。
若有範例程式碼和步驟說明，將會更有幫助。
# CONTEXT # 使用者希望學習Docker容器的操作，涵蓋基本命令與應用。
# OBJECTIVE # 獲得詳細的Docker操作指南和實用範例。
# STYLE # 要求具體指引和範例程式碼。
# TONE # 友好且具教育意義，讓使用者能輕鬆理解。
# AUDIENCE # Docker初學者，希望系統化學習基礎知識和實踐。
# RESPONSE # 提供一系列Docker命令的指南，並附上範例和操作步驟。 


#zh-TW
使用臺灣特有的慣用語和口語表達，讓交流更自然。'];

foreach ($chat_history as $message) {
    // 將聊天歷史中的每條消息轉換為 OpenAI API 所需的格式
    $messages[] = ['role' => $message['sender'] == 'user' ? 'user' : 'assistant', 'content' => $message['text']];
}
// 將使用者最新的消息添加到消息數組中
$messages[] = ['role' => 'user', 'content' => $user_message];





// 發送請求到 OpenAI API
$ch = curl_init(); // 初始化 cURL
curl_setopt($ch, CURLOPT_URL, 'https://api.openai.com/v1/chat/completions'); // 設定 API 請求的 URL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 設定返回結果為字串
curl_setopt($ch, CURLOPT_POST, true); // 設定請求方法為 POST
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json', // 設定請求頭為 JSON 格式
    'Authorization: Bearer ' . $openai_api_key, // 設定 API Key 驗證
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    //'model' => 'gpt-4o-mini',  // 選擇使用的模型（請根據需求替換）
    'model' => 'gpt-4o',  // 選擇使用的模型（請根據需求替換）
    'messages' => $messages, // 傳送消息數組
    'max_tokens' => 3000 // 設定最大生成的 token 數
]));

$response = curl_exec($ch); // 執行 cURL 請求並獲取回應
curl_close($ch); // 關閉 cURL 會話

// 解析並返回回應
$response_data = json_decode($response, true); // 將回應解析為 JSON 格式
$bot_reply = $response_data['choices'][0]['message']['content'] ?? '抱歉，發生了錯誤。'; // 獲取機器人回覆，若出錯則返回預設訊息

// 返回回應
echo json_encode(['reply' => $bot_reply]); // 將機器人回覆以 JSON 格式返回給前端
?>
