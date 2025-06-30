



<div class="border-t border-gray-300 pt-4">
    <!-- Government Documents -->
    <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Government Documents</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        @php
            $sss = $file201->sss_number ?? '-';
            $philhealth = $file201->philhealth_number ?? '-';
            $pagibig = $file201->pagibig_number ?? '-';
            $tin = $file201->tin_id_number ?? '-';
        @endphp

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">SSS number:</label>
            <input type="text" readonly value="{{ $sss }}"
                   class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Philhealth number:</label>
            <input type="text" readonly value="{{ $philhealth }}"
                   class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pag-Ibig number:</label>
            <input type="text" readonly value="{{ $pagibig }}"
                   class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tin ID number:</label>
            <input type="text" readonly value="{{ $tin }}"
                   class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
        </div>
    </div>

    <!-- Licenses / Certifications -->
    <h3 class="text-lg font-semibold text-[#BD6F22] mb-3">Licenses / Certifications</h3>

    @php
        $licenses = $file201->licenses ?? [];
    @endphp

    @forelse($licenses as $index => $license)
        <div class="border-t border-gray-200 py-4">
            <h4 class="text-md font-semibold text-gray-700 mb-2">
                License / Certification #{{ $index + 1 }}
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License / Certification</label>
                    <input type="text" readonly
                           value="{{ $license['name'] ?? '-' }}"
                           class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License Number</label>
                    <input type="text" readonly
                           value="{{ $license['number'] ?? '-' }}"
                           class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date Taken</label>
                    <input type="text" readonly
                           value="{{ !empty($license['date']) ? \Carbon\Carbon::parse($license['date'])->format('F d, Y') : '-' }}"
                           class="w-full bg-gray-100 cursor-not-allowed border border-gray-300 rounded-md px-3 py-2" />
                </div>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500 italic">No licenses/certifications recorded.</p>
    @endforelse

    <!-- License Summary -->
    @if(count($licenses))
        <div class="mt-6">
            <h4 class="text-md font-semibold text-[#BD6F22] mb-2">Summary of License / Certification Names</h4>
            <ul class="list-disc list-inside text-sm text-gray-800">
                @foreach ($licenses as $license)
                    @if(!empty($license['name']))
                        <li>{{ $license['name'] }}</li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif
</div>


