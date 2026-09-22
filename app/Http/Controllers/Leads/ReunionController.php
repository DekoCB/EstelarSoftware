<?php

namespace App\Http\Controllers\Leads;

use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreReunionRequest;
use App\Http\Requests\Leads\UpdateReunionRequest;
use App\Models\Lead;
use App\Models\LeadMeeting;

class ReunionController extends Controller
{
    public function store(StoreReunionRequest $request, Lead $lead)
    {
        $lead->meetings()->create(
            $request->validated() + ['created_by' => auth()->id()]
        );

        return back()->with('success', 'Reunión agendada correctamente.');
    }

    public function update(UpdateReunionRequest $request, Lead $lead, LeadMeeting $reunion)
    {
        $reunion->update($request->validated());

        return back()->with('success', 'Reunión actualizada correctamente.');
    }

    public function destroy(Lead $lead, LeadMeeting $reunion)
    {
        $reunion->delete();

        return back()->with('success', 'Reunión eliminada.');
    }
}
