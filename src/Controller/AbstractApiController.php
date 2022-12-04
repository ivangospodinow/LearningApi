<?php
namespace App\Controller;

use stdClass;

abstract class AbstractApiController
{
    protected function validateSchemaWithErrorReponse(array $params, string $schema)
    {
        $validator = new \JsonSchema\Validator;
        $objectToValidate = empty($params) ? new stdClass : json_decode(json_encode($params));

        $validator->validate(
            $objectToValidate,
            (object) [
                '$ref' => 'file://' . realpath('schema/' . $schema),
            ]
        );

        if ($validator->isValid()) {
            return false;
        }

        return [
            'success' => false,
            'errors' => $validator->getErrors(),
        ];
    }
}
