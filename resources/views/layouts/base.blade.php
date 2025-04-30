<!DOCTYPE html>
<html lang="en">

<head>

  @include('layouts.shared/title-meta', ['title' => $title])

  @include('layouts.shared/head-css')
  @yield('css')
</head>

<body class="bg-primary d-flex justify-content-center align-items-center min-vh-100 p-5">

 @yield('content')

 @include('layouts.shared/footer-scripts')
 @yield('scripts')
</body>

</html>
