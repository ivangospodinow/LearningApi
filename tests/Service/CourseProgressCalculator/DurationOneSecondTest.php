<?php

namespace Test\Service\CourseProgressCalculator;

final class DurationOneSecondTest extends AbstractCourseProgressCalculator
{
    public function testOneSecondCases()
    {
        $this->doTestCases([
            [
                'name' => 'Test 1 second duration, if it is not 100% needed daily avg will be always 1',
                'params' => [self::SECOND, '5%', '2022-12-01', '2022-12-05', '2022-12-04'],
                'expected' => ['progress_status' => 'not on track', 'expected_progress' => 75, 'needed_daily_learning_time' => self::SECOND],
            ],
            [
                'name' => 'Test 1 second duration, if it is not 100% needed daily avg will be always 1',
                'params' => [self::SECOND, '95%', '2022-12-01', '2022-12-05', '2022-12-04'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 75, 'needed_daily_learning_time' => self::SECOND],
            ],
            [
                'name' => 'Test 1 second duration, course is done, needed should be 0',
                'params' => [self::SECOND, '100%', '2022-12-01', '2022-12-05', '2022-12-04'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 75, 'needed_daily_learning_time' => self::ZERO_SECONDS],
            ],
            [
                'name' => 'Test 1 second duration, course not started',
                'params' => [self::SECOND, '100%', '2022-12-01', '2022-12-05', '2022-11-15'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 0, 'needed_daily_learning_time' => self::ZERO_SECONDS],
            ],
            [
                'name' => 'Test 1 second duration, course not started',
                'params' => [self::SECOND, '100%', '2022-12-01', '2022-12-05', '2022-11-15'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 0, 'needed_daily_learning_time' => self::ZERO_SECONDS],
            ],
            [
                'name' => 'Test 1 second duration, course ended on track',
                'params' => [self::SECOND, '100%', '2022-12-01', '2022-12-05', '2022-12-15'],
                'expected' => ['progress_status' => 'on track', 'expected_progress' => 100, 'needed_daily_learning_time' => self::ZERO_SECONDS],
            ],
            [
                'name' => 'Test 1 second duration, course ended overdue 0%',
                'params' => [self::SECOND, '0%', '2022-12-01', '2022-12-05', '2022-12-15'],
                'expected' => ['progress_status' => 'overdue', 'expected_progress' => 100, 'needed_daily_learning_time' => self::SECOND],
            ],
            [
                'name' => 'Test 1 second duration, course ended overdue 99%',
                'params' => [self::SECOND, '99%', '2022-12-01', '2022-12-05', '2022-12-15'],
                'expected' => ['progress_status' => 'overdue', 'expected_progress' => 100, 'needed_daily_learning_time' => self::SECOND],
            ],
        ]);
    }
}
