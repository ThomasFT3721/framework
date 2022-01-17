<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">

# Framework zaacom

php version : **8.0**

* Menu
    * Installation [<i class="fas fa-hashtag"></i>](#installation)
    * Route [<i class="fas fa-hashtag"></i>](#route)
      * Add route [<i class="fas fa-hashtag"></i>](#addRoute)
      * Admin route [<i class="fas fa-hashtag"></i>](#adminRoute)

## <div id="installation">Installation</div>

1. Add this into `/composer.json` file
```json
"autoload": {
  "psr-4": {
    "Zaacom\\": "vendor/zaacom/framework/src"
  }
},
"repositories": [
  {
    "type": "vcs",
    "url": "https://github.com/ThomasFT3721/framework"
  }
],
"require": {
  "zaacom/framework": "dev-master"
}
```
2. Run command `composer install`.
3. Go to the folder `/vendor/zaacom/framework` and copy the `.htaccess`, `index.php` files to `/`.
4. Run command `composer dump`.
5. Start the server and go to `/Admin/Folders/generate`.
5. After go to `/Admin/Objects/index`, select the objects to be generated.
6. Add this into `/composer.json` file
```json
"autoload": {
  ...,
  "classmap": [
    "models",
    "controllers",
    "tools"
  ]
}
```
7. Run command `composer dump`.
8. Now you are ready, **have fun!**

## <div id="route">Route</div>


### <div id="addRoute">Add route</div>

To add a route you must create a PHP file in the folder `/routes/`.

In this file add the following code: 

```php
<?php

Zaacom\routing\Route::get("/", [ExampleController::class, "methodName"], ["name" => "rooute_name"]);
Zaacom\routing\Route::post("/", [ExampleController::class, "methodName"], ["name" => "rooute_name"]);
Zaacom\routing\Route::delete("/", [ExampleController::class, "methodName"], ["name" => "rooute_name"]);
Zaacom\routing\Route::patch("/", [ExampleController::class, "methodName"], ["name" => "rooute_name"]);
Zaacom\routing\Route::put("/", [ExampleController::class, "methodName"], ["name" => "rooute_name"]);
```

### <div id="adminRoute">Admin route</div>

> `/Admin/Folders/generate` 
> 
> Generates the necessary folders

> `/Admin/Objects/index`
> 
> Generates objects like tables in the databases specified in the file `/.env`
