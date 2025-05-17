<?php
/**
 * Eurofurence Website Core Component
 * Includes debug(), dirmtime(), dircopy() and is_external() as global functions
 * @author	draconigen@gmail.com
 * @since 	11/2015
 * @version	4.1
 * @license	MIT
 */
class EFWebCore
{
	public object $current;
	public string $base;

	public ?object $config;
	private string $path;

	public function __construct(string $configfile)
	{
		// load and parse config
		$this->config = json_decode(file_get_contents($configfile), false);
		if (is_null($this->config)) 
		{
			die("Failed to parse " . $configfile . ", reason: " . json_last_error_msg());
		}

		// clear file stat cache
		clearstatcache();

		// ensure correct path settings format
		$this->config->staticOut->path = trim($this->config->staticOut->path, "/") . "/";
		$this->config->staticOut->targetBase = trim($this->config->staticOut->targetBase, "/") . "/";
		$this->config->defaults->pagesDirectory = trim($this->config->defaults->pagesDirectory, "/") . "/";

		// replace EF number in base url
		$this->config->staticOut->targetBase = str_replace(
			'{number}',
			$this->config->convention->number,
			$this->config->staticOut->targetBase
		);

		// subtract directory from request URI
		$request_uri = substr($_SERVER["REQUEST_URI"], strlen(dirname($_SERVER["SCRIPT_NAME"])));

		// determine page property key
		$this->path =
			in_array($request_uri,["", "/"]) ?
			$this->config->defaults->rootPage :
			trim(parse_url($request_uri, PHP_URL_PATH), "/");

		// construct base url
		$this->base = $this->get_base();

		// select page from config
		if (property_exists($this->config->pages, $this->path))
		{
			$this->current = $this->config->pages->{$this->path};
			$this->current->key = $this->path;
		}
		else
		{
			$this->handle_not_found();
		}

		// override with NotAccessiblePage if necessary 
		if (!$this->current->accessible)
		{
			$this->handle_not_accessible();
		}

		// construct title meta tag content
		$this->current->title = $this->resolve_convention_properties
		(
			$this->config->defaults->titlePrefix .
			$this->current->title .
			$this->config->defaults->titleSuffix
		);

		// copy all config.convention properties to current page
		foreach (get_object_vars($this->config->convention) as $key => $value)
		{
			$this->current->$key = $value;
		}

		// construct description meta tag content
		$this->current->description = $this->resolve_convention_properties($this->current->description);

		// construct keywords meta tag content
		$this->current->keywords = $this->resolve_convention_properties
		(
			trim($this->config->defaults->keywords . ", " . $this->current->keywords, ", ")
		);

		// construct OGP image
		$this->current->ogpImage =
			$this->base . 
			$this->config->defaults->ogpImagePrefix .
			(empty($this->current->ogpImage) ? $this->config->defaults->ogpImage : $this->current->ogpImage);
		
		// determine OGP image size
		$ogpImageSize = getimagesize($this->current->ogpImage);
		$this->current->ogpImageWidth = $ogpImageSize[0];
		$this->current->ogpImageHeight = $ogpImageSize[1];

		// construct robots meta tag content
		if (empty($this->current->robots))
		{
			$this->current->robots = $this->config->defaults->robots;
		}

		// start output caching
		ob_start();
	}
	
	/**
	 * Returns the full url to the current page.
	 * @since 3.12
	 * @return string full url, e.g. https://www.eurofurence.org/EF25/artshow/bidding
	 */
	public function get_full_url() : string
	{
		return $this->base . $this->path;
	}

