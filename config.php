<?php

return [

    // as per the slim3 guide
    'displayErrorDetails' => true,
    'addContentLengthHeader' => false,

    'factory' => [

    ],

    'routes' => [
        [
            'type' => 'get',
            'uri' => '/api/course-progress',
            'callback' => [\App\Controller\CourseProgressApiController::class, 'index'],
            'public' => false,
        ],
    ],
];
