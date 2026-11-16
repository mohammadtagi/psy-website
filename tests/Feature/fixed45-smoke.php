<?php

// Run from the project root: php tests/fixed45-smoke.php
// Requires the project's Composer dependencies; does not boot Laravel or access a database.
require dirname(__DIR__).'/vendor/autoload.php';

use App\Services\Booking\BookingService;
use Carbon\CarbonImmutable;

$service = new BookingService();
$round = new ReflectionMethod($service, 'roundUpToQuarterHour');
$first = new ReflectionMethod($service, 'firstSlotStart');
$interval = (new ReflectionClass($service))->getConstant('SLOT_INTERVAL_MINUTES');
$checks = 0;
$equal = static function ($expected, $actual) use (&$checks): void {
    if ($expected !== $actual) {
        throw new RuntimeException(var_export([$expected, $actual], true));
    }
    $checks++;
};
$local = static fn (string $time) => CarbonImmutable::parse('2030-01-01 '.$time, 'Asia/Tehran');
foreach ([['09:12', '09:15'], ['09:15', '09:15'], ['09:15:01', '09:30'], ['09:15:00.000001', '09:30'], ['09:59', '10:00'], ['23:59', '00:00']] as [$input, $expected]) {
    $equal($expected, $round->invoke($service, $local($input))->setTimezone('Asia/Tehran')->format('H:i'));
}
$equal(45, $interval);
$starts = [];
$end = $local('12:00')->utc();
for ($start = $first->invoke($service, $local('09:12'), $local('00:00')->utc()); $start->addMinutes(45)->lte($end); $start = $start->addMinutes($interval)) {
    $starts[] = $start->setTimezone('Asia/Tehran')->format('H:i');
}
$equal(['09:15', '10:00', '10:45'], $starts);
$equal(true, $round->invoke($service, $local('09:12'))->addMinutes(45)->eq($local('10:00')));
$equal(false, $round->invoke($service, $local('09:12'))->addMinutes(45)->lte($local('09:59')));
$nextDay = $local('00:00')->addDay()->utc();
$equal('00:30', $first->invoke($service, $local('23:40'), $nextDay)->setTimezone('Asia/Tehran')->format('H:i'));
$equal(760000, $service->amountForDuration(45));
echo "Passed {$checks} checks (no database).\n";
