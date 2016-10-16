<?php
// Create a list with items available to read
$rootDir = dirname(__DIR__);
$notesDir = $rootDir . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR;

function listNotes($directory) {
    global $notesDir;
    $directoryIterator = new FilesystemIterator($directory);

    $list = '';
    foreach ($directoryIterator as $directoryEntry) {
        // Negeer alle bestanden welke geen Markdown files zijn
        if ($directoryEntry->isFile()
            && $directoryEntry->getExtension() !== 'md'
        ) {
            continue;
        }

        if ($directoryEntry->isFile()) {
            $filename = substr($directoryEntry->getFilename(), 0, strlen($directoryEntry->getFilename()) - 3);
            $noteTitle = str_replace('_', ' ', $filename);

            // The pathname without the extension can be used as a slug
            $pathname = $directoryEntry->getPathname();
            $pathname = str_replace($notesDir, '', $pathname);
            $pathname = str_replace(DIRECTORY_SEPARATOR, '/', $pathname);
            $pathnameWithoutExt = substr($pathname, 0, strlen($pathname) - 3);

            $list .= "<li><a href='/read/{$pathnameWithoutExt}' title='{$noteTitle}'>{$noteTitle}</a></li>";
            continue;
        }

        if ($directoryEntry->isDir()) {
            $directoryList = listNotes($directoryEntry->getPathname());

            // If the direcotry is empty, than ignore it in the index
            if (empty($directoryList)) {
                continue;
            }

            $list .= <<<html
                <li>
                    {$directoryEntry->getFilename()}
                    <ul>
                        {$directoryList}
                    </ul>
                </li>
html;
            continue;
        }
    }

    return $list;
}

$notesList = listNotes($notesDir);

echo <<<html
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Index | My Notes</title>
        </head>
        <body>
            <h1>Index</h1>
            <p>
                Click the link of the notes you would like to know more about.
            </p>
            <p>
                {$notesList}
            </p>
        </body>
    </html>
html;
