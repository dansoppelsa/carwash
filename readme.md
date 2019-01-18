# Carwash

**Carwash** is a data scrubbing utility for **Laravel** applications

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
    ],
    'addresses' => [
        'street' => 'streetAddress',
        'city' => 'city',
        'zip' => 'postcode'
    ],
   'bios' => [
        'content' => 'sentences:2'   
    ]
];
``` 
**Carwash** uses the fabulous [Faker](https://github.com/fzaninotto/Faker) package under the hood to generate replacement data. Please refer to the Faker documentation for a complete list of [available formatters](https://github.com/fzaninotto/Faker#formatters).

More generally, the format of the **Carwash** config file is:
```php
<?php

return [
    '[TABLE_NAME]' => [
        '[COLUMN_NAME]' => '[Faker Formatter][:argument1,argument2]'
    ]
];
``` 

#### 3. Run Scrub Command

```php
php artisan carwash:scrub
```

### Other
Instead of passing a Faker Formatter as the value for each field in your **Carwash** config file, alternatively
you can set the field value to a Callable that returns the new field value. This closure will receive an instance of
**Faker** and the current value of the attribute being scrubbed.

```php
<?php

return [
    'users' => [
        'name' => function ($faker, $currentValue) {
            return "{$faker->firstName} {$faker->lastName}";
        },
        'bio' => new BioFormatter
    ]
];

class BioFormatter
{
    public function __invoke($faker)
    {
        return $faker->sentences(42);
    }
}
```

Callables can also replace entire table configurations allowing full control over what data is scrubbed and how.

```php
<?php

return [
    'users' => function ($faker, $user) {
        $firstName = $faker->firstName;
        $lastName = $faker->lastName;
    
        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $firstName . "." . $lastName. "@" . $faker->safeEmailDomain,
            'phone' => substr($user['phone'], 0, 3) . "-555-" . $faker->randomNumber(4)
        ];
    },
];
```
