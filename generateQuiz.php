<?php
require_once 'config.php';

if (!isset($_POST['textContent'])) {
    echo json_encode(['error' => 'No text content provided']);
    exit;
}

$textContent = $_POST['textContent'];
$apiKey = $_ENV['GEMINI_API_KEY'];

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

$data = [
    'contents' => [
        [
            'parts' => [
                [
                    'text' => "Generate 5 multiple-choice quiz questions based on the following text:\n$textContent\nFormat: Q1) Question text? \nA) Option1 \nB) Option2 \nC) Option3 \nD) Option4 \nAnswer: A"
                ]
            ]
        ]
    ]
];

$options = [
    'http' => [
        'header' => "Content-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($data),
    ],
];

$context = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode(['error' => 'API call failed']);
    exit;
}

$response = json_decode($result, true);
$generatedText = $response['candidates'][0]['content']['parts'][0]['text'];

$questionsArray = explode("\n", $generatedText);
$cleanedQuestions = array_map(function($line) {
    return str_replace('**', '', $line);
}, array_filter($questionsArray, function($line) {
    return trim($line) !== '';
}));

echo json_encode($cleanedQuestions);
?>