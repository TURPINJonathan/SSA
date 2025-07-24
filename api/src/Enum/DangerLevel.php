<?php

namespace App\Enum;

enum DangerLevel: string
{
	case LOW = 'Low';
	case MEDIUM = 'Medium';
	case HIGH = 'High';
	case CRITICAL = 'Critical';

	public function getValue(): int
	{
		return match ($this) {
			self::LOW => 1,
			self::MEDIUM => 2,
			self::HIGH => 3,
			self::CRITICAL => 4,
		};
	}

	public function isHigherThan(DangerLevel $other): bool
	{
		return $this->getValue() > $other->getValue();
	}

	public static function getHighest(array $dangerLevels): self
	{
		if (empty($dangerLevels)) {
			return self::LOW;
		}

		$highest = self::LOW;
		foreach ($dangerLevels as $level) {
			if ($level->isHigherThan($highest)) {
				$highest = $level;
			}
		}

		return $highest;
	}
}
