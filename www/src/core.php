<?php
/**
 * Eurofurence Website Core Component
 * Includes debug(), dirmtime(), dircopy(), is_external(), substitute() as global functions
 * @author	flam@dogpixels.net
 * @since 	11/2015
 * @version	5.0
 * @license	MIT
 */
class EFWebCore {
	public stdClass $config; 	# the entire config as seen in config/core.json
	public stdClass $page;		# the current page
	
	public function __construct(string $configfile)
	{
		// load and parse config
		$this->config = json_decode(file_get_contents($configfile), false);
		if (is_null($this->config)) {
			die("Failed to parse " . $configfile . ", reason: " . json_last_error_msg());
		}

		// ensure correct path settings format
		$this->config->staticOut->path = trim($this->config->staticOut->path, "/") . "/";
		$this->config->staticOut->targetBase = trim($this->config->staticOut->targetBase, "/") . "/";
		$this->config->defaults->pagesDirectory = trim($this->config->defaults->pagesDirectory, "/") . "/";

		// subtract directory from request URI
		$request_uri = substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["SCRIPT_NAME"])));

		// determine page key (corresponds config.json -> pages)
		$key =
			(in_array($request_uri,["", "/"]) || str_starts_with($request_uri, "?") || str_starts_with($request_uri, "/?")) ?
			$this->config->defaults->rootPage :
			trim(parse_url($request_uri, PHP_URL_PATH), "/");

		// override page key with 404 ("Not Found"), if it's not in config.json -> pages
		if (!property_exists($this->config->pages, $key))
			$key = '404';

		// override page key with 401 ("Not Yet Available"), if page is configured not accessible
		else if (!$this->config->pages->{$key}->accessible)
			$key = '501';
		
		// http or https
		$mode = 
			((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 
			'https://' : 'http://';

		// construct base url
		$this->config->base = str_replace(
			'{number}',
			$this->config->convention->number,
			str_replace('index.php', '', $mode . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'])
		);

		// load page from config
		$this->page = $this->config->pages->{$key};
		$this->page->key = $key;

		// load page content and $meta
		ob_start();
		include($this->config->defaults->pagesDirectory . $this->page->uri);
		$this->page->content = ob_get_contents();
		ob_end_clean();

		// fill out {placeholders} within the config defaults
		$this->config->defaults = substitute($this->config->defaults, $this->config->convention);
 
		// fill out {placeholders} within the $page[] variables (from within the sub-page file)
		$page = substitute($page, $this->config->convention);

		// merge all sub-page header information to $this->page
		$this->page = (object) [...(array) $this->page, ...$page];

		// construct description meta tag content
		$this->page->keywords = implode(', ', array_unique(
			array_filter(
				array_map('trim', array_merge(
					explode(',', $this->config->defaults->keywords),
					explode(',', $this->page->keywords)
				)),
				static fn (string $keyword): bool => $keyword !== ''
			)
		));

		// construct OGP image
		$this->page->ogpImage =
			$this->config->base . 
			$this->config->defaults->ogpImagePrefix .
			(empty($this->page->ogpImage) ? $this->config->defaults->ogpImage : $this->page->ogpImage);
		
		// determine OGP image size
		$ogpImageSize = getimagesize($this->page->ogpImage);
		$this->page->ogpImageWidth = $ogpImageSize[0];
		$this->page->ogpImageHeight = $ogpImageSize[1];

		// construct robots meta tag content
		if (empty($this->page->robots)) {
			$this->page->robots = $this->config->defaults->robots;
		}

		// start output buffering
		ob_start();
	}

	/**
	 * Returns the full url to the current page.
	 * @since 3.12
	 * @return string full url, e.g. https://www.eurofurence.org/EF25/artshow/bidding
	 */
	public function get_full_url() : string	{
		return $this->config->base . $this->page->key;
	}

	/**
	 * Returns an array of data to populate schema.org breadcrumb lists.
	 * @since 3.00
	 * @return array of objects containing page data
	 */
	public function get_breadcrumb_data() {
		$results = [];

		// return empty in case of root page
		if ($this->page->key === $this->config->defaults->rootPage) {
			return $results;
		}

		$path = "";
		foreach (explode("/", $this->page->key) as $key) {
			// construct key path
			$path .= $key . "/";

			if (!property_exists($this->config->pages, trim($path, "/"))) {
				$path = $this->config->defaults->notFoundPage;
			}
			
			// append desired data from page object
			$ret = new stdClass();
			$ret->name = $this->get_page($path)->nav;
			$ret->url = trim($this->config->base . $path, "/");

			// appends to results
			$results[] = $ret;
		}

		return $results;
	}

	/**
	 * Returns menu html as a string.
	 * @since 2.00
	 * @return string menu html
	 */
	public function get_menu() : string	{
		// copy config.menu.categoryOrder as keys to empty arrays
		$categorized_pages = array_fill_keys($this->config->menu->categoryOrder, []);

		// sort pages into categories ($categorized_pages)
		foreach ($this->config->pages as $key => $page) {
			$category = $page->cat ?? "";

			if ($page->nav && $page->accessible && $category !== "") {
				// if category is not listed in config.menu.categoryOrder, append to end
				if (!array_key_exists($category, $categorized_pages)) {
					$categorized_pages[$category] = [];
				}
				$categorized_pages[$category][$key] = $page;
			}
		}

		// generate categories html string
		$category_i = 1;
		$categories_html = "";
		foreach ($categorized_pages as $category_title => $pages)
		{
			// insert counter (e.g. tabindex)
			$category_html = mb_ereg_replace("\{i\}", $category_i++, $this->config->menu->templates->category);

			// insert category title
			$category_html = mb_ereg_replace("\{title\}", $category_title, $category_html);
			
			// generate items html string
			$items = "";
			foreach ($pages as $key => $page)
			{
				// determine if page uri is external link (e.g. starts with "http(s)://" or "www.")
				$ext = is_external($page->uri);

				// load item template
				$item = $this->config->menu->templates->item;


				// insert href
				$item = mb_ereg_replace
				(
					"\{href\}",
					!$this->config->defaults->externalEmbed && $ext? $page->uri : $key,
					$item
				);

				// insert hrefSuffix
				$item = mb_ereg_replace("\{hrefSuffix\}", $this->config->menu->hrefSuffix, $item);

				// insert ActiveClass, if current == page active
				$item = mb_ereg_replace
				(
					"\{ifActiveClass\}",
					($this->page !== $page? "" : $this->config->menu->ifActiveClass),
					$item
				);

				// insert target property to external targets
				$item = mb_ereg_replace
				(
					"\{externalTarget\}",
					!$this->config->defaults->externalEmbed && $ext? $this->config->menu->externalTarget : "",
					$item
				);				

				// insert menuText
				$item = mb_ereg_replace("\{menuText\}", $page->nav, $item);

				// append item to items html string
				$items .= $item;
			}

			// insert item html string into categories html string 
			$categories_html .= mb_ereg_replace("\{items\}", $items, $category_html);
		}

		// insert categories html string into nav html string and return
		return mb_ereg_replace("\{categories\}", $categories_html, $this->config->menu->templates->nav);
	}

	/**
	 * If config.staticOut is enabled, write output cache to file under
	 * config.staticOut.path. If $_GET["export"] is set, then automate each visiting page.
	 * @since 4.00
	 */
	public function end() {
		// if static output is enabled, write static file
		if ($this->config->staticOut->enabled) {
			$this->write_static_output();
		}

		// end output buffering and obtain buffer content
		$ob = ob_get_contents();
		ob_end_clean();

		// if GET export is set, trigger mechanic to auto-visit every accessible page
		if (isset($_GET['export'])) {
			// init session handler
			session_start();

			// init session-based autoexport control
			if (!isset($_SESSION["EFWebCoreAutoExport"])) {
				$_SESSION["EFWebCoreAutoExport"]["order"] = [];
				$_SESSION["EFWebCoreAutoExport"]["total"] = 0;
				$_SESSION["EFWebCoreAutoExport"]["next"] = 0;

				foreach ($this->config->pages as $key => $page) {
					if (!is_external($page->uri)) {
						$_SESSION["EFWebCoreAutoExport"]["order"][] = $key;
						$_SESSION["EFWebCoreAutoExport"]["total"]++;
					}
				}
			}

			// prepend status line to output buffer after writing to file
			$ob = 
				"<h1 id=\"EFWebCoreAutoExport\">EFWebCoreAutoExport: " .
				round($_SESSION["EFWebCoreAutoExport"]["next"] / $_SESSION["EFWebCoreAutoExport"]["total"] * 100) .
				"%</h1>" .
				$ob;

			// set header to load next page in line
			if ($_SESSION["EFWebCoreAutoExport"]["next"] < $_SESSION["EFWebCoreAutoExport"]["total"]) {
				header (
					"Refresh: 1, url=" .
					$this->config->base .
					$_SESSION["EFWebCoreAutoExport"]["order"][$_SESSION["EFWebCoreAutoExport"]["next"]++] . 
					"?export"
				);
			}
			else {
				session_destroy();
			}
		}

		// finally, send buffer
		echo $ob;
	}

	/**
	 * Returns the page with the given key, or null on failure.
	 * @since 4.00
	 * @return stdObject the page object requested
	 */
	private function get_page(string $page_key) : ?stdClass	{
		$page_key = trim($page_key, "/");

		foreach ($this->config->pages as $key => $page) {
			if ($page_key === $key) {
				return $page;
			}
		}

		return null;
	}

	/**
	 * Writes the current output buffer to a file under config.staticOut.path and a 
	 * sub directory according to the page's path key.
	 * @since 4.00
	 */
	private function write_static_output() {
		// construct target path
		$path = 
		$this->config->staticOut->path . $this->page->key . "/";

		// ensure target path exists
		if (!file_exists($path)) {
			mkdir($path, 0755, true); 	// Warning: mkdir(): File exists
		}

		// construct target file name
		$file = "index.html";

		// write output cache to file
		// Note: using str_replace() due to the lack of mb_str_replace() assumes that
		// $this->config->base and $this->config->staticOut->targetBase will never contain
		// multibyte characters.
		if (file_put_contents (
			$path . $file,
			str_replace($this->config->base, $this->config->staticOut->targetBase, ob_get_contents())
		) === false) {
			debug("[warning] config.staticOut enabled, but file write failed.");
		}

		// scan all other files and directories for changes and copy them if necessary
		$exclude = [
			".",
			"..",
			".htaccess",
			"index.php",
			"core.php",
			"core.config.json",
			"updatepartners.php",
			trim($this->config->defaults->pagesDirectory, "/"),
			trim($this->config->staticOut->path, "/")
		];

		foreach (scandir(".") as $item) {
			$source = $item;
			$target = $this->config->staticOut->path . $item;

			if (!in_array($source, $exclude)) {
				if (!is_dir($source)) {
					copy($source, $target);
				}
				else {
					dircopy($source, $target);
				}
			}
		}

		// if home, copy pages/home/index.html to pages/index.html to catch ways to access this page, / and /home.
		if ($this->page->key === $this->config->defaults->rootPage) {
			copy($path . $file, $this->config->staticOut->path . $file);
		}
	}
}

/**
 * GLOBAL FUNCTIONS
 */

/**
 * Outputs any variable within <pre class="debug"> and some trace information in <h3> within.
 * @since 4.00
 */
function debug($var) {
	$trace = debug_backtrace(1);
	echo "<pre class=\"debug\">";
	echo "<h3>" . basename($trace[0]["file"]) . ":" . $trace[0]["line"] . "</h3>";
	var_dump($var);
	echo "</pre>";
}
	
// /**
//  * Retrieves the last modify timestamp of a directory, respecting recursion.
//  * @param string directory to retrieve last modify time for
//  * @return int last modified timestamp of the specified directory
//  * @since 4.00
//  */
// function dirmtime(string $path) : int {
// 	if (!file_exists($path))
// 		return 0;

// 	$last_timestamp = filemtime($path);

// 	if (!is_dir($path))
// 		return $last_timestamp;

// 	foreach (scandir($path) as $item) {
// 		if ($item != "." && $item != "..") {
// 			$mtime = filemtime($path . "/" . $item);

// 			if (is_dir($path . "/" . $item))
// 				$mtime = dirmtime($path . "/" . $item);

// 			if ($mtime > $last_timestamp)
// 				$last_timestamp = $mtime;
// 		}
// 	}

// 	return $last_timestamp;
// }

/**
 * Copies a directory and all its contents recursively.
 * @param string source directory
 * @param string destination directory
 * @since 4.00
 */
function dircopy(string $source, string $target) {
	if (!is_dir($source)) {
		echo "Warning: non-directory source passed to dircopy().";
		return;
	}

	$dir = opendir($source);

	if (!is_dir($target)) {
		mkdir($target);
	}

	while (($file = readdir($dir)) !== false) { 
		if ($file != "." && $file != "..") { 
			if (is_dir($source . "/" . $file)) {
				dircopy($source . "/" . $file, $target . "/" . $file);
			}
			else {
				copy($source . "/" . $file, $target . "/" . $file);
			}
			
		}
	}

	closedir($dir); 
}

/**
 * Substitute all occurances of {something} (as defined in $substitutions) within
 * a flat $subject and return the result.
 * 
 * Example:
 *    $subject = ["con" => "Eurofurence {number}"]
 *    $substitions = ["number" => "30"]
 *    substitute($subject, $substitions) -> ["con" => "Eurofurence 30"]
 * 
 * @param array $subject|object|string The flat target array on which to perform the substitions on.
 * @param array $substitions The flat array of string => string values to replace
 */
function substitute(array|object|string $subject, stdClass|array $substitutions): array|object|string {
	$substitutions = (array) $substitutions;

	$substitute = function (mixed $value) use (&$substitute, $substitutions): mixed {
		if (is_string($value)) {
			return preg_replace_callback(
				'/\{([^}]+)\}/',
				static fn (array $matches): string =>
					array_key_exists($matches[1], $substitutions)
						? (string) $substitutions[$matches[1]]
						: $matches[0],
				$value
			);
		}

		if (is_array($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = $substitute($item);
			}

			return $value;
		}

		if (is_object($value)) {
			foreach (get_object_vars($value) as $key => $item) {
				$value->{$key} = $substitute($item);
			}

			return $value;
		}

		return $value;
	};

	return $substitute($subject);
}

/**
 * Determines if an URI is external, e.g. if it starts with "http(s)://" or "www.".
 * @param string URI string, e.g. https://www.eurofurence.org (=> true) or home.php (=> false)
 * @return bool True, if URI starts with http(s):// or www.
 * @since 4.00
 */
function is_external(string $uri) : bool {
	return mb_ereg_match("(https?\:\/\/)|(www\.)", $uri, "");
}