<?php

namespace App\Enums;

enum RoleName: string
{
    case SystemAdmin = 'system_admin';
    case GeneralCoordinator = 'general_coordinator';
    case MinistryCoordinator = 'ministry_coordinator';
    case FinancialCoordinator = 'financial_coordinator';
    case Member = 'member';
}
