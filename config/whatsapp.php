<?php

return [
    'base_url' => env('WHATSAPP_API_BASE_URL', 'https://graph.facebook.com/v22.0'),
    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID', '327333167128874'),
    'access_token' => env('WHATSAPP_ACCESS_TOKEN', ''),
    'template_name' => env('WHATSAPP_TEMPLATE_NAME', 'phptraining_start'),
    'template_language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'pt_BR'),
    'template_image_url' => env('WHATSAPP_TEMPLATE_IMAGE_URL', 'https://example.com/image.jpg'),
    'test_phone_number' => env('WHATSAPP_TEST_PHONE_NUMBER', '5511970954944'),
    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
    'message_type' => env('WHATSAPP_MESSAGE_TYPE', 'template'),
    'language_code' => env('WHATSAPP_LANGUAGE', 'en'),
];
