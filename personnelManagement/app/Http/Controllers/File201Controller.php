<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File201;
use App\Models\OtherFile;
use App\Models\User;
use App\Models\Application;
use App\Mail\RequirementsLetterMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Traits\FileValidationTrait;
use App\Enums\ApplicationStatus;

class File201Controller extends Controller
{
    use FileValidationTrait;
    /**
     * Display the logged-in user's 201 form.
     */
    public function show()
    {
        $file201 = auth()->user()->file201;
        $otherFiles = OtherFile::where('user_id', auth()->id())->get();

        return view('users.files', compact('file201', 'otherFiles'));
    }

    /**
     * HR Staff: View an applicant/employee's requirements (for perfEval + requirementsModal + employees page).
     */
   public function showApplicantFiles($applicantId)
{
    // Authorization: HR Admin and HR Staff can view File 201 documents
    if (!in_array(auth()->user()->role, ['hrAdmin', 'hrStaff'])) {
        return response()->json([
            'error' => 'Unauthorized. Only HR personnel can view File 201 documents.'
        ], 403);
    }

    // ✅ SECURITY FIX: Verify the user exists and has at least one application
    $user = User::find($applicantId);

    if (!$user) {
        return response()->json([
            'error' => 'User not found.'
        ], 404);
    }

    // Check if user has any application (regardless of status, to support both applicants and employees)
    $hasApplication = Application::where('user_id', $applicantId)->exists();

    if (!$hasApplication) {
        return response()->json([
            'error' => 'No application found for this user.'
        ], 403);
    }

    $file201 = File201::where('user_id', $applicantId)->first();
    $otherFiles = OtherFile::where('user_id', $applicantId)->get();

    // ✅ SECURITY FIX: Mask sensitive government ID numbers
    if ($file201) {
        $file201->sss_number = $file201->sss_number ? '****' . substr($file201->sss_number, -4) : null;
        $file201->philhealth_number = $file201->philhealth_number ? '****' . substr($file201->philhealth_number, -4) : null;
        $file201->tin_id_number = $file201->tin_id_number ? '****' . substr($file201->tin_id_number, -4) : null;
        $file201->pagibig_number = $file201->pagibig_number ? '****' . substr($file201->pagibig_number, -4) : null;
    }

    return response()->json([
        'file201' => $file201,
        'otherFiles' => $otherFiles
    ]);
}

