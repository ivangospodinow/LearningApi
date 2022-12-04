<?php

namespace Test\Service\CourseProgressCalculator;

use App\Service\CourseProgressCalculator;
use DateTime;
use DateTimeZone;
use Test\AbstractTestCase;

final class CourseProgressCalculatorTest extends AbstractTestCase
{

    public function testConstructorCurrentProgressErrorCase1()
    {
        $this->expectExceptionMessage('Current progress can only be a number between 0 and 100');
        $service = new CourseProgressCalculator(0, 101, new DateTime(), new DateTime());
    }

    public function testConstructorCurrentProgressErrorCase2()
    {
        $this->expectExceptionMessage('Current progress can only be a number between 0 and 100');
        $service = new CourseProgressCalculator(0, -1, new DateTime(), new DateTime());
    }

    public function testConstructorTimezoneError()
    {
        $this->expectExceptionMessage('Related dates timezones mismatch. All timezones must be the same.');
        $service = new CourseProgressCalculator(0, 0, new DateTime('now', new DateTimeZone('Etc/GMT+1')), new DateTime('now', new DateTimeZone('Etc/GMT+2')));
    }

    public function testConstructorTimezoneWithCurrentDateError()
    {
        $this->expectExceptionMessage('Related dates timezones mismatch. All timezones must be the same.');
        $service = new CourseProgressCalculator(0, 0, new DateTime('now', new DateTimeZone('Etc/GMT+1')), new DateTime('now', new DateTimeZone('Etc/GMT+1')), new DateTime('now', new DateTimeZone('Etc/GMT+2')));
    }

    public function testConstructorAssigmentDateWrongIntervalError()
    {
        $this->expectExceptionMessage('Assignment date can not be bigger than due date.');
        $service = new CourseProgressCalculator(0, 0, new DateTime('2022-12-01'), new DateTime('2022-11-01'));
    }

    public function testConstructorCourseDurationError()
    {
        $this->expectExceptionMessage('Course duration can not be bigger that the allocated time for this course.');
        $service = new CourseProgressCalculator(strtotime('2 days'), 0, new DateTime('2022-12-01'), new DateTime('2022-12-02'));
    }

    public function testConstructorDueDateFormat()
    {
        $service = new CourseProgressCalculator(60, 0, new DateTime('2022-12-01'), new DateTime('2022-12-02 23:59:59'));
        $this->assertSame('2022-12-03 00:00:00', $this->getPrivateProp($service, 'dueDate')->format('Y-m-d H:i:s'));
    }
}
