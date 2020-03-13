@extends('spa-modules.spa-app')

@section('content')
<div id="spa-proofer" v-cloak class="full-height">
<!-- Main Content -->
    <proofer-container></proofer-container>
</div>
@endsection

@section('scripts')
<!-- JavaScript -->
<script src="/js/spa-modules/spa-proofer.js"></script>
@endsection
