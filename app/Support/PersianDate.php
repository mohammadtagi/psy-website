<?php

namespace App\Support;

use Carbon\CarbonImmutable;
use DateTimeInterface;
use IntlCalendar;
use IntlDateFormatter;
use InvalidArgumentException;
use RuntimeException;

final class PersianDate
{
    private const TIMEZONE = 'Asia/Tehran';

    public static function format(
        DateTimeInterface $date,
        string $pattern = 'yyyy/MM/dd',
    ): string {
        $formatter = new IntlDateFormatter(
            'fa_IR@calendar=persian',
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            self::TIMEZONE,
            IntlDateFormatter::TRADITIONAL,
            $pattern,
        );

        $result = $formatter->format($date);

        if ($result === false) {
            throw new RuntimeException('Unable to format Persian date.');
        }

        return $result;
    }

    public static function toGregorian(string $input): string
    {
        $normalized = strtr(trim($input), [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3',
            '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7',
            '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3',
            '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7',
            '٨' => '8', '٩' => '9',
        ]);

        if (! preg_match(
            '/\A([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})\z/',
            $normalized,
            $matches,
        )) {
            throw new InvalidArgumentException('Invalid Persian date.');
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];

        if (
            $year < 1
            || $month < 1
            || $month > 12
            || $day < 1
            || $day > 31
        ) {
            throw new InvalidArgumentException('Invalid Persian date.');
        }

        $calendar = IntlCalendar::createInstance(
            self::TIMEZONE,
            'en_US@calendar=persian',
        );

        if ($calendar === null) {
            throw new RuntimeException('Unable to create Persian calendar.');
        }

        $calendar->setLenient(false);
        $calendar->clear();
        $calendar->set($year, $month - 1, $day, 12, 0, 0);

        $milliseconds = $calendar->getTime();

        if (
            $milliseconds === false
            || $calendar->get(IntlCalendar::FIELD_YEAR) !== $year
            || $calendar->get(IntlCalendar::FIELD_MONTH) !== $month - 1
            || $calendar->get(IntlCalendar::FIELD_DAY_OF_MONTH) !== $day
        ) {
            throw new InvalidArgumentException('Invalid Persian date.');
        }

        return CarbonImmutable::createFromTimestamp(
            $milliseconds / 1000,
            self::TIMEZONE,
        )->format('Y-m-d');
    }


    public static function birthDateToGregorian(string $input): string
    {
        $normalized = strtr(trim($input), [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3',
            '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7',
            '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3',
            '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7',
            '٨' => '8', '٩' => '9',
        ]);

        if (! preg_match(
            '/\A([0-9]{4})\/([0-9]{1,2})\/([0-9]{1,2})\z/',
            $normalized,
            $matches,
        )) {
            throw new InvalidArgumentException('Invalid Persian date.');
        }

        $year = (int) $matches[1];
        $month = (int) $matches[2];
        $day = (int) $matches[3];

        if (
            $year < 1
            || $month < 1
            || $month > 12
            || $day < 1
            || $day > 31
        ) {
            throw new InvalidArgumentException('Invalid Persian date.');
        }

        $calendar = IntlCalendar::createInstance(
            self::TIMEZONE,
            'en_US@calendar=persian',
        );

        if ($calendar === null) {
            throw new RuntimeException('Unable to create Persian calendar.');
        }

        $calendar->setLenient(false);
        $calendar->clear();

        // Noon avoids edge cases around historical midnight offset changes.
        $calendar->set($year, $month - 1, $day, 12, 0, 0);

        $milliseconds = $calendar->getTime();

        if (
            $milliseconds === false
            || $calendar->get(IntlCalendar::FIELD_YEAR) !== $year
            || $calendar->get(IntlCalendar::FIELD_MONTH) !== $month - 1
            || $calendar->get(IntlCalendar::FIELD_DAY_OF_MONTH) !== $day
        ) {
            throw new InvalidArgumentException('Invalid Persian date.');
        }

        $date = CarbonImmutable::createFromTimestamp(
            $milliseconds / 1000,
            self::TIMEZONE,
        );

        if ($date->startOfDay()->gt(
            CarbonImmutable::now(self::TIMEZONE)->startOfDay()
        )) {
            throw new InvalidArgumentException('Birth date is in the future.');
        }

        return $date->format('Y-m-d');
    }
}
