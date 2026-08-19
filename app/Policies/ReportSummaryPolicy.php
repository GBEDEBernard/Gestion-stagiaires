<?php

namespace App\Policies;

use App\Models\ReportSummary;
use App\Models\User;

class ReportSummaryPolicy
{
    public function view(User $user, ReportSummary $summary): bool
    {
        return $user->id === $summary->user_id || $user->hasAnyRole(['admin', 'superviseur']);
    }
}
