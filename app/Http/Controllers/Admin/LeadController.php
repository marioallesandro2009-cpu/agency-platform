<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::latestFirst();

        if ($request->filled("status")) {
            $query->status($request->input("status"));
        }

        return Inertia::render("Admin/Leads/Index", [
            "leads" => $query->get(),
            "filters" => $request->only("status"),
            "counts" => [
                "new" => Lead::status("new")->count(),
                "contacted" => Lead::status("contacted")->count(),
                "in_progress" => Lead::status("in_progress")->count(),
                "won" => Lead::status("won")->count(),
                "lost" => Lead::status("lost")->count(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Lead $lead)
    {
        $data = $request->validate([
            "status" => "required|in:new,contacted,in_progress,won,lost",
            "admin_notes" => "nullable|string|max:2000",
        ]);

        $lead->update($data);

        return back()->with("success", "Status lead berhasil diperbarui.");
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();

        return back()->with("success", "Lead berhasil dihapus.");
    }
}
