<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\UsersNote;
use Auth;

class Search extends Component
{
	public $searchTerm;

	public $notes;

    public function render()
    {
    	
    	//$this-$searchTerm = '%' . $this->$searchTerm . '%';
    	//$this->notes = UsersNote::where('first_name', 'ilike', $searchTerm)->get();
        return view('notes');

        
    }

    public function getNotesOfUserFromDb($currentuserid)
    {
		$notes = UsersNote::where('user_id', $currentuserid)->orderBy('id', 'desc')->paginate(20);

        return $notes;
    }    
}
