@props(['content' => null])

@php
    use GrahamCampbell\Markdown\Facades\Markdown;

    try {
        $markdown = $content ?? $slot;

        // Simulate an error here for testing

        $html = Markdown::convertToHtml($markdown);
    } catch (\Throwable $e) {
        $html = '<div class="bg-red-100 text-red-900 p-4 rounded-lg max-w-xl mr-auto">Error rendering AI message.<br>' . e($e->getMessage()) . '</div>';
    }
@endphp

<div class="text-[15px] text-gray-900 p-4 rounded-lg max-w-xl mr-auto prose max-w-none">
    {!! $html !!}
</div>
