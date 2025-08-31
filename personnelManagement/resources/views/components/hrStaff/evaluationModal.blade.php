<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Evaluation Modal -->
<div x-show="showModal"
     x-cloak
     x-transition
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">

    <form method="POST"
        :action="`{{ route('hrStaff.evaluations.store', ':id') }}`.replace(':id', selectedApplicationId)"
        class="bg-white rounded-lg p-6 w-full max-w-2xl"
        @click.away="showModal = false">

        @csrf

        <h2 class="text-xl font-bold text-[#BD6F22] mb-4">Training Evaluation</h2>
        <p class="text-sm text-gray-600 mb-4">Employee: 
            <span class="font-semibold" x-text="selectedEmployee"></span>
        </p>

        <div class="space-y-4 mb-6">
            <template x-for="(label, key) in categories" :key="key">
                <div class="flex justify-between items-center">
                    <span x-text="label[0]"></span>
                    <div class="relative">
                        <input type="number"
                            :name="key + '_score'"
                            x-model.number="scores[key]"
                            @input="validateScore(key)"
                            :max="label[1]"
                            min="0"
                            class="border border-gray-300 rounded pl-2 pr-10 py-1 w-28 text-right focus:ring-[#BD6F22]"
                            placeholder="0" />
                        <span class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500" x-text="'/' + label[1]"></span>
                    </div>
                </div>
            </template>
        </div>

        <input type="hidden" name="result" :value="result.toLowerCase()" />

        <div class="mb-6 text-right text-sm text-gray-700 font-semibold">
            Overall Score: <span x-text="totalScore + '/100'"></span>
        </div>

       <div class="mb-6">
    <label class="block font-medium text-sm mb-1">Result</label>
    <div class="flex items-center">
        <div class="ml-auto">
            <template x-if="result === 'Passed'">
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-green-500 text-white">Passed</span>
            </template>
            <template x-if="result === 'Failed'">
                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-red-500 text-white">Failed</span>
            </template>
        </div>
    </div>
</div>


        <div class="text-sm text-gray-700 border-t pt-4 mt-4">
            <p class="font-medium mb-2">Scoring and Interpretation:</p>
            <div class="grid grid-cols-2 gap-2">
                <div>70 – 100</div><div>Passed</div>
                <div>69 and below</div><div>Failed</div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <button type="button" @click="showModal = false"
                    class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">
                Cancel
            </button>
            <button type="submit"
                    class="bg-[#BD6F22] hover:bg-[#a55f1d] text-white px-4 py-2 rounded">
                Submit
            </button>
        </div>
    </form>
</div>
