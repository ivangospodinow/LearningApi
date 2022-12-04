<?php

namespace Test\Service\CourseProgressCalculator;

final class PeriodLessThanADayTest extends AbstractCourseProgressCalculator
{
    public function testPeriodLessThanADayCases()
    {
        $this->doTestCases([
            [
                'name' => 'Test course period less than a day not on track, all material is required',
                'params' => [self::HOUR * 4, '5%', '2022-12-01 12:00:00', '2022-12-01 22:00:00', '2022-12-01 20:00:00'],
                'expected' => ['progress_status' => 'not on track', 'expected_progress' => 80, 'needed_daily_learning_time' => self::SECOND * 13680],
            ],
            [
                'name' => 'Test course period less than a day on track, all material is required',
                'params' => [self::HOUR * 4, '90%', '2022-12-01 12:00:00', '2022-12-01 22:00:00', '2022-12-01 20:00:00'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 80, 'needed_daily_learning_time' => self::SECOND * 1440],
            ],
            [
                'name' => 'Test course period less than a day on track and 100% progress',
                'params' => [self::HOUR * 4, '100%', '2022-12-01 12:00:00', '2022-12-01 22:00:00', '2022-12-01 20:00:00'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 80, 'needed_daily_learning_time' => self::ZERO_SECONDS],
            ],
            [
                'name' => 'Test course period less than a day and overdue',
                'params' => [self::HOUR * 4, '75%', '2022-12-01 12:00:00', '2022-12-01 22:00:00', '2022-12-02 20:00:00'],
                'expected' => ['progress_status' => 'overdue', 'expected_progress' => 100, 'needed_daily_learning_time' => self::HOUR],
            ],
            [
                'name' => 'Test course period less than a day and not started',
                'params' => [self::HOUR * 4, '75%', '2022-12-01 12:00:00', '2022-12-01 22:00:00', '2022-12-01 00:00:00'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 0, 'needed_daily_learning_time' => self::ZERO_SECONDS],
            ],
        ]);
    }
}
