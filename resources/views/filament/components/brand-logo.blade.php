@php
    $isLogin = request()->routeIs('filament.admin.auth.*');
@endphp

@if ($isLogin)
    <div class="flex flex-col items-center justify-center gap-2 py-1">
        <img src="{{ asset('images/logo-hsk.png') }}"
             alt="HSK Secret Logo"
             class="h-16 w-auto object-contain" />
        <span class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">
            WMS Gudang Pusat
        </span>
    </div>
@else
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logo-hsk.png') }}"
             alt="HSK Secret Logo"
             class="h-9 w-auto object-contain shrink-0" />
        <span class="text-base font-bold tracking-tight text-gray-950 dark:text-white whitespace-nowrap">
            WMS Gudang Pusat
        </span>
    </div>
@endif
