# Pipit Pinion

Pipit Pinion is a [Perch Feather](https://docs.grabaperch.com/api/feathers/) that helps you manage front-end assets such as CSS and Javascript through Perch.

## Installation
* Download the latest version of Pinion.
* Unzip the download
* Place the `pipit_pinion` folder in `perch/addons/feathers`
* Add Pinion to your `perch/config/feathers.php`
* Enable Feathers in your `perch/config/config.php`

### Adding Pinion to `feathers.php`
If you don't have the file `perch/config/feathers.php`, create it and add:

```php
<?php
    include(PERCH_PATH.'/addons/feathers/pipit_pinion/runtime.php');
?>
```


### Enabling Feathers
You also need to enable Feathers in your `perch/config/config.php`

```php
define('PERCH_FEATHERS', true);
```


## Configuration
You can define default folders in `perch/addons/feathers/pipit_pinion/config.php` for Pinion to look for your CSS and Javascript files.

| Name                        | Value                                                                                                                                              |
|-----------------------------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| PIPIT_PINION_ASSETS_DIR     | The name of the directory where your production assets are. The path is relative to root.                                                                                       |
| PIPIT_PINION_ASSETS_DEV_DIR | The name of the directory where your development assets are. Pinion uses this directory when `PERCH_PRODUCTION_MODE` is set to`PERCH_DEVELOPMENT`. The path is relative to root. |

The below example uses a folder called `assets` for production and `src` for development.

```php
define('PIPIT_PINION_ASSETS_DIR', 'assets');
define('PIPIT_PINION_ASSETS_DEV_DIR', 'src');
```

In this example, CSS files need to be in `assets/css` and `src/css`, and Javascript files need to be in `assets/js` and `src/js`. This way you can use `perch_get_css()` and `perch_get_javascript()` to link all the files from the respective folder to your document by default.

```php
 .
 ├── assets/
 │   ├── js/
 │   └── css/
 │   
 ├── src/
 │   ├── js/
 │   └── css/
 │   
 └── perch
```

The `perch_get_css()` and `perch_get_javascript()` functions have several options you can use giving you a lot of flexibility to control what gets inserted into your document and in what order. For more details check the [Function Reference](https://grabapipit.com/pipits/feathers/pinion/docs/functions) page.