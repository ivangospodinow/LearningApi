<?php

namespace Test\Service\CourseProgressCalculator;

final class MultiDayPeriodAndCourseTest extends AbstractCourseProgressCalculator
{

    public function testMultiDayPeriodAndCourseCases()
    {
        $this->doTestCases([
            [
                'name' => 'Test a day course for a month at start',
                'params' => [self::DAY, '0%', '2022-12-01 00:00:00', '2022-12-31 23:59:59', '2022-12-01 00:00:00'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 0, 'needed_daily_learning_time' => self::SECOND * 2788],
            ],
            [
                'name' => 'Test a day course for a month not on track, increase needed daily learning time',
                'params' => [self::DAY, '25%', '2022-12-01 00:00:00', '2022-12-31 23:59:59', '2022-12-10 00:00:00'],
                'expected' => ['progress_status' => 'not on track', 'expected_progress' => 29, 'needed_daily_learning_time' => self::SECOND * 2946],
            ],
            [
                'name' => 'Test a day course for a month not on track, last day with 0% done, whole days needs to be done',
                'params' => [self::DAY, '0%', '2022-12-01 00:00:00', '2022-12-31 23:59:59', '2022-12-31 12:00:00'],
                'expected' => ['progress_status' => 'not on track', 'expected_progress' => 98, 'needed_daily_learning_time' => self::DAY],
            ],
            [
                'name' => 'Test a day course for a month on track, half way point',
                'params' => [self::DAY, '50%', '2022-12-01 00:00:00', '2022-12-31 23:59:59', '2022-12-16 12:00:00'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 50, 'needed_daily_learning_time' => self::SECOND * 2788],
            ],
        ]);
    }
}
