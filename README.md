# Copy Pages
Dashboard widget for Kirby panel that allows users to copy (clone) existing pages.

![Screenshot](screenshot.png)

## Installation

### Requirements

-	PHP 5.4.0+
-	Kirby 2.3.0+

### Kirby CLI (untested)

```
cd path/to/kirby
kirby plugin:install medienbaecker/kirby-copy-pages
```

This should install the plugin at `site/plugins/copy-pages`.

### Git Submodule

```
cd path/to/kirby
git submodule add https://github.com/medienbaecker/kirby-copy-pages.git site/plugins/copy-pages
```

### Manual download

1. [Download](https://github.com/medienbaecker/kirby-copy-pages/archive/master.zip) a ZIP archive of this repository
2. Extract the contents of `master.zip`
3. Rename the extracted folder to `copy-pages` and move it into the `site/plugins/` directory in your Kirby project