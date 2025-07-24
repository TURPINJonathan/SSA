<?php

namespace App\Validator\Constraints;

use App\Entity\Mission;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AgentsInfiltrationConstraintValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (!$value instanceof Mission) {
			return;
		}

		foreach ($value->getAgents() as $agent) {
			if ($agent->getCountryInfiltrated() !== $value->getCountry()) {
				$this->context->buildViolation($constraint->message)
					->setParameter('{{ agent }}', $agent->getCodename())
					->setParameter('{{ country }}', $value->getCountry()?->getName() ?? 'Unknown')
					->atPath('agents')
					->addViolation();
			}
		}
	}
}
