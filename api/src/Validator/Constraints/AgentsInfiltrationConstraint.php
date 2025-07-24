<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class AgentsInfiltrationConstraint extends Constraint
{
	public string $message = 'Agent "{{ agent }}" must be infiltrated in the mission country "{{ country }}".';

	public function getTargets(): string
	{
		return self::CLASS_CONSTRAINT;
	}
}
