<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File201;
use App\Models\OtherFile;

class File201Controller extends Controller
{
    /**
     * Display the 201 form.
     */
    public function show()
    {
        $file201 = auth()->user()->file201;
        $otherFiles = \App\Models\OtherFile::where('user_id', auth()->id())->get(); // fully qualified

        $view = auth()->user()->role === 'employee'
            ? 'employee.files'
            : 'applicant.files';

        return view($view, compact('file201', 'otherFiles'));
    }

    /**
     * Store or update the 201 file.
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
            'additional_documents.*.type' => 'required|string|max:255',
            'additional_documents.*.file' => 'required|file|mimes:pdf|max:2048',
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

        OtherFile::where('user_id', auth()->id())->delete();

        if ($request->has('additional_documents')) {
        foreach ($request->additional_documents as $index => $doc) {
            if (isset($doc['file']) && $request->file("additional_documents.$index.file")) {
                $uploadedFile = $request->file("additional_documents.$index.file");
                $filePath = $uploadedFile->store('other_documents', 'public');

                OtherFile::create([
                    'user_id' => auth()->id(),
                    'type' => $doc['type'],
                    'file_path' => $filePath,
                ]);
            }
        }
    }

        return redirect()->back()->with('success', '201 file saved successfully!');
    }

    public function destroy($id)
    {
        // Try to delete an OtherFile by ID and current user
        $file = OtherFile::where('id', $id)->where('user_id', auth()->id())->first();

        if ($file) {
            if (\Storage::disk('public')->exists($file->file_path)) {
                \Storage::disk('public')->delete($file->file_path);
            }

            $file->delete();

            return redirect()->back()->with('success', 'Document deleted successfully.');
        }

        // Otherwise, you could optionally handle File201 deletion here, or return error
        return redirect()->back()->with('error', 'File not found or unauthorized.');
    }
}
