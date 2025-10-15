<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proforma;
use App\Models\ProformaWork;
use App\Services\NotificationService;

class ProformaWorkController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('auth');
        $this->notificationService = $notificationService;

        $this->middleware('permission:proforma-work-create', ['only' => ['store']]);
        $this->middleware('permission:proforma-work-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:proforma-work-delete', ['only' => ['destroy']]);
    }
    public function store(Request $request)
    {

        $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'customer_id' => 'required|integer|exists:customers,id',
            'ref_no' => 'required|string|max:255',
            'date' => 'required|date',

            'type' => 'required|in:Aluminium,Finishing',
            'discount' => 'nullable|numeric|min:0',
            'vat_percentage' => 'required|numeric|min:0|max:100',
            'payment_validity' => 'required|string|max:255',
            'delivery_terms' => 'required|string|max:255',
            'works' => 'required|array',
            'works.*.name' => 'required|string|max:255',
            'works.*.unit' => 'required|string|max:50',
            'works.*.amount' => 'nullable|numeric|min:0',
            'works.*.quantity' => 'required|numeric|min:1',
            'works.*.total' => 'required|numeric|min:0',

        ]);

        $before_vat_total = array_sum(array_map(function ($work) {
            return $work['total'];
        }, $request->works));

        $vat_amount = ($before_vat_total * $request->vat_percentage) / 100;
        $after_vat_total = $before_vat_total + $vat_amount;
        if ($request->discount > $after_vat_total) {
            return redirect()->back()->with('error', 'The discount cannot be larger than the after VAT total.');
        } else
            $final_total = $after_vat_total - $request->discount;
        $proforma = Proforma::create([
            'project_id' => $request->project_id,
            'customer_id' => $request->customer_id,
            'date' => $request->date,
            'ref_no' => $request->ref_no,
            'discount' => $request->discount,
            'vat_amount' => $request->vat_percentage,
            'payment_validity' => $request->payment_validity,
            'delivery_terms' => $request->delivery_terms,
            'before_vat_total' => $before_vat_total,
            'after_vat_total' => $after_vat_total,
            'final_total' => $final_total,
            'type' => 'work',
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
        ]);

        // Create associated ProformaWork records
        foreach ($request->works as $workData) {
            ProformaWork::create([
                'proforma_id' => $proforma->id,
                'work_name' => $workData['name'],
                'work_unit' => $workData['unit'],
                'work_amount' => $workData['amount'],
                'work_quantity' => $workData['quantity'],
                'work_total' => $workData['total'],
            ]);
        }

        return redirect()->back()->with('success', 'Proforma and related works saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'ref_no' => 'required|string',
            'date' => 'required|date',
            'works.*.name' => 'required|string',
            'works.*.unit' => 'required|string',
            'works.*.amount' => 'required|numeric',
            'works.*.quantity' => 'required|numeric',
            'works.*.total' => 'nullable|numeric',

        ]);

        $proforma = Proforma::findOrFail($id);

        // Update the proforma fields
        $proforma->type = 'work';
        $proforma->ref_no = $request->ref_no;
        $proforma->date = $request->date;
        $proforma->discount = $request->discount;
        $proforma->vat_percentage = $request->vat_percentage;
        $proforma->payment_validity = $request->payment_validity;
        $proforma->delivery_terms = $request->delivery_terms;
        $proforma->created_by = auth()->id();
        $proforma->approved_by = auth()->id();
        $proforma->save();

        // Update work entries
        foreach ($request->works as $work) {
            if (isset($work['id'])) {
                // Attempt to find the existing work entry by ID
                $workEntry = ProformaWork::find($work['id']);
            }

            // If no work entry is found, create a new one
            if (empty($workEntry)) {
                $workEntry = new ProformaWork;
            }

            $workEntry->work_name = $work['name'];
            $workEntry->work_unit = $work['unit'];
            $workEntry->work_amount = $work['amount'];
            $workEntry->work_quantity = $work['quantity'];
            $workEntry->work_total = $work['total'] ?? 0;
            $workEntry->proforma_id = $proforma->id;
            $workEntry->save();
        }

        return redirect()->back()->with('success', 'Work Proforma updated successfully.');
    }
    public function destroy($id)
    {
        $proforma = Proforma::findOrFail($id);
        $proforma->works()->delete();

        $proforma->delete();

        return redirect()->back()->with('success', 'Proforma deleted successfully.');
    }
    public function approve($id)
    {
        $proforma = Proforma::with('project')->findOrFail($id);

        if ($proforma->status !== 'pending') {
            return redirect()->back()->withErrors('Proforma is already ' . $proforma->status);
        }

        $proforma->update([
            'status' => 'approved',
            'approved_by' => auth()->id()
        ]);

        // Send notification about proforma approval
        $this->notificationService->notifyProformaApproved($proforma, auth()->id());

        return redirect()->back()->with('success', 'Proforma approved successfully.');
    }

    public function reject($id)
    {
        $proforma = Proforma::with('project')->findOrFail($id);

        if ($proforma->status !== 'pending') {
            return redirect()->back()->withErrors('Proforma is already ' . $proforma->status);
        }

        $proforma->update([
            'status' => 'rejected',
            'approved_by' => auth()->id()
        ]);

        // Send notification about proforma rejection
        $this->notificationService->createForUsersWithNotificationPermission('proforma_rejected', [
            'type' => 'proforma_rejected',
            'message' => "Proforma '{$proforma->ref_no}' has been rejected",
            'data' => [
                'proforma_id' => $proforma->id,
                'proforma_ref' => $proforma->ref_no,
                'project_id' => $proforma->project_id,
                'project_name' => $proforma->project->name
            ],
            'action_url' => route('projects.showProject', $proforma->project_id),
            'created_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Proforma rejected.');
    }
}
