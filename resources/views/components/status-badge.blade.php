@props(['status'])

@php
$styles = match($status) {
    'open'        => 'bg-blue-100 text-blue-800',
    'in_progress' => 'bg-yellow-100 text-yellow-800',
    'resolved'    => 'bg-green-100 text-green-800',
    'closed'      => 'bg-gray-100 text-gray-600',
    default       => 'bg-gray-100 text-gray-600',
};

$labels = [
    'open'        => 'Open',
    'in_progress' => 'In Progress',
    'resolved'    => 'Resolved',
    'closed'      => 'Closed',
];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $styles }}">
    {{ $labels[$status] ?? $status }}
</span>
