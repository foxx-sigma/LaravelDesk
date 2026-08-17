@props(['priority'])

@php
$styles = match($priority) {
    'low'    => 'bg-gray-100 text-gray-600',
    'medium' => 'bg-blue-100 text-blue-700',
    'high'   => 'bg-orange-100 text-orange-700',
    'urgent' => 'bg-red-100 text-red-700',
    default  => 'bg-gray-100 text-gray-600',
};

$labels = [
    'low'    => '⬇ Low',
    'medium' => '➡ Medium',
    'high'   => '⬆ High',
    'urgent' => '🔴 Urgent',
];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $styles }}">
    {{ $labels[$priority] ?? $priority }}
</span>
