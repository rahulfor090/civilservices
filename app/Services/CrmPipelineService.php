<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CrmPipelineService
{
    /**
     * Transition lead stage and log activity.
     */
    public function transitionStage(
        Lead $lead,
        string $toStage,
        ?string $notes = null,
        ?Carbon $followUpAt = null,
        ?User $actor = null
    ): void {
        DB::transaction(function () use ($lead, $toStage, $notes, $followUpAt, $actor) {
            $fromStage = $lead->status;

            $lead->update([
                'status' => $toStage,
                'follow_up_at' => $followUpAt ?: $lead->follow_up_at,
            ]);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $actor ? $actor->id : auth()->id(),
                'type' => 'status_change',
                'outcome' => "Stage changed from {$fromStage} to {$toStage}",
                'notes' => $notes,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Assign lead to a counsellor.
     */
    public function assignLead(Lead $lead, User $counsellor, ?User $actor = null): void
    {
        DB::transaction(function () use ($lead, $counsellor, $actor) {
            $lead->update(['assigned_to' => $counsellor->id]);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $actor ? $actor->id : auth()->id(),
                'type' => 'assignment',
                'outcome' => "Assigned to {$counsellor->name}",
                'notes' => 'Lead routed via CRM pipeline.',
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Add a call or counselling note.
     */
    public function recordActivity(Lead $lead, string $type, ?string $outcome, ?string $notes, ?User $actor = null): LeadActivity
    {
        return LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $actor ? $actor->id : auth()->id(),
            'type' => $type,
            'outcome' => $outcome,
            'notes' => $notes,
            'created_at' => now(),
        ]);
    }
}
