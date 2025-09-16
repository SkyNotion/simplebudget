<!doctype html>
<html>
  <head>
    <title>@yield('title') | SimpleBudget</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
    @include('font')
  </head>
  <body>
    @yield('content')
    @if((isset($message) && isset($level)) || (session('message') && session('level')))
      <div id="toast-container"></div>
      <script>
        function Toast(message, level, duration = 10000) {
          const toastContainer = document.getElementById('toast-container');
          const toast = document.createElement('div');
          toast.classList.add('toast', `toast-${level}`);
          toast.innerHTML = `<span>${message}</span>`;
          toastContainer.appendChild(toast);
  
          setTimeout(() => {
              toast.remove();
          }, duration);
        }
        Toast("{{ session('message') ? session('message') : $message }}",
              "{{ session('level') ? session('level') : $level }}");
      </script>
    @endif
  </body>
</html>