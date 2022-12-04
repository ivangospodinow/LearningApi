<?php

namespace Test\Service\CourseProgressCalculator;

use App\Controller\CourseProgressApiController;
use Test\AbstractTestCase;

final class CourseProgressApiControllerTest extends AbstractTestCase
{

    /**
     * @var CourseProgressApiController
     */
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = new CourseProgressApiController();
    }

    public function testInvalidData()
    {
        $result = $this->controller->index([]);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame('The property courseDuration is required', $result['errors'][0]['message']);
        $this->assertSame('The property currentProgress is required', $result['errors'][1]['message']);
        $this->assertSame('The property assignmentDate is required', $result['errors'][2]['message']);
        $this->assertSame('The property dueDate is required', $result['errors'][3]['message']);
    }

    public function testInvalidCourseDuration()
    {
        $result = $this->controller->index(['courseDuration' => 'test']);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame('String value found, but an integer is required', $result['errors'][0]['message']);
    }

    public function testInvalidCurrentProgress()
    {
        $result = $this->controller->index(['currentProgress' => 'test']);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
        $this->assertSame('String value found, but an integer is required', $result['errors'][1]['message']);
    }

    public function testInvalidAssignmentDate()
    {
        $result = $this->controller->index([
            'courseDuration' => 1,
            'currentProgress' => 1,
            'assignmentDate' => 'test',
            'dueDate' => '2022-10-12',
        ]);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
    }

    public function testInvalidDueDate()
    {
        $result = $this->controller->index([
            'courseDuration' => 1,
            'currentProgress' => 1,
            'assignmentDate' => '2022-10-12',
            'dueDate' => 'test',
        ]);
        $this->assertFalse($result['success']);
        $this->assertNotEmpty($result['errors']);
    }
}
