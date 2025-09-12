{{-- resources/views/vendor/moonshine/layouts/custom.blade.php --}}
@include('moonshine::layouts.base')

{{-- Inyectar JS personalizado al final del body --}}
@push('scripts')
    <script src="/js/custom.js"></script>
@endpush
