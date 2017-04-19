# FashionGuide Oauth2 PHP SDK
  
該套件提供 FashionGuide Oauth2 取得資料

## 版本需求

- PHP 7.0（含）以上
- Laravel 5.3（含）以上

## 安裝

1. 透過 `composer` 安裝

`composer require fashionguide/oauth2`

2. 加入 provider

    `config/app.php`
    ```php
    'providers' => [
        \FashionGuide\Oauth2\Providers\ServiceProvider::class,
    ]
    ```
    
3. 加入 alias

    `config/app.php`
    
    ```php
    'aliases' => [
       'FG' => \FashionGuide\Oauth2\Facades\FashionGuide::class,
    ]
    ```
    
## API Document

// todo

## Usage

### 取得 user 資料

1. 先取得登入網址

```php
<?php

use \FashionGuide\Oauth2\FashionGuide;

public function index(FashionGuide $fg)
{
    $fg->getLoginUrl();
    return view('view', ['loginUrl' => $fg]);
} 

```

2. 設定 callback url 取得 authorization code，並且透過 sdk 取得資料

```php
<?php

use \FashionGuide\Oauth2\FashionGuide;
use FashionGuide\Oauth2\Exceptions\RequestException;

public function callback(FashionGuide $fg)
{
    try {
        $user = $fg->get('/member/me');
    } catch (RequestException $e) {
        
    }
}
```

---

### RequestException

