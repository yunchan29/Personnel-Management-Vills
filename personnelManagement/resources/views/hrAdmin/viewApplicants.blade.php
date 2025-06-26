@extends('layouts.hrAdmin')
@section('content')
<section>
    <div class="p-6 bg-white rounded-lg shadow">
  <h1 class="mb-6 text-2xl font-bold text-[#BD6F22]">Applications</h1>
  <div class="flex space-x-6 border-b border-gray-300 text-sm font-semibold text-gray-600 pb-2 mb-4">
   
    <button>Job Postings</button>
     <button class="text-[#BD6F22] border-b-2 border-[#BD6F22] pb-1">Applicants</button>
    <button>Interview Schedule</button>
    <button>Training Schedule</button>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm text-left text-gray-700">
      <thead class="border-b font-semibold bg-gray-50">
        <tr>
          <th class="py-3 px-4">Name</th>
          <th class="py-3 px-4">Position Applied</th>
          <th class="py-3 px-4">Company</th>
          <th class="py-3 px-4">Date Applied</th>
          <th class="py-3 px-4">Resume</th>
          <th class="py-3 px-4">201 Files</th>
          <th class="py-3 px-4">Progress</th>
        </tr>
      </thead>
      <tbody>
        <tr class="border-b hover:bg-gray-50">
          <td class="py-3 px-4 font-medium">Charlene S. Manzanilla</td>
          <td class="py-3 px-4">Production Operator</td>
          <td class="py-3 px-4">Toyota Philippines Corp.</td>
          <td class="py-3 px-4 italic">May 14, 2025</td>
          <td class="py-3 px-4">
            <button class="bg-[#BD6F22] text-white px-3 py-1 rounded shadow hover:bg-[#a95e1d]">
              See Attachment
            </button>
          </td>
          <td class="py-3 px-4">
            <button class="border border-[#BD6F22] text-[#BD6F22] px-3 py-1 rounded hover:bg-[#BD6F22] hover:text-white">
              View
            </button>
          </td>
          <td class="py-3 px-4">
            <div class="relative inline-block text-left">
              <button class="bg-[#BD6F22] text-white px-3 py-1 rounded shadow hover:bg-[#a95e1d]">
                Under review
              </button>
              <!-- You can add a dropdown here if needed -->
            </div>
          </td>
        </tr>
        <!-- Repeat rows as needed -->
      </tbody>
    </table>
  </div>
</div>

</section>
@endsection