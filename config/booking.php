<?php

return [
    'cancellation_before_minutes' => (int) env(
        'APPOINTMENT_CANCELLATION_BEFORE_MINUTES',
        720,
    ),

    'new_booking_grace_minutes' => (int) env(
        'APPOINTMENT_NEW_BOOKING_GRACE_MINUTES',
        60,
    ),
];
