<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">

# Framework zaacom

php version : **8.1**

* Menu
    * Installation [<i class="fas fa-hashtag"></i>](#installation)
    * Attributes [<i class="fas fa-hashtag"></i>](#attributes)
      * Controller [<i class="fas fa-hashtag"></i>](#attrController)
      * Route [<i class="fas fa-hashtag"></i>](#attrRoute)

## <div id="installation">Installation</div>

1. Run this command
```cmd
Zaacom create project
```
2. Complete the processes and dow you are ready, **have fun!**

## <div id="attributes">Attributes</div>


### <div id="attrController">Controller</div>

```php
use Zaacom\attributes\Controller;


#[Controller]
class MainController extends \Zaacom\controllers\BaseController {
    ...
}
```

### <div id="attrRoute">Route</div>

```php
use Zaacom\attributes\Controller;
use Zaacom\attributes\Route;
use Zaacom\routing\RouteMethodEnum;

#[Controller]
class MainController extends \Zaacom\controllers\BaseController {

    #[Route]
    public function index() {
        //Name: GET.MainController.index
        //path: MainController/index
        //method: GET
    }
    
    #[Route(method: RouteMethodEnum::POST)]
    public function index2() {
        //Name: POST.MainController.index2
        //path: MainController/index2
        //method: POST
    }
    
    #[Route(path: 'home')]
    public function index3() {
        //Name: GET.MainController.index3
        //path: home
        //method: GET
    }
    
    #[Route(name: 'home')]
    public function index4() {
        //Name: home
        //path: MainController/index4
        //method: GET
    }
}
```

```php
use Zaacom\attributes\Controller;
use Zaacom\attributes\Route;
use Zaacom\routing\RouteMethodEnum;

#[Controller]
#[Route(path: 'main')]
class MainController extends \Zaacom\controllers\BaseController {

    #[Route]
    public function index() {
        //Name: GET.MainController.index
        //path: path/index
        //method: GET
    }
    
    #[Route(path: 'home')]
    public function index2() {
        //Name: GET.MainController.index2
        //path: path/home
        //method: GET
    }
}
```

```php
use Zaacom\attributes\Controller;
use Zaacom\attributes\Route;
use Zaacom\routing\RouteMethodEnum;

#[Controller]
#[Route(name: 'main')]
class MainController extends \Zaacom\controllers\BaseController {

    #[Route]
    public function index() {
        //Name: main.index
        //path: path/index
        //method: GET
    }
    
    #[Route(name: 'home')]
    public function index2() {
        //Name: main.home
        //path: MainController/index2
        //method: GET
    }
}
```


### <div id="adminRoute">Admin route</div>

> `/Admin/Folders/generate` 
> 
> Generates the necessary folders

> `/Admin/Objects/index`
> 
> Generates objects like tables in the databases specified in the file `/.env`
