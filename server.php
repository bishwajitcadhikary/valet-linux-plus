<?php

/**
 * Define the user's "~/.valet" path.
 */
define('VALET_HOME_PATH', posix_getpwuid(fileowner(__FILE__))['dir'].'/.valet');
define('VALET_STATIC_PREFIX', '41c270e4-5535-4daa-b23e-c269744c2f45');

if (!function_exists('show_valet_404')) {
    /**
     * Show the Valet 404 "Not Found" page.
     */
    function show_valet_404()
    {
        http_response_code(404);
        require __DIR__.'/cli/templates/404.html';
        exit;
    }
}

if (!function_exists('show_project_directory')) {
    /**
     * Show the Valet 404 "Not Found" page.
     *
     * @param string $directory
     * @param string $siteName
     * @param string $uri
     * @param array  $ignoredPaths
     */
    function show_project_directory($directory, $siteName, $uri, $ignoredPaths = ['.'])
    {
        if (!substr($uri, 0, -1)) {
            $ignoredPaths[] = '..';
        }
        include __DIR__.'/cli/templates/directory.php';
        exit;
    }
}

if (!function_exists('show_available_sites')) {
    /**
     * Show available sites.
     *
     * @param array $valetConfig
     */
    function show_available_sites($valetConfig)
    {
        $availableSites = [];
        foreach ($valetConfig['paths'] as $path) {
            foreach (glob($path.'/*', GLOB_ONLYDIR) as $dirPath) {
                $slug = valet_path_to_slug($dirPath);
                $availableSites[$slug] = ucfirst(str_replace('-', ' ', $slug));
            }
        }
        require __DIR__.'/cli/templates/sites.php';
        exit;
    }
}

if (!function_exists('valet_support_xip_io')) {
    /**
     * @param $domain string Domain to filter
     *
     * @return string Filtered domain (without xip.io feature)
     */
    function valet_support_xip_io($domain)
    {
        if (substr($domain, -7) === '.xip.io') {
            // support only ip v4 for now
            $domainPart = explode('.', $domain);
            if (count($domainPart) > 6) {
                $domain = implode('.', array_reverse(array_slice(array_reverse($domainPart), 6)));
            }
        }

        if (strpos($domain, ':') !== false) {
            $domain = explode(':', $domain)[0];
        }

        return $domain;
    }
}

if (!function_exists('valet_path_to_slug')) {
    /**
     * Convert absolute path to slug.
     *
     * @param string $path
     *
     * @return string Slug version of last folder name
     */
    function valet_path_to_slug($path)
    {
        $replace = [
            '&lt;'   => '', '&gt;' => '', '&#039;' => '', '&amp;' => '',
            '&quot;' => '', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'Ae',
            '&Auml;' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'A', '??' => 'Ae',
            '??'      => 'C', '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'C', '??' => 'D', '??' => 'D',
            '??'      => 'D', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'E',
            '??'      => 'E', '??' => 'E', '??' => 'E', '??' => 'E', '??' => 'G', '??' => 'G',
            '??'      => 'G', '??' => 'G', '??' => 'H', '??' => 'H', '??' => 'I', '??' => 'I',
            '??'      => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I', '??' => 'I',
            '??'      => 'I', '??' => 'IJ', '??' => 'J', '??' => 'K', '??' => 'K', '??' => 'K',
            '??'      => 'K', '??' => 'K', '??' => 'K', '??' => 'N', '??' => 'N', '??' => 'N',
            '??'      => 'N', '??' => 'N', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
            '??'      => 'Oe', '&Ouml;' => 'Oe', '??' => 'O', '??' => 'O', '??' => 'O', '??' => 'O',
            '??'      => 'OE', '??' => 'R', '??' => 'R', '??' => 'R', '??' => 'S', '??' => 'S',
            '??'      => 'S', '??' => 'S', '??' => 'S', '??' => 'T', '??' => 'T', '??' => 'T',
            '??'      => 'T', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'Ue', '??' => 'U',
            '&Uuml;' => 'Ue', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U', '??' => 'U',
            '??'      => 'W', '??' => 'Y', '??' => 'Y', '??' => 'Y', '??' => 'Z', '??' => 'Z',
            '??'      => 'Z', '??' => 'T', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a',
            '??'      => 'ae', '&auml;' => 'ae', '??' => 'a', '??' => 'a', '??' => 'a', '??' => 'a',
            '??'      => 'ae', '??' => 'c', '??' => 'c', '??' => 'c', '??' => 'c', '??' => 'c',
            '??'      => 'd', '??' => 'd', '??' => 'd', '??' => 'e', '??' => 'e', '??' => 'e',
            '??'      => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e', '??' => 'e',
            '??'      => 'f', '??' => 'g', '??' => 'g', '??' => 'g', '??' => 'g', '??' => 'h',
            '??'      => 'h', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'i',
            '??'      => 'i', '??' => 'i', '??' => 'i', '??' => 'i', '??' => 'ij', '??' => 'j',
            '??'      => 'k', '??' => 'k', '??' => 'l', '??' => 'l', '??' => 'l', '??' => 'l',
            '??'      => 'l', '??' => 'n', '??' => 'n', '??' => 'n', '??' => 'n', '??' => 'n',
            '??'      => 'n', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'oe',
            '&ouml;' => 'oe', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'o', '??' => 'oe',
            '??'      => 'r', '??' => 'r', '??' => 'r', '??' => 's', '??' => 'u', '??' => 'u',
            '??'      => 'u', '??' => 'ue', '??' => 'u', '&uuml;' => 'ue', '??' => 'u', '??' => 'u',
            '??'      => 'u', '??' => 'u', '??' => 'u', '??' => 'w', '??' => 'y', '??' => 'y',
            '??'      => 'y', '??' => 'z', '??' => 'z', '??' => 'z', '??' => 't', '??' => 'ss',
            '??'      => 'ss', '????' => 'iy', '??' => 'A', '??' => 'B', '??' => 'V', '??' => 'G',
            '??'      => 'D', '??' => 'E', '??' => 'YO', '??' => 'ZH', '??' => 'Z', '??' => 'I',
            '??'      => 'Y', '??' => 'K', '??' => 'L', '??' => 'M', '??' => 'N', '??' => 'O',
            '??'      => 'P', '??' => 'R', '??' => 'S', '??' => 'T', '??' => 'U', '??' => 'F',
            '??'      => 'H', '??' => 'C', '??' => 'CH', '??' => 'SH', '??' => 'SCH', '??' => '',
            '??'      => 'Y', '??' => '', '??' => 'E', '??' => 'YU', '??' => 'YA', '??' => 'a',
            '??'      => 'b', '??' => 'v', '??' => 'g', '??' => 'd', '??' => 'e', '??' => 'yo',
            '??'      => 'zh', '??' => 'z', '??' => 'i', '??' => 'y', '??' => 'k', '??' => 'l',
            '??'      => 'm', '??' => 'n', '??' => 'o', '??' => 'p', '??' => 'r', '??' => 's',
            '??'      => 't', '??' => 'u', '??' => 'f', '??' => 'h', '??' => 'c', '??' => 'ch',
            '??'      => 'sh', '??' => 'sch', '??' => '', '??' => 'y', '??' => '', '??' => 'e',
            '??'      => 'yu', '??' => 'ya',
        ];

        // make a human readable string
        $slug = strtr(basename($path), $replace);

        // replace non letter or digits by -
        $slug = preg_replace('~[^\\pL\d.]+~u', '-', $slug);

        // trim
        $slug = trim($slug, '-');

        // remove unwanted characters
        $slug = preg_replace('~[^-\w.]+~', '', $slug);

        return strtolower($slug);
    }
}

