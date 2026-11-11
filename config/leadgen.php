<?php

$ids = array_filter(array_map('trim', explode(',', (string) env('LEADGEN_ALLOWED_USER_IDS', ''))));

return [
    'allowed_user_ids' => array_map('intval', $ids),
    'calling_entity_legal_name' => env('LEADGEN_CALLING_ENTITY_LEGAL_NAME', ''),
    'outbound_ai_calling_enabled' => filter_var(
        env('LEADGEN_OUTBOUND_AI_CALLING_ENABLED', false),
        FILTER_VALIDATE_BOOL
    ),
];
