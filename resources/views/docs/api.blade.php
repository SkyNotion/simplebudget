<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta charset="UTF-8">
        <title>Simple Budget Server API</title>
        <link rel="stylesheet" type="text/css" href="{{ asset('/swagger-ui/swagger-ui.css') }}">
    </head>
    <body>
        <div id="swagger-ui"></div>
        <script src="{{ asset('/swagger-ui/swagger-ui-standalone-preset.js') }}"></script>
        <script src="{{ asset('/swagger-ui/swagger-ui-bundle.js') }}"></script>
        <script>
            function HideTopbarPlugin() {
              return {
                components: {
                  Topbar: function() { return null }
                }
              }
            }

            window.onload = function() {
                window.ui = SwaggerUIBundle({
                    url: "{{ asset("/api/$webapp/api.yaml") }}",
                    dom_id: '#swagger-ui',
                    deepLinking: true,
                    presets: [
                        SwaggerUIBundle.presets.apis,
                        SwaggerUIStandalonePreset
                    ],
                    plugins: [
                        SwaggerUIBundle.plugins.DownloadUrl,
                        HideTopbarPlugin
                    ],
                    layout: "StandaloneLayout",
                });
            }
        </script>
    </body>
</html>