<?php

namespace App\Validator\Constraints\Mission;

use App\Entity\Mission;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MissionStatusResultConstraintValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint): void
	{
		if (!$constraint instanceof MissionStatusResultConstraint) {
			throw new UnexpectedTypeException($constraint, MissionStatusResultConstraint::class);
		}

		if (!$value instanceof Mission) {
			return;
		}

		if ($value->getStatus() !== null && $value->getMissionResult() === null) {
			$this->context->buildViolation($constraint->message)
				->addViolation();
		}
	}
}
