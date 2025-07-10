@props(['error' => false])

@if ($error)
    <p class="text-sm text-red-500">{{ $error }}</p>
@endif
