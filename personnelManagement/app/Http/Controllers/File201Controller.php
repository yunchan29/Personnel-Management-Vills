<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File201;

class File201Controller extends Controller
{
    /**
     * Display the 201 form.
     */
public function show()
{
    $file201 = auth()->user()->file201;

    // Check if user is an applicant or employee
    $view = auth()->user()->role === 'employee'
        ? 'employee.files'
        : 'applicant.files';

    return view($view, compact('file201'));
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

        return redirect()->back()->with('success', '201 file saved successfully!');
    }
}
