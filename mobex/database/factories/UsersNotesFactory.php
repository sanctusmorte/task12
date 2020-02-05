<?php

use Faker\Generator as Faker;

$factory->define(App\UsersNote::class, function (Faker $faker) {
    return [
     	'user_id' => '2',
        'first_name' => str_random(4,10),
        'last_name' => str_random(5,12),
        'phone' => str_random(10),
        'observations' => str_random(50,160),        
    ];
});
