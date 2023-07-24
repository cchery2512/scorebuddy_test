<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validation;
use Carbon\Carbon;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class NextDrawDayRequest
{

    private $params;

    public function __construct(ContainerBagInterface $params)
    {
        $this->params = $params;
    }

    public function validated(Request $request): array
    {

        $requestData['date'] = $request->query->has('date') ? str_replace(' ', '+', $request->get('date')) : Carbon::now()->toATOMString();

        $this->validateDateFormat($requestData['date']);
        
        return (array)$requestData;
    }

    private function validateDateFormat(?string $date): void
    {
        $constraints = new Assert\Collection([
            'date' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Regex([
                    'pattern' => $this->params->get('app.atom_regex'),
                    'message' => 'Invalid DateTime format. Try again. '
                ]),
            ],
        ]);

        $this->validate($date, $constraints, 'date');
    }

    private function validate($value, Assert\Collection $constraints, string $field): void
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate([$field => $value], $constraints);

        if (count($violations) > 0) {
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }

            throw new \InvalidArgumentException(implode(' ', $messages));
        }
    }
}
