<?php

namespace App\Enum;

enum DangerLevel: string
{
	case LOW = 'Low';
	case MEDIUM = 'Medium';
	case HIGH = 'High';
	case CRITICAL = 'Critical';
}