 /**
     * ✅ HR Staff: Send email listing missing requirements for applicant
     */
    public function sendMissingRequirements($applicantId)
    {
        $user = User::findOrFail($applicantId);

        // List of all required documents
        $requiredDocs = [
            'Barangay Clearance',
            'NBI Clearance',
            'Police Clearance',
            'Medical Certificate',
            'Birth Certificate'
        ];

        // Fetch File201 + OtherFiles
        $file201 = File201::where('user_id', $applicantId)->first();
        $otherFiles = OtherFile::where('user_id', $applicantId)->get();

        // Determine which required docs are missing
        $uploadedDocTypes = $otherFiles->pluck('type')->toArray();
        $missingDocs = collect($requiredDocs)->filter(fn($doc) => !in_array($doc, $uploadedDocTypes))->values();

        // You can also check if any key IDs are missing in File201
        $missingFile201 = collect([
            'SSS Number' => empty($file201?->sss_number),
            'PhilHealth Number' => empty($file201?->philhealth_number),
            'Pag-IBIG Number' => empty($file201?->pagibig_number),
            'TIN ID Number' => empty($file201?->tin_id_number),
        ])->filter(fn($missing) => $missing)->keys();

        // Merge both missing lists
        $missingRequirements = $missingFile201->merge($missingDocs)->values();

        if ($missingRequirements->isEmpty()) {
            return response()->json(['message' => 'No missing requirements.'], 200);
        }

       try {
        Mail::to($user->email)->send(new RequirementsLetterMail($user, $missingRequirements));

        // Update notification timestamp and increment reminder count
        // Each send replaces the previous notification with updated info
        $user->requirements_notified_at = now();
        $user->requirements_reminder_count = ($user->requirements_reminder_count ?? 0) + 1;
        $user->save();

        $reminderText = $user->requirements_reminder_count === 1
            ? '1st notification'
            : ($user->requirements_reminder_count === 2 ? '2nd reminder' : $user->requirements_reminder_count . 'th reminder');

        return response()->json([
            'message' => 'Requirements email sent successfully.',
            'notified_at' => $user->requirements_notified_at->format('M d, Y h:i A'),
            'reminder_count' => $user->requirements_reminder_count,
            'reminder_text' => $reminderText
        ], 200);
    } catch (\Throwable $e) {
        // Log full error and return message for debugging
        Log::error('sendMissingRequirements error: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString(),
            'user_id' => $applicantId,
        ]);

        return response()->json([
            'message' => 'Mail error: '.$e->getMessage()
        ], 500);
    }
}

    /**
     * Store or update the 201 file for logged-in user.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'sss_number' => 'nullable|string|max:50',
        'philhealth_number' => 'nullable|string|max:50',
        'pagibig_number' => 'nullable|string|max:50',
        'tin_id_number' => 'nullable|string|max:50',
        'licenses' => 'nullable|array',
        'licenses.*.name' => 'nullable|string|max:255',
        'licenses.*.number' => 'nullable|string|max:255',
        'licenses.*.date' => 'nullable|date',

        // Government ID uploads
        'sss_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:4096',
        'philhealth_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:4096',
        'pagibig_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:4096',
        'tin_file' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:4096',

        // Additional documents
        'additional_documents' => 'nullable|array',
        'additional_documents.*.type' => 'nullable|string|max:255',
        'additional_documents.*.file' => 'nullable|file|mimes:pdf|max:2048',
    ]);

    // Create or update File201 record
    $file201 = File201::updateOrCreate(
        ['user_id' => auth()->id()],
        [
            'sss_number' => $validated['sss_number'] ?? null,
            'philhealth_number' => $validated['philhealth_number'] ?? null,
            'pagibig_number' => $validated['pagibig_number'] ?? null,
            'tin_id_number' => $validated['tin_id_number'] ?? null,
            'licenses' => $validated['licenses'] ?? [],
        ]
    );

    // === GOVERNMENT ID FILE UPLOADS === //
    $govFiles = [
        'sss_file' => 'sss_file_path',
        'philhealth_file' => 'philhealth_file_path',
        'pagibig_file' => 'pagibig_file_path',
        'tin_file' => 'tin_file_path',
    ];

    foreach ($govFiles as $inputName => $columnName) {
        if ($request->hasFile($inputName)) {

            $uploadedFile = $request->file($inputName);

            $uploadResult = $this->validateAndStoreFile(
                $uploadedFile,
                "file201/$inputName",
                ['pdf', 'png', 'jpg', 'jpeg'],
                'public'
            );

            if (!$uploadResult['success']) {
                return redirect()->back()->withErrors([$inputName => $uploadResult['error']]);
            }

            if ($file201->$columnName && \Storage::disk('public')->exists($file201->$columnName)) {
                \Storage::disk('public')->delete($file201->$columnName);
            }

            $file201->update([
                $columnName => $uploadResult['path']
            ]);
        }
    }

    // Additional documents logic (unchanged)
    if ($request->has('additional_documents')) {
        foreach ($request->additional_documents as $index => $doc) {
            if (isset($doc['file']) && $request->file("additional_documents.$index.file")) {
                $uploadedFile = $request->file("additional_documents.$index.file");

                $uploadResult = $this->validateAndStoreFile(
                    $uploadedFile,
                    'other_documents',
                    ['pdf'],
                    'public'
                );

                if (!$uploadResult['success']) {
                    return redirect()->back()->withErrors(['file' => $uploadResult['error']]);
                }

                $existingFile = OtherFile::where('user_id', auth()->id())
                    ->where('type', $doc['type'])
                    ->first();

                if ($existingFile) {
                    if ($existingFile->file_path && \Storage::disk('public')->exists($existingFile->file_path)) {
                        \Storage::disk('public')->delete($existingFile->file_path);
                    }

                    $existingFile->update([
                        'file_path' => $uploadResult['path'],
                    ]);
                } else {
                    OtherFile::create([
                        'user_id'   => auth()->id(),
                        'type'      => $doc['type'],
                        'file_path' => $uploadResult['path'],
                    ]);
                }
            }
        }
    }

    return redirect()->back()->with('success', '201 file saved successfully!');
}
    /** 
     * Delete an uploaded document.
     */
    public function destroy($id)
    {
        $file = OtherFile::where('id', $id)->where('user_id', auth()->id())->first();

        if ($file) {
            if (\Storage::disk('public')->exists($file->file_path)) {
                \Storage::disk('public')->delete($file->file_path);
            }

            $file->delete();

            return redirect()->back()->with('success', 'Document deleted successfully.');
        }

        return redirect()->back()->with('error', 'File not found or unauthorized.');
    }
}
