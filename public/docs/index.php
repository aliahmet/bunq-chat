<?php
$uri = $_SERVER['REQUEST_URI'];
$url = parse_url($uri);
?><!-- HTML for static distribution bundle build -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CHAT-API</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700|Source+Code+Pro:300,600|Titillium+Web:400,600,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="./swagger-ui.css" >
  <link rel="icon" type="image/png" href="./favicon-32x32.png" sizes="32x32" />
  <style>
    html
    {
      box-sizing: border-box;
      overflow: -moz-scrollbars-vertical;
      overflow-y: scroll;
    }
    *,
    *:before,
    *:after
    {
      box-sizing: inherit;
    }

    body {
      margin:0;
      background: #fafafa;
    }
  </style>
</head>

<body>
<!-- The Modal -->
<div id="swagger-ui"></div>

<script src="./swagger-ui-bundle.js"> </script>
<script src="./swagger-ui-standalone-preset.js"> </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script>
window.onload = function() {
    if(window.location.pathname.substr(-1,1) != "/") window.location.pathname+="/";
  
  // Build a system
  const ui = SwaggerUIBundle({
    url: window.location.origin + "/swagger",
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
  })

  window.ui = ui
}

$(function () {
    $('body').on('DOMNodeInserted', 'input[placeholder=Authorization]', function () {
        accesstoken = localStorage.getItem("accesstoken");
        if(accesstoken)
            $(this).val(accesstoken)
    });
})
</script>
</body>

</html>
