@extends('spa-modules.spa-app')

@section('content')
<div id="spa-projects" v-cloak class="full-height">
<!-- Main Content -->
    <router-view></router-view>
   <!--  <projects-container></projects-container> -->
</div>
@endsection

@section('scripts')
<!-- JavaScript -->
<script src="/js/spa-modules/spa-projects.js"></script>
@endsection
