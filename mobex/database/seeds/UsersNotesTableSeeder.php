<?php

use Illuminate\Database\Seeder;

class UsersNotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    factory(App\UsersNote::class, 100)->create()->each(function ($u) {
	        
	    });    	       
    }
}
