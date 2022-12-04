<?php

namespace App\Service;

use DateTime;

/**
 * Some determinations:
 * - Maximum learning time per day is 86400 seconds (1 day).
 * - Ideal means average learning per second. This way required learning for not full days will be proportinaly less (prorated).
 * - It should be ok to have progress on a course that is not yet started. May be the periods were shifted for some reason.
 * - If overdue needed daily learning become maximum daily learning instead of ideal.
 * - if due time is 23:59:59 then a second will be added for more clear calculations. It will be assumed as one day of 86400 seconds.
 **/
final class CourseProgressCalculator
{
    const STATUS_ON_TRACK = 'on track';
    const STATUS_NOT_ON_TRACK = 'not on track';
    const STATUS_OVERDUE = 'overdue';

    const SECONDS_DAY = 86400;
    const MAX_LEARNING_PER_DAY = self::SECONDS_DAY;

    /**
     * @var integer
     */
    protected $courseDuration;

    /**
     * @var integer
     */
    protected $currentProgress;

    /**
     * @var DateTime
     */
    protected $assignmentDate;

    /**
     * @var DateTime
     */
    protected $dueDate;

    /**
     * @var DateTime
     */
    protected $currentDate;

    /**
     * @param integer $courseDuration
     * @param integer $currentProgress
     * @param DateTime $assignmentDate
     * @param DateTime $dueDate
     * @param DateTime|null $currentDate
     */
    public function __construct(int $courseDuration, int $currentProgress, DateTime $assignmentDate, DateTime $dueDate, DateTime $currentDate = null)
    {
        if ($currentProgress > 100 || $currentProgress < 0) {
            throw new \Exception('Current progress can only be a number between 0 and 100');
        }

        $timeZone = $assignmentDate->getTimezone()->getName();
        if ($timeZone != $dueDate->getTimezone()->getName() || ($currentDate && $timeZone !== $currentDate->getTimezone()->getName())) {
            throw new \Exception('Related dates timezones mismatch. All timezones must be the same.');
        }

        if ($assignmentDate >= $dueDate) {
            throw new \Exception('Assignment date can not be bigger than due date.');
        }

        if ($dueDate->getTimestamp() - $assignmentDate->getTimestamp() < $courseDuration) {
            throw new \Exception('Course duration can not be bigger that the allocated time for this course.');
        }

        $this->courseDuration = $courseDuration;
        $this->currentProgress = $currentProgress;

        // break the references to avoid unexpected side effects
        $this->assignmentDate = clone $assignmentDate;
        $this->dueDate = clone $dueDate;
        $this->currentDate = $currentDate ? clone $currentDate : new DateTime('now', $assignmentDate->getTimezone());

        // assume a full day to the second for more clear calculations
        if ($this->dueDate->format('H:i:s') === '23:59:59') {
            $this->dueDate->modify('+1 second');
        }
    }

    public function calculateProgress()
    {
        // Early bail conditions
        // course not started, you are on track
        if ($this->currentDate < $this->assignmentDate) {
            return [
                'progress_status' => self::STATUS_ON_TRACK,
                'expected_progress' => 0,
                'needed_daily_learning_time' => 0,
            ];
        }

        // Course due date has ended => finished 100% or overdue
        if ($this->currentDate > $this->dueDate) {
            return [
                'progress_status' => $this->currentProgress === 100 ? self::STATUS_ON_TRACK : self::STATUS_OVERDUE,
                'expected_progress' => 100,
                // if it is overdue and progress is not 100, rest of the lesson is required but not more that a day
                'needed_daily_learning_time' => $this->currentProgress === 100 ? 0 : min($this->getRemainingCourseDuration(), self::MAX_LEARNING_PER_DAY),
            ];
        }

        $assignmentDuration = $this->dueDate->getTimestamp() - $this->assignmentDate->getTimestamp();
        $timeProgressOnCourse = $this->currentDate->getTimestamp() - $this->assignmentDate->getTimestamp();
        $expectedCourseProgressPercent = (int) round($timeProgressOnCourse / $assignmentDuration * 100);

        $progressStatus = $this->getStatus($this->currentProgress, $expectedCourseProgressPercent);

        $neededDailyLearningTime = $this->calcualteDailyLearningAverage(
            $this->currentDate,
            $this->dueDate,
            $this->getRemainingCourseDuration()
        );

        return [
            'progress_status' => $progressStatus,
            'expected_progress' => $expectedCourseProgressPercent,
            'needed_daily_learning_time' => min($neededDailyLearningTime, self::MAX_LEARNING_PER_DAY),
        ];
    }

    protected function calcualteDailyLearningAverage(DateTime $periodStart, DateTime $periodEnd, int $courseDuration): int
    {
        // in case the period is 1 day, then  what is left of the coruse is required.
        if ($periodEnd->getTimestamp() - $periodStart->getTimestamp() <= self::SECONDS_DAY) {
            return $courseDuration;
        }

        $assignmentDuration = $periodEnd->getTimestamp() - $periodStart->getTimestamp();
        $averageLearningPerSecond = $courseDuration / $assignmentDuration;
        return (int) ceil($averageLearningPerSecond * self::SECONDS_DAY);
    }

    protected function getStatus(int $currentProgress, int $expectedProgress): string
    {
        return $currentProgress >= $expectedProgress ? self::STATUS_ON_TRACK : self::STATUS_NOT_ON_TRACK;
    }

    protected function getRemainingCourseDuration()
    {
        return (int) ceil($this->courseDuration * (100 - $this->currentProgress) / 100);
    }
}
