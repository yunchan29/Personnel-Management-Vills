<div class="relative" x-data="{ open: false }">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-xl z-20">
        {{ $content }}
    </div>
</div>
