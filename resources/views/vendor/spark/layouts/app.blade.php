<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,600' rel='stylesheet' type='text/css'>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css' rel='stylesheet'
          type='text/css'>

    <!-- CSS -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/notifications.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/settings.css') }}">
    <!-- Scripts -->
@yield('scripts', '')

<!-- Global Spark Object -->
    <script>
        window.Spark = <?php echo json_encode(array_merge(
            Spark::scriptVariables(), []
        )); ?>;
    </script>

    {{--Gist Script (ConvertFox JS code)--}}
    <script>
        (function (d, h, w) {
            var convertfox = w.convertfox = w.convertfox || [];
            convertfox.methods = ['trackPageView', 'identify', 'track', 'setAppId'];
            convertfox.factory = function (t) {
                return function () {
                    var e = Array.prototype.slice.call(arguments);
                    e.unshift(t);
                    convertfox.push(e);
                    return convertfox;
                }
            };
            for (var i = 0; i < convertfox.methods.length; i++) {
                var c = convertfox.methods[i];
                convertfox[c] = convertfox.factory(c)
            }
            s = d.createElement('script'), s.src = "//d3sjgucddk68ji.cloudfront.net/convertfox.min.js", s.async = !0, e = d.getElementsByTagName(h)[0], e.appendChild(s), s.addEventListener('load', function (e) {
            }, !1), convertfox.setAppId("w27nyas2"), convertfox.trackPageView()
        })(document, 'head', window);
    </script>
</head>
<body class="with-navbar">
<div id="spark-app" v-cloak>
    <!-- Navigation -->
@if (Auth::check())
    @include('spark::nav.user')
@else
    @include('spark::nav.guest')
@endif

<!-- Main Content -->
@yield('content')

<!-- Application Level Modals -->
    @if (Auth::check())
        @include('spark::modals.custom-notifications')
        @include('spark::modals.support')
        @include('spark::modals.session-expired')
    @endif
</div>

<!-- JavaScript -->
<script src="{{ mix('js/app.js') }}"></script>
<script src="/js/sweetalert.min.js"></script>
</body>
</html>
