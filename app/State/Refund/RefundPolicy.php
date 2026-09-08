<?php

declare(strict_types=1);

namespace App\States\Refund;

use Carbon\CarbonInterface;
use InvalidArgumentException;

final class RefundPolicy
{
    public function percentageForCancellation(
        CarbonInterface $startsAt,
        CarbonInterface $now,
    ): int {
        $secondsUntilStart = $now->diffInSeconds($startsAt, false);

        if ($secondsUntilStart < 2 * 60 * 60) {
            return 0;
        }

        if ($secondsUntilStart >= 24 * 60 * 60) {
            return 100;
        }

        return 50;
    }

    public function amountForCancellation(
        int $paidAmount,
        CarbonInterface $startsAt,
        CarbonInterface $now,
    ): int {
        if ($paidAmount < 0) {
            throw new InvalidArgumentException(
                'Paid amount cannot be negative.'
            );
        }

        return (int) floor(
            $paidAmount * $this->percentageForCancellation($startsAt, $now) / 100
        );
    }
}