/**
 * Load the Valet configuration.
 */
$valetConfig = json_decode(
    file_get_contents(VALET_HOME_PATH.'/config.json'),
    true
);

/**
 * Parse the URI and site / host for the incoming request.
 */
$uri = rawurldecode(
    explode('?', $_SERVER['REQUEST_URI'])[0]
);

$siteName = basename(
// Filter host to support xip.io feature
    valet_support_xip_io(explode(':', strtolower($_SERVER['HTTP_HOST']))[0]),
    '.'.$valetConfig['domain']
);

if (strpos($siteName, 'www.') === 0) {
    $siteName = substr($siteName, 4);
}

/**
 * Validate if request is from Remote IP.
 * */
if ($_SERVER['SERVER_ADDR'] !== '127.0.0.1') {
    if (strpos($uri, '/') === 0) {
        $urlParam = substr($uri, 1, strlen($uri));
        if (substr($urlParam, -1) === '/') {
            $urlParam = substr($urlParam, 0, -1);
        }
        if (strtolower($urlParam) === 'valet-sites') {
            $urlParams = parse_url($_SERVER['REQUEST_URI']);
            if (isset($urlParams['query'])) {
                parse_str($urlParams['query'], $parameters);
                if ($parameters['use']) {
                    setcookie('valet_remote_path', $parameters['use'], 0);
                    header('Location: /');
                    exit;
                }
            }
            show_available_sites($valetConfig);
            exit;
        }
    }
    if (!isset($_COOKIE['valet_remote_path'])) {
        header('Location: /valet-sites');
        exit;
    }
    $siteName = $_COOKIE['valet_remote_path'];
}

/**
 * Determine the fully qualified path to the site.
 */
$valetSitePath = null;

foreach ($valetConfig['paths'] as $path) {
    $domain = ($pos = strrpos($siteName, '.')) !== false
        ? substr($siteName, $pos + 1)
        : null;

    foreach (glob($path.'/*', GLOB_ONLYDIR) as $dirPath) {
        $slug = valet_path_to_slug($dirPath);

        if ($slug == $siteName || $slug == $domain) {
            $valetSitePath = $dirPath;

            break 2;
        }
    }
}

if (is_null($valetSitePath)) {
    show_valet_404();
}

/**
 * Find the appropriate Valet driver for the request.
 */
$valetDriver = null;

require __DIR__.'/cli/drivers/require.php';

$valetDriver = ValetDriver::assign($valetSitePath, $siteName, $uri);

if (!$valetDriver) {
    show_valet_404();
}

/**
 * Overwrite the HTTP host for Ngrok.
 */
if (isset($_SERVER['HTTP_X_ORIGINAL_HOST'])) {
    $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_ORIGINAL_HOST'];
}

/**
 * Allow driver to mutate incoming URL.
 */
$uri = $valetDriver->mutateUri($uri);

/**
 * Determine if the incoming request is for a static file.
 */
$isPhpFile = pathinfo($uri, PATHINFO_EXTENSION) === 'php';

if ($uri !== '/' && !$isPhpFile && $staticFilePath = $valetDriver->isStaticFile($valetSitePath, $siteName, $uri)) {
    $valetDriver->serveStaticFile($staticFilePath, $valetSitePath, $siteName, $uri);

    return;
}

/**
 * Attempt to dispatch to a front controller.
 */
$frontControllerPath = $valetDriver->frontControllerPath(
    $valetSitePath,
    $siteName,
    $uri
);
if (!$frontControllerPath) {
    if (is_dir($valetSitePath.$uri)) {
        show_project_directory($valetSitePath, $siteName, $uri);
    } else {
        show_valet_404();
    }
}

chdir(dirname($frontControllerPath));

require $frontControllerPath;
