<div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-all group">
    <div class="flex items-center">
        <div class="p-3 rounded-xl bg-{{ $color }}-100 group-hover:scale-105 transition-transform">
            <svg class="w-6 h-6 text-{{ $color }}-600" fill="currentColor" viewBox="0 0 20 20">
                @if($icon === 'calendar-days')
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                @elseif($icon === 'clock')
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12h-2v4h2V6z" clip-rule="evenodd"></path>
                @elseif($icon === 'briefcase')
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm0 14v-5.5a1 1 0 011-1h6a1 1 0 011 1V16h-8z" clip-rule="evenodd"></path>
                @elseif($icon === 'exclamation-triangle')
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                @endif
            </svg>
        </div>
        <div class="ml-4">
            <p class="text-sm font-medium text-slate-600 uppercase tracking-wide">{{ $title }}</p>
            <p class="text-3xl font-bold text-slate-900 mt-1">{{ $value }}</p>
            <p class="text-sm text-{{ $color }}-600 font-semibold mt-1">{{ $subtitle }}</p>
            {{ $slot }}
        </div>
    </div>
</div>