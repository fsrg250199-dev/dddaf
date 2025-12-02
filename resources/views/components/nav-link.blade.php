@props(['href' => '#', 'icon' => '', 'iconStyle' => 'solid', 'active' => false])

@php
    $iconClass = $iconStyle === 'duotone' ? 'fa-duotone' : 'fas';

    $classes = $active
        ? 'bg-[#3a5a55] text-[#f8fafc] border-r-2 border-[#d4af37] shadow-sm'
        : 'text-[#f8fafc] hover:bg-[#3a5a55] hover:text-[#e6c45c]';
@endphp

<a href="{{ $href }}"
    class="flex items-center px-4 py-3 rounded-lg transition-all duration-300 group {{ $classes }}">
    <i
        class="{{ $iconClass }} fa-{{ $icon }} mr-3 {{ $active ? 'text-[#e6c45c]' : 'text-[#e6c45c]/80 group-hover:text-[#e6c45c]' }}"></i>
    <span class="font-medium tracking-wide">{{ $slot }}</span>
    @if ($active)
        <span class="ml-auto h-2 w-2 bg-[#e6c45c] rounded-full animate-pulse"></span>
    @endif
</a>
