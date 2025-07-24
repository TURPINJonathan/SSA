<?php

namespace App\Validator\Constraints\Mission;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class MissionStatusResultConstraint extends Constraint
{
	public string $message = 'A mission with a status must have a mission result.';

	public function getTargets(): string
	{
		return self::CLASS_CONSTRAINT;
	}
}
