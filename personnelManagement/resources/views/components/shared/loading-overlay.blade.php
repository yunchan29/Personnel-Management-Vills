@props(['id' => 'loading-overlay'])

<div id="{{ $id }}" class="fixed inset-0 bg-white bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="flex items-center space-x-4">
        <x-shared.loading-spinner size="h-8 w-8" color="text-[#BD9168]" />
        <span class="text-[#BD9168] font-semibold text-lg">Loading...</span>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show loading overlay on form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                document.getElementById('{{ $id }}').classList.remove('hidden');
            });
        });

        // Show loading overlay on navigation links (except anchors and external links)
        document.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href && !href.startsWith('#') && !href.startsWith('javascript:') &&
                    !this.hasAttribute('target') && !this.hasAttribute('download')) {
                    document.getElementById('{{ $id }}').classList.remove('hidden');
                }
            });
        });
    });
</script>
