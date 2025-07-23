<?php

namespace App\Enum;

enum AgentStatus: string
{
	case ON_MISSION = 'On Mission';
	case RETIRED = 'Retired';
	case KILLED_IN_ACTION = 'Killed In Action';
	case AVAILABLE = 'Available';
}
