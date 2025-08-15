<!doctype html>
<html>
    <head>
      <title>Simple Budget Server API</title>
      <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1" />
    </head>
    <body>
      <div id="app"></div>
      <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
      <script>
        Scalar.createApiReference('#app', {
          url: "{{ asset("/api/$webapp/api.yaml") }}",
          proxyUrl: 'https://proxy.scalar.com',
        })
      </script>
    </body>
</html>