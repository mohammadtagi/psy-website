<?php

return [
    'hold_minutes' => (int) env('PAYMENT_HOLD_MINUTES', 15),

    'online_enabled' => (bool) env('PAYMENT_ONLINE_ENABLED', false),
];
