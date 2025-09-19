<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File201;
use App\Models\OtherFile;

class File201Controller extends Controller
{
    /**
     * Display the logged-in user's 201 form.
     */
    public function show()
    {
        $file201 = auth()->user()->file201;
        $otherFiles = OtherFile::where('user_id', auth()->id())->get();

        $view = auth()->user()->role === 'employee'
            ? 'employee.files'
            : 'applicant.files';

        return view($view, compact('file201', 'otherFiles'));
    }

    /**
     * HR Staff: View an applicant's requirements (for perfEval + requirementsModal).
     */
   public function showApplicantFiles($applicantId)
{
    $file201 = File201::where('user_id', $applicantId)->first();
    $otherFiles = OtherFile::where('user_id', $applicantId)->get();

    return response()->json([
        'file201' => $file201,
        'otherFiles' => $otherFiles
    ]);
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
            'additional_documents' => 'nullable|array',
            'additional_documents.*.type' => 'nullable|string|max:255',
            'additional_documents.*.file' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        File201::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'sss_number' => $validated['sss_number'] ?? null,
                'philhealth_number' => $validated['philhealth_number'] ?? null,
                'pagibig_number' => $validated['pagibig_number'] ?? null,
                'tin_id_number' => $validated['tin_id_number'] ?? null,
                'licenses' => $validated['licenses'] ?? [],
            ]
        );

        if ($request->has('additional_documents')) {
            foreach ($request->additional_documents as $index => $doc) {
                if (isset($doc['file']) && $request->file("additional_documents.$index.file")) {
                    $uploadedFile = $request->file("additional_documents.$index.file");
                    $filePath = $uploadedFile->store('other_documents', 'public');

                    // ✅ Prevent duplicate type uploads
                    $alreadyExists = OtherFile::where('user_id', auth()->id())
                        ->where('type', $doc['type'])
                        ->exists();

                    if (!$alreadyExists) {
                        OtherFile::create([
                            'user_id'   => auth()->id(),
                            'type'      => $doc['type'],
                            'file_path' => $filePath,
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
