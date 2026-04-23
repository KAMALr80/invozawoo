<?php

// app/Helpers/HolidayHelper.php

namespace App\Helpers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class HolidayHelper
{
    /**
     * Calculate Easter date for a given year using the Anonymous Gregorian algorithm
     * This works without the PHP calendar extension
     *
     * @param int $year
     * @return Carbon
     */
    private static function getEasterDate($year)
    {
        // Using the Anonymous Gregorian algorithm (works in all PHP versions)
        $a = $year % 19;
        $b = floor($year / 100);
        $c = $year % 100;
        $d = floor($b / 4);
        $e = $b % 4;
        $f = floor(($b + 8) / 25);
        $g = floor(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = floor($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = floor(($a + 11 * $h + 22 * $l) / 451);
        $month = floor(($h + $l - 7 * $m + 114) / 31);
        $day = (($h + $l - 7 * $m + 114) % 31) + 1;

        return Carbon::create($year, $month, $day)->startOfDay();
    }

    /**
     * Get fixed holidays (same date every year)
     *
     * @param int|null $year
     * @return array
     */
    public static function getFixedHolidays($year = null)
    {
        $year = $year ?? now()->year;

        return [
            [
                'name' => "New Year's Day",
                'date' => Carbon::create($year, 1, 1)->startOfDay(),
                'is_optional' => false,
                'type' => 'fixed'
            ],
            [
                'name' => 'Republic Day',
                'date' => Carbon::create($year, 1, 26)->startOfDay(),
                'is_optional' => false,
                'type' => 'fixed'
            ],
            [
                'name' => 'International Women\'s Day',
                'date' => Carbon::create($year, 3, 8)->startOfDay(),
                'is_optional' => true,
                'type' => 'fixed'
            ],
            [
                'name' => 'Independence Day',
                'date' => Carbon::create($year, 8, 15)->startOfDay(),
                'is_optional' => false,
                'type' => 'fixed'
            ],
            [
                'name' => 'Gandhi Jayanti',
                'date' => Carbon::create($year, 10, 2)->startOfDay(),
                'is_optional' => false,
                'type' => 'fixed'
            ],
            [
                'name' => 'Christmas Day',
                'date' => Carbon::create($year, 12, 25)->startOfDay(),
                'is_optional' => false,
                'type' => 'fixed'
            ],
        ];
    }

    /**
     * Get dynamic holidays (based on calculations like Easter)
     *
     * @param int|null $year
     * @return array
     */
    public static function getDynamicHolidays($year = null)
    {
        $year = $year ?? now()->year;
        $holidays = [];

        // Easter Sunday (calculated dynamically)
        try {
            $easter = self::getEasterDate($year);
            $holidays[] = [
                'name' => 'Easter Sunday',
                'date' => $easter,
                'is_optional' => true,
                'type' => 'dynamic'
            ];

            // Good Friday (2 days before Easter)
            $holidays[] = [
                'name' => 'Good Friday',
                'date' => $easter->copy()->subDays(2),
                'is_optional' => true,
                'type' => 'dynamic'
            ];

            // Easter Monday (1 day after Easter)
            $holidays[] = [
                'name' => 'Easter Monday',
                'date' => $easter->copy()->addDay(),
                'is_optional' => true,
                'type' => 'dynamic'
            ];
        } catch (\Exception $e) {
            // Fallback dates if calculation fails
            $holidays[] = [
                'name' => 'Easter Sunday',
                'date' => Carbon::create($year, 4, 12)->startOfDay(),
                'is_optional' => true,
                'type' => 'dynamic'
            ];
            $holidays[] = [
                'name' => 'Good Friday',
                'date' => Carbon::create($year, 4, 10)->startOfDay(),
                'is_optional' => true,
                'type' => 'dynamic'
            ];
        }

        // Thanksgiving (4th Thursday of November)
        $thanksgiving = Carbon::create($year, 11, 1)->startOfDay()->next('Thursday')->addWeeks(3);
        $holidays[] = [
            'name' => 'Thanksgiving Day',
            'date' => $thanksgiving,
            'is_optional' => true,
            'type' => 'dynamic'
        ];

        // Black Friday (day after Thanksgiving)
        $holidays[] = [
            'name' => 'Black Friday',
            'date' => $thanksgiving->copy()->addDay(),
            'is_optional' => true,
            'type' => 'dynamic'
        ];

        return $holidays;
    }

    /**
     * Get Diwali date (approximate calculation)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getDiwaliDate($year)
    {
        // Diwali usually falls between October 15 and November 15
        // This is a simplified calculation
        $baseDate = Carbon::create($year, 10, 15)->startOfDay();
        $dayOffset = ($year * 7) % 30;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Eid al-Fitr date (approximate)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getEidDate($year)
    {
        // Simplified Eid calculation
        $baseDate = Carbon::create($year, 5, 1)->startOfDay();
        $dayOffset = ($year * 3) % 28;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Holi date (March full moon approximation)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getHoliDate($year)
    {
        // Holi is typically in March
        $baseDate = Carbon::create($year, 3, 8)->startOfDay();
        $dayOffset = ($year * 2) % 20;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Mahashivratri date (February/March)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getMahashivratriDate($year)
    {
        $baseDate = Carbon::create($year, 2, 20)->startOfDay();
        $dayOffset = ($year * 3) % 28;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Ram Navami date (March/April)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getRamNavamiDate($year)
    {
        $baseDate = Carbon::create($year, 3, 25)->startOfDay();
        $dayOffset = ($year * 2) % 25;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Raksha Bandhan date (August)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getRakshaBandhanDate($year)
    {
        $baseDate = Carbon::create($year, 8, 10)->startOfDay();
        $dayOffset = ($year * 2) % 20;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Ganesh Chaturthi date (August/September)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getGaneshChaturthiDate($year)
    {
        $baseDate = Carbon::create($year, 8, 25)->startOfDay();
        $dayOffset = ($year * 3) % 25;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get Dussehra date (October)
     *
     * @param int $year
     * @return Carbon
     */
    private static function getDussehraDate($year)
    {
        $baseDate = Carbon::create($year, 10, 5)->startOfDay();
        $dayOffset = ($year * 2) % 25;

        return $baseDate->addDays($dayOffset);
    }

    /**
     * Get month-specific/religious holidays
     *
     * @param int|null $year
     * @return array
     */
    public static function getMonthSpecificHolidays($year = null)
    {
        $year = $year ?? now()->year;

        return [
            // January
            [
                'name' => 'Makar Sankranti',
                'date' => Carbon::create($year, 1, 14)->startOfDay(),
                'is_optional' => true,
                'type' => 'religious'
            ],
            [
                'name' => 'Pongal',
                'date' => Carbon::create($year, 1, 15)->startOfDay(),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // February
            [
                'name' => 'Mahashivratri',
                'date' => self::getMahashivratriDate($year),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // March
            [
                'name' => 'Holi',
                'date' => self::getHoliDate($year),
                'is_optional' => false,
                'type' => 'religious'
            ],
            // April
            [
                'name' => 'Ram Navami',
                'date' => self::getRamNavamiDate($year),
                'is_optional' => true,
                'type' => 'religious'
            ],
            [
                'name' => 'Mahavir Jayanti',
                'date' => Carbon::create($year, 4, 6)->startOfDay(),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // May
            [
                'name' => 'Buddha Purnima',
                'date' => Carbon::create($year, 5, 7)->startOfDay(),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // August
            [
                'name' => 'Raksha Bandhan',
                'date' => self::getRakshaBandhanDate($year),
                'is_optional' => true,
                'type' => 'religious'
            ],
            [
                'name' => 'Janmashtami',
                'date' => Carbon::create($year, 8, 18)->startOfDay(),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // September
            [
                'name' => 'Ganesh Chaturthi',
                'date' => self::getGaneshChaturthiDate($year),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // October
            [
                'name' => 'Dussehra',
                'date' => self::getDussehraDate($year),
                'is_optional' => false,
                'type' => 'religious'
            ],
            [
                'name' => 'Diwali',
                'date' => self::getDiwaliDate($year),
                'is_optional' => false,
                'type' => 'religious'
            ],
            // November
            [
                'name' => 'Guru Nanak Jayanti',
                'date' => Carbon::create($year, 11, 15)->startOfDay(),
                'is_optional' => true,
                'type' => 'religious'
            ],
            // December
            [
                'name' => "New Year's Eve",
                'date' => Carbon::create($year, 12, 31)->startOfDay(),
                'is_optional' => true,
                'type' => 'fixed'
            ],
        ];
    }

    /**
     * Get all holidays for a specific year
     *
     * @param int|null $year
     * @param bool $includeOptional
     * @return array
     */
    public static function getAllHolidays($year = null, $includeOptional = true)
    {
        $year = $year ?? now()->year;

        // Merge all holiday sources
        $holidays = array_merge(
            self::getFixedHolidays($year),
            self::getDynamicHolidays($year),
            self::getMonthSpecificHolidays($year)
        );

        // Filter optional holidays if needed
        if (!$includeOptional) {
            $holidays = array_filter($holidays, function($holiday) {
                return !$holiday['is_optional'];
            });
        }

        // Remove duplicates (same date different names)
        $uniqueHolidays = [];
        foreach ($holidays as $holiday) {
            $dateKey = $holiday['date']->format('Y-m-d');
            if (!isset($uniqueHolidays[$dateKey])) {
                $uniqueHolidays[$dateKey] = $holiday;
            }
        }

        // Sort by date
        usort($uniqueHolidays, function($a, $b) {
            return $a['date']->timestamp - $b['date']->timestamp;
        });

        return array_values($uniqueHolidays);
    }

    /**
     * Get upcoming holidays (from today onwards)
     *
     * @param int $limit
     * @param bool $includeOptional
     * @return array
     */
    public static function getUpcomingHolidays($limit = 10, $includeOptional = true)
    {
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        // Get holidays for current and next year
        $holidays = array_merge(
            self::getAllHolidays($currentYear, $includeOptional),
            self::getAllHolidays($nextYear, $includeOptional)
        );

        // Filter only upcoming holidays (today and future)
        $upcoming = array_filter($holidays, function($holiday) {
            return $holiday['date']->isFuture() || $holiday['date']->isToday();
        });

        // Limit results
        $upcoming = array_slice($upcoming, 0, $limit);

        // Format for display with additional info
        return array_map(function($holiday) {
            $daysUntil = now()->startOfDay()->diffInDays($holiday['date']);

            return [
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'days_until' => $daysUntil,
                'is_optional' => $holiday['is_optional'],
                'type' => $holiday['type'] ?? 'general',
                'is_today' => $daysUntil === 0,
                'is_tomorrow' => $daysUntil === 1,
                'date_formatted' => $holiday['date']->format('l, F j, Y'),
                'day_name' => $holiday['date']->format('l'),
                'month_name' => $holiday['date']->format('F'),
                'year' => $holiday['date']->year
            ];
        }, $upcoming);
    }

    /**
     * Get holidays for a specific month
     *
     * @param int $year
     * @param int $month
     * @param bool $includeOptional
     * @return array
     */
    public static function getHolidaysByMonth($year, $month, $includeOptional = true)
    {
        $holidays = self::getAllHolidays($year, $includeOptional);

        return array_filter($holidays, function($holiday) use ($month) {
            return $holiday['date']->month === $month;
        });
    }

    /**
     * Get holidays for a specific date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param bool $includeOptional
     * @return array
     */
    public static function getHolidaysInRange($startDate, $endDate, $includeOptional = true)
    {
        $startYear = $startDate->year;
        $endYear = $endDate->year;

        $holidays = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $holidays = array_merge($holidays, self::getAllHolidays($year, $includeOptional));
        }

        return array_filter($holidays, function($holiday) use ($startDate, $endDate) {
            return $holiday['date']->between($startDate, $endDate);
        });
    }

    /**
     * Check if a specific date is a holiday
     *
     * @param string|Carbon $date
     * @return array|false
     */
    public static function isHoliday($date)
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);
        $holidays = self::getAllHolidays($date->year, true);

        foreach ($holidays as $holiday) {
            if ($holiday['date']->format('Y-m-d') === $date->format('Y-m-d')) {
                return $holiday;
            }
        }

        return false;
    }

    /**
     * Get count of holidays in a year
     *
     * @param int|null $year
     * @param bool $includeOptional
     * @return int
     */
    public static function getHolidayCount($year = null, $includeOptional = true)
    {
        return count(self::getAllHolidays($year ?? now()->year, $includeOptional));
    }

    /**
     * Get next upcoming holiday
     *
     * @param bool $includeOptional
     * @return array|null
     */
    public static function getNextHoliday($includeOptional = true)
    {
        $upcoming = self::getUpcomingHolidays(1, $includeOptional);
        return !empty($upcoming) ? $upcoming[0] : null;
    }

    /**
     * Group holidays by month for calendar view
     *
     * @param int|null $year
     * @param bool $includeOptional
     * @return array
     */
    public static function getHolidaysByMonthGrouped($year = null, $includeOptional = true)
    {
        $year = $year ?? now()->year;
        $holidays = self::getAllHolidays($year, $includeOptional);

        $grouped = [];
        foreach ($holidays as $holiday) {
            $month = $holiday['date']->month;
            $monthName = $holiday['date']->format('F');

            if (!isset($grouped[$month])) {
                $grouped[$month] = [
                    'month_number' => $month,
                    'month_name' => $monthName,
                    'holidays' => []
                ];
            }

            $grouped[$month]['holidays'][] = $holiday;
        }

        // Sort by month number
        ksort($grouped);

        return array_values($grouped);
    }
}
