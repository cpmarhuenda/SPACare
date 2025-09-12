{{-- resources/views/vendor/moonshine/layouts/custom.blade.php --}}
@extends('moonshine::layouts.app')

@section('head')
  @parent
  @moonShineAssets   {{-- aquí entra main.css del tema --}}

  {{-- 🔎 Marcador para comprobar que este layout se usa --}}
  <meta name="custom-layout" content="on">

  {{-- ✅ Tu CSS al final, para que gane la cascada --}}
  <link rel="stylesheet" href="{{ asset('css/custom.css?v=32') }}">
@endsection

@push('scripts')
  {{-- ✅ Tu JS al final del body --}}
  <script src="{{ asset('js/custom.js?v=32') }}" defer></script>
@endpush