	/**
	 * Returns an array of data to populate schema.org breadcrumb lists.
	 * @since 3.00
	 * @return array of objects containing page data
	 */
	public function get_breadcrumb_data()
	{
		$results = [];

		// return empty in case of root page
		if ($this->path === $this->config->defaults->rootPage)
		{
			return $results;
		}

		$path = "";
		foreach (explode("/", $this->path) as $key)
		{
			// construct key path
			$path .= $key . "/";

			if (!property_exists($this->config->pages, trim($path, "/")))
			{
				$path = $this->config->defaults->notFoundPage;
			}
			
			// append desired data from page object
			$ret = new stdClass();
			$ret->name = $this->get_page($path)->menuText;
			$ret->url = trim($this->base . $path, "/");

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
	public function get_menu() : string
	{
		// copy config.menu.categoryOrder as keys to empty arrays
		$categorized_pages = array_fill_keys($this->config->menu->categoryOrder, []);

		// sort pages into categories ($categorized_pages)
		foreach ($this->config->pages as $key => $page)
		{
			if ($page->inMenu)
			{
				// if category is not listed in config.menu.categoryOrder, append to end
				if (!array_key_exists($page->category, $categorized_pages))
				{
					$categorized_pages[$page->category] = [];
				}

				$categorized_pages[$page->category][$key] = $page;
			}
		}

		// generate categories html string
		$categories_html = "";
		foreach ($categorized_pages as $category_title => $pages)
		{
			// insert category title
			$category_html = mb_ereg_replace("\{title\}", $category_title, $this->config->menu->templates->category);
			
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
					($this->current !== $page? "" : $this->config->menu->ifActiveClass),
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
				$item = mb_ereg_replace("\{menuText\}", $page->menuText, $item);

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
	 * Includes the content of the current page.
	 * @since 1.00
	 */
	public function load_content()
	{
		include($this->config->defaults->pagesDirectory . $this->current->uri);
	}

	/**
	 * Retrieves the content (by uri) of a page or the current page by default.
	 * @since 1.0
	 * @param stdObject page object
	 */
	public function get_content($page = null)
	{
		// default missing parameters
		if (is_null($page))
		{
			$page = $this->current;
		}

		// check for accessibility
		if (!$page->accessible)
		{
			$page = $this->config->pages->{$this->config->defaults->notAccessiblePage};
		}

		// pause output buffering and stash buffer content
		$ob = ob_get_contents();
		ob_end_clean();

		// start new output buffer and obtain page content or redirect
		ob_start();
		if (!is_external($page->uri))
		{
			include($this->config->defaults->pagesDirectory . $page->uri);
		}
		else if ($this->config->defaults->externalEmbed)
		{
			echo file_get_contents($page->uri);
		}
		else
		{
			header("Location: " . $page->uri);
			return "Redirecting to: " . $page->uri;
		}
		
		// end output buffering and store buffer content for returning
		$ret = ob_get_contents();
		ob_end_clean();

		// reinstate prior output buffering
		ob_start();
		echo $ob;

		// return buffered page content
		return $ret;
	}

	/**
	 * If config.staticOut is enabled, write output cache to file under
	 * config.staticOut.path. If $_GET["export"] is set, then automate each visiting page.
	 * @since 4.00
	 */
	public function end()
	{
		// if static output is enabled, write static file
		if ($this->config->staticOut->enabled)
		{
			$this->write_static_output();
		}

		if ($this->config->staticOut->lastModifiedEnabled)
		{
			// do not trigger on error pages, to avoid adding missing resources to lastModifiedMapFile
			if (!in_array($this->current->key, 
					[
						$this->current->key === $this->config->defaults->notFoundPage,
						$this->current->key === $this->config->defaults->notAccessiblePage
					]
				))
			{

				// read last modified map file
				$map = json_decode(file_get_contents($this->config->staticOut->lastModifiedMapFile), false);
				if (is_null($map)) 
				{
					debug("[warning] Failed to parse " . $this->config->staticOut->lastModifiedMapFile . ", reason: " . json_last_error_msg());
				}

				// read last modified timestamp from file system
				$timestamp = filemtime($this->config->defaults->pagesDirectory . $this->config->pages->{$this->path}->uri);

				// if timestamp not yet present or outdated, update it
				if 
				(
					!property_exists($map, $this->path) ||
					$map->{$this->path} !== $timestamp
				)
				{
					$map->{$this->path} = $timestamp;
					if (file_put_contents($this->config->staticOut->lastModifiedMapFile, json_encode($map, JSON_PRETTY_PRINT)) === false)
					{
						debug("[warning] staticOut.lastModifiedEnabled behavior enabled, but writing to {$this->config->staticOut->lastModifiedMapFile} failed.");
					}
				}
			}
		}

		// end output buffering and obtain buffer content
		$ob = ob_get_contents();
		ob_end_clean();

		// if GET export is set, trigger mechanic to auto-visit every accessible page
		if (isset($_GET['export']))
		{
			// init session handler
			session_start();

			// init session-based autoexport control
			if (!isset($_SESSION["EFWebCoreAutoExport"]))
			{
				$_SESSION["EFWebCoreAutoExport"]["order"] = [];
				$_SESSION["EFWebCoreAutoExport"]["total"] = 0;
				$_SESSION["EFWebCoreAutoExport"]["next"] = 0;

				foreach ($this->config->pages as $key => $page)
				{
					if (!is_external($page->uri))
					{
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
			if ($_SESSION["EFWebCoreAutoExport"]["next"] < $_SESSION["EFWebCoreAutoExport"]["total"])
			{
				header
				(
					"Refresh: 1, url=" .
					$this->base .
					$_SESSION["EFWebCoreAutoExport"]["order"][$_SESSION["EFWebCoreAutoExport"]["next"]++] . 
					"?export"
				);
			}
			else 
			{
				session_destroy();
			}
		}

		// finally, send buffer
		echo $ob;
	}

	/**
	 * Sets current page to config.notFoundPage and headers to reflect status 404.
	 */
	private function handle_not_found()
	{
		$this->current = $this->config->pages->{$this->config->defaults->notFoundPage};
		$this->current->key = $this->config->defaults->notFoundPage;
		header('HTTP/1.0 404 Not Found', true, 404);
	}

	/**
	 * Sets current page to config.notAccessiblePage and headers to refelect status 501.
	 */
	private function handle_not_accessible()
	{
		$this->current = $this->config->pages->{$this->config->defaults->notAccessiblePage};
		$this->current->key = $this->config->defaults->notAccessiblePage;
		header('HTTP/1.0 501 Not Implemented', true, 501);
	}

	/**
	 * Generate appropriate content for the <base> tag. HTTP/S is prefixed according to how 
	 * the website was accessed.
	 * @since 3.00
	 * @return string base url
	 */
	private function get_base() : string
	{
		$mode = 
			((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 
			'https://' : 'http://';

		return str_replace('index.php', '', $mode . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);	
	}

	/**
	 * Writes the current output buffer to a file under config.staticOut.path and a 
	 * sub directory according to the page's path key.
	 * @since 4.00
	 */
	private function write_static_output()
	{
		// construct target path
		$path = 
		$this->config->staticOut->path . $this->path . "/";

		// ensure target path exists
		if (!file_exists($path))
		{
			mkdir($path, 0755, true); 	// Warning: mkdir(): File exists
		}

		// construct target file name
		$file = "index.html";

		// write output cache to file
		// Note: using str_replace() due to the lack of mb_str_replace() assumes that
		// $this->base and $this->config->staticOut->targetBase will never contain
		// multibyte characters.
		if (file_put_contents
		(
			$path . $file,
			str_replace($this->base, $this->config->staticOut->targetBase, ob_get_contents())
		) === false)
		{
			debug("[warning] config.staticOut enabled, but file write failed.");
		}

		// scan all other files and directories for changes and copy them if necessary
		$exclude = 
		[
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

		foreach (scandir(".") as $item) 
		{
			$source = $item;
			$target = $this->config->staticOut->path . $item;

			if (!in_array($source, $exclude) && dirmtime($source) > dirmtime($target))
			{
				if (!is_dir($source))
				{
					copy($source, $target);
				}
				else
				{
					dircopy($source, $target);
				}
			}
		}

		// if home, copy pages/home/index.html to pages/index.html to catch ways to access this page, / and /home.
		if ($this->path === $this->config->defaults->rootPage)
		{
			copy($path . $file, $this->config->staticOut->path . $file);
		}
	}

	/**
	 * Iterates through all config.convention keys and replaces them in a given string
	 * within curly brackes, e.g. "This is number {number}." -> "This is number 25."
	 * @param string input string that may contain config.convention keys in curly brackets
	 * @return string processed input string with all matching keys replaced.
	 * @since 4.00
	 */
	private function resolve_convention_properties(string $text) : string
	{
		foreach ($this->config->convention as $key => $value)
		{
			$text = mb_ereg_replace("\{" . $key . "\}", $value, $text, "r");
		}

		return $text;
	}

	/**
	 * Returns the page with the given key, or null on failure.
	 * @since 4.00
	 * @return stdObject the page object requested
	 */
	private function get_page(string $page_key) : ?stdClass
	{
		$page_key = trim($page_key, "/");

		foreach ($this->config->pages as $key => $page)
		{
			if ($page_key === $key)
			{
				return $page;
			}
		}

		return null;
	}
}

/**
 * GLOBAL FUNCTIONS
 */

/**
 * Outputs any variable within <pre class="debug"> and some trace information in <h3> within.
 * @since 4.00
 */
function debug($var)
{
	$trace = debug_backtrace(1);
	echo "<pre class=\"debug\">";
	echo "<h3>" . basename($trace[0]["file"]) . ":" . $trace[0]["line"] . "</h3>";
	var_dump($var);
	echo "</pre>";
}
	
/**
 * Retrieves the last modify timestamp of a directory, respecting recursion.
 * @param string directory to retrieve last modify time for
 * @return int last modified timestamp of the specified directory
 * @since 4.00
 */
function dirmtime(string $path) : int
{
	if (!file_exists($path))
	{
		return 0;
	}

	$last_timestamp = filemtime($path);

	if (!is_dir($path))
	{
		return $last_timestamp;
	}

	foreach (scandir($path) as $item)
	{
		if ($item != "." && $item != "..")
		{
			$mtime = filemtime($path . "/" . $item);

			if (is_dir($path . "/" . $item))
			{
				$mtime = dirmtime($path . "/" . $item);
			}

			if ($mtime > $last_timestamp)
			{
				$last_timestamp = $mtime;
			}
		}
	}

	return $last_timestamp;
}

/**
 * Copies a directory and all its contents recursively.
 * @param string source directory
 * @param string destination directory
 * @since 4.00
 */
function dircopy(string $source, string $target)
{
	if (!is_dir($source))
	{
		echo "Warning: non-directory source passed to dircopy().";
		return;
	}

	$dir = opendir($source);

	if (!is_dir($target))
	{
		mkdir($target);
	}

	while (($file = readdir($dir)) !== false)
	{ 
		if ($file != "." && $file != "..")
		{ 
			if (is_dir($source . "/" . $file))
			{
				dircopy($source . "/" . $file, $target . "/" . $file);
			}
			else
			{
				copy($source . "/" . $file, $target . "/" . $file);
			}
		}
	}

	closedir($dir); 
}

/**
 * Determines if an URI is external, e.g. if it starts with "http(s)://" or "www.".
 * @param string URI string, e.g. https://www.eurofurence.org (=> true) or home.php (=> false)
 * @return bool True, if URI starts with http(s):// or www.
 * @since 4.00
 */
function is_external(string $uri) : bool
{
	return mb_ereg_match("(https?\:\/\/)|(www\.)", $uri, "");
}