{{-- resources/views/filament/placeholders/file-info.blade.php --}}
<div class="text-sm text-gray-600 dark:text-gray-400">
    <div class="grid grid-cols-1 gap-2">
        @if($fileSize)
            <div class="flex items-center gap-2">
                <x-heroicon-o-archive-box class="w-4 h-4" />
                <span class="font-medium">Kích thước:</span>
                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">
                    {{ $fileSize }}
                </span>
            </div>
        @endif

        @if($fileType)
            <div class="flex items-center gap-2">
                <x-heroicon-o-document class="w-4 h-4" />
                <span class="font-medium">Loại file:</span>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs">
                    {{ $fileType }}
                </span>
            </div>
        @endif

        @if($downloadCount !== null)
            <div class="flex items-center gap-2">
                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                <span class="font-medium">Lượt tải:</span>
                <span class="px-2 py-1 bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 rounded text-xs">
                    {{ number_format($downloadCount) }}
                </span>
            </div>
        @endif
    </div>
</div>