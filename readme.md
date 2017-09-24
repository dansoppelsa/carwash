# Carwash

**Carwash** is a data scrubbing utility for Laravel applications

## Installation

#### Install via composer
```
composer require dansoppelsa/carwash
```

## Usage
#### 1. Publish default config file
```
php artisan vendor:publish
```

#### 2. Edit config file at `config/carwash.php` for your application

```php
<?php

return [
    'users' => [
        'first_name' => 'firstName',
        'last_name' => 'lastName',
        'email' => 'safeEmail'
    ]
];
``` 
**Carwash** uses the fabulous [Faker](https://github.com/fzaninotto/Faker) package under the hood to generate replacement data. Please refer to the Faker documentation for a complete list of [available formatters](https://github.com/fzaninotto/Faker#formatters).

More generally, the format of the **Carwash** config file is:
```php
<?php

return [
    '[TABLE_NAME]' => [
        '[COLUMN_NAME]' => '[Faker Formatter]'
    ]
];
``` 

#### 3. Run Scrub Command

```php
php artisan carwash:scrub
```
