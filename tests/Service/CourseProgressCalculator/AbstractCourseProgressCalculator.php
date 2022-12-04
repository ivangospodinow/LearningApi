<?php

namespace Test\Service\CourseProgressCalculator;

use App\Controller\CourseProgressApiController;
use App\Service\CourseProgressCalculator;
use DateTime;
use Test\AbstractTestCase;

abstract class AbstractCourseProgressCalculator extends AbstractTestCase
{
    const ZERO_SECONDS = 0;
    const SECOND = 1;
    const MINUTE = self::SECOND * 60;
    const HOUR = self::MINUTE * 60;
    const DAY = self::HOUR * 24;

    /**
     * @var CourseProgressApiController
     */
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new CourseProgressApiController();
    }

    public function createService()
    {
        $params = func_get_args();
        $params[0] = (int) $params[0];
        $params[1] = (int) $params[1];
        $params[2] = new DateTime($params[2]);
        $params[3] = new DateTime($params[3]);
        if (isset($params[4])) {
            $params[4] = new DateTime($params[4]);
        }
        return new CourseProgressCalculator(...$params);
    }

    public function doTestCases(array $cases)
    {
        foreach ($cases as $case) {
            $calculateProgress = $this->createService(...$case['params'])->calculateProgress();
            $this->assertSame($case['expected'], $calculateProgress, get_class($this) . ':: ' . $case['name']);

            $response = $this->controller->index([
                'courseDuration' => (int) $case['params'][0],
                'currentProgress' => (int) $case['params'][1],
                'assignmentDate' => $case['params'][2],
                'dueDate' => $case['params'][3],
                'currentDate' => $case['params'][4],
            ]);
            $this->assertSame($case['expected'], $response, get_class($this) . ':: ' . $case['name']);
        }
    }
}
