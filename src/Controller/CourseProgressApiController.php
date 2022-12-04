<?php
namespace App\Controller;

use App\Service\CourseProgressCalculator;
use DateTime;

class CourseProgressApiController extends AbstractApiController
{
    public function index(array $params)
    {
        if (isset($params['courseDuration']) && is_numeric($params['courseDuration'])) {
            $params['courseDuration'] = (int) $params['courseDuration'];
        }
        if (isset($params['currentProgress']) && is_numeric($params['currentProgress'])) {
            $params['currentProgress'] = (int) $params['currentProgress'];
        }

        if ($errors = $this->validateSchemaWithErrorReponse($params, 'CourseProgress.json')) {
            return $errors;
        }

        try {
            $service = new CourseProgressCalculator(
                $params['courseDuration'],
                $params['currentProgress'],
                new DateTime($params['assignmentDate']),
                new DateTime($params['dueDate']),
                isset($params['currentDate']) ? new DateTime($params['currentDate']) : null
            );
            return $service->calculateProgress();
        } catch (\Exception $e) {
            return [
                'success' => false,
                'errors' => [
                    [
                        'property' => 'course calculator',
                        'message' => $e->getMessage(),
                    ],
                ],
            ];
        }
    }
}
