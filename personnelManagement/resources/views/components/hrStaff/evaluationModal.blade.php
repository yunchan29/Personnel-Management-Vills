<style>
    [x-cloak] { display: none !important; }
</style>

<!-- Evaluation Modal -->
<div 
    x-show="showModal"
    x-cloak
    x-transition
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
    @keydown.escape.window="showModal = false"
>
    <form 
        method="POST"
        :action="`{{ route('hrStaff.evaluations.store', ':id') }}`.replace(':id', selectedApplicationId ?? 0)"
        class="bg-white rounded-lg p-6 w-full max-w-2xl relative"
        @click.away="showModal = false"
        x-ref="evaluationForm"
    >
        @csrf

        <!-- Title -->
        <h2 class="text-xl font-bold text-[#BD6F22] mb-2">
            <span x-text="alreadyEvaluated ? 'View Evaluation' : 'Training Evaluation'"></span>
        </h2>
        <p class="text-sm text-gray-600 mb-4">
            Employee: <span class="font-semibold" x-text="selectedEmployee ?? 'N/A'"></span>
        </p>

        <!-- Scores -->
        <div class="space-y-4 mb-6">
            <template x-for="(label, key) in categories" :key="key">
                <div class="flex justify-between items-center">
                    <span x-text="label[0]"></span>
                    <div class="relative">
                        <input 
                            type="number"
                            :name="key + '_score'"
                            x-model.number="scores[key]"
                            @input="!alreadyEvaluated && validateScore(key)"
                            :max="label[1]"
                            min="0"
                            :disabled="alreadyEvaluated"
                            class="border border-gray-300 rounded pl-2 pr-10 py-1 w-28 text-right 
                                   focus:ring-[#BD6F22] focus:border-[#BD6F22] disabled:bg-gray-100"
                            placeholder="0"
                        />
                        <span 
                            class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500"
                            x-text="'/' + label[1]">
                        </span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Result & Score -->
        <input type="hidden" name="result" :value="result ?? ''" />

        <div class="mb-6 text-right text-sm text-gray-700 font-semibold">
            Overall Score: <span x-text="(totalScore ?? 0) + '/100'"></span>
        </div>

        <div class="mb-6">
            <label class="block font-medium text-sm mb-1">Result</label>
            <div class="flex items-center">
                <div class="ml-auto">
                    <template x-if="result === 'Passed'">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                     bg-green-500 text-white">Passed</span>
                    </template>
                    <template x-if="result === 'Failed'">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold 
                                     bg-red-500 text-white">Failed</span>
                    </template>
                </div>
            </div>
        </div>

        <!-- Scoring Legend -->
        <div class="text-sm text-gray-700 border-t pt-4 mt-4">
            <p class="font-medium mb-2">Scoring and Interpretation:</p>
            <div class="grid grid-cols-2 gap-2">
                <div>70 – 100</div><div>Passed</div>
                <div>69 and below</div><div>Failed</div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <button 
                type="button" 
                @click="showModal = false"
                class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded"
            >
                Close
            </button>
            
            <!-- Only show submit if no evaluation yet -->
            <template x-if="!alreadyEvaluated">
                <button 
                    type="submit"
                    @click.prevent="submitEvaluation"
                    class="bg-[#BD6F22] hover:bg-[#a55f1d] text-white px-4 py-2 rounded"
                >
                    Submit
                </button>
            </template>
        </div>
    </form>
</div>
