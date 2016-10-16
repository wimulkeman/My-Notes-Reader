<?php
// Check if the url is clean
if (empty($_GET['url'])) {
    header("HTTP/1.0 500 Internal server error");
}
$requestUrl = $_GET['url'];
// Prevent invalid characters send through
if (!preg_match('/[a-z0-9\/\.]/i', $requestUrl)) {
    header("HTTP/1.0 500 Internal server error");
}

$rootDir = dirname(__DIR__);
$notesDir = $rootDir . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;
$filename = str_replace('/', DIRECTORY_SEPARATOR, $requestUrl) . '.md';
$filePath = $notesDir . $filename;

if (!is_file($filePath)) {
    header("HTTP/1.0 500 Internal server error");
}

$documentTitle = str_replace(array('_', '/'), array(' ', ' > '), $requestUrl);

// Include the parser
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$markdownParser = new Parsedown();

$markdownToHtml = $markdownParser->text(file_get_contents($filePath));

echo <<<html
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
            <title>{$documentTitle} | My Notes</title>
            <link rel="stylesheet" type="text/css" href="/css/highlight/default.min.css">
            <link rel="stylesheet" type="text/css" href="/css/highlight/darcula.css">
        </head>
        <body>
            <a href="/" title="Back to index">Back to index</a>
            {$markdownToHtml}
            <script src="/js/highlight.pack.js"></script>
            <script>hljs.initHighlightingOnLoad();</script>
            <script>
                var links = document.links;

                // Make sure to other sources are opened in a new tab
                for (var i = 0, linksLength = links.length; i < linksLength; i++) {
                   if (links[i].hostname != window.location.hostname) {
                       links[i].target = '_blank';
                   } 
                }
            </script>
        </body>
    </html>
html;
