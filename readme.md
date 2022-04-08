## Very basic remote CSS downloader

### **Usage** for PHP CLI (space separated parameters)

Usage: `php  ./parse.php remote_css_url [filename_find] [filename_replace]`

* arg-1: (required) URL of CSS
* arg-2: (otional) filename text search
* arg-3: (otional) filename text replacement

### Example 1 (minimal)

`php ./parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css`

This will download ds.css into the current directory and any referenced urls to css assets into their respective directories (images, fonts, icons)

### Optional - Rename Downloaded Files

* arg-2 can be a string or a comma separated list
* arg-3 can be a string or a comma separated list

This uses `str_replace([find,strings], [replace,string], $subject)`

### Example 2

Rename downloaded files from `*sourcesanspro*.*` to `*dgtepro*.*`

`php ./parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcesanspro dgtepro`

This will download ds.css into the current directory and any referenced urls to css assets into their respective directories (images, fonts, icons), replacing sourcesanspro with dgtepro in css asset filenames.

### Example 3

Rename downloaded files from *sourcesanspro*.* to *sanspro*.* and *sourcecodepro*.* to *codepro*.*

`php ./parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcesanspro,sourcecodepro sanspro,codepro`

As above, replacing sourcesanspro and sourcecodepro with sanspro and codepro respectively.

### Example 4

Remove sourcesanspro and sourcecodepro from all filenames

`php ./parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcesanspro,sourcecodepro`

As above, **removing** sourcesanspro and sourcecodepro from css asset filenames

## Or run with docker

#### Build

`docker build -t css-asset-dl .`

#### Run - docker will fire up the container, download and rewrite the CSS into a folder (css) in the current host directory.

`docker run -it --name css-asset-dl -v ${pwd}/css:/usr/src/css_asset_dl/css --rm css-asset-dl php parse.php https://ds.fusioncharts.com/2.0.42/css/ds.css sourcecode,sourcesans code,sans`


