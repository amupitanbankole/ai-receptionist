<?php

$ids = array_filter(array_map('trim', explode(',', (string) env('LEADGEN_ALLOWED_USER_IDS', ''))));

return [
    /*
    |--------------------------------------------------------------------------
    | Lead generation operator allowlist
    |--------------------------------------------------------------------------
    |
    | Set LEADGEN_ALLOWED_USER_IDS to comma-separated authenticated user IDs.
    | An empty value intentionally denies access to the module.
    |
    */
    'allowed_user_ids' => array_map('intval', $ids),

    // Outbound AI calling remains disabled until legal review and deployment checks.
    'calling_entity_legal_name' => env('LEADGEN_CALLING_ENTITY_LEGAL_NAME', ''),\n    'outbound_ai_calling_enabled' => (bool) env('LEADGEN_OUTBOUND_AI_CALLING_ENABLED', false),
];
