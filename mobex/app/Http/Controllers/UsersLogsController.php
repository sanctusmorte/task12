<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UsersLog;

class UsersLogsController extends Controller
{
    /**
     * Display user's logs on the page
     *
     * @return view
     */

    public function index()
    {
    	if (Auth::user()) {
    		$currentuserid = Auth::user()->id;
    		$logs = $this->getLogEventsOfUserFromDb($currentuserid);
    	} else {
    		$logs = [];
    	}
    	
    	return view('logs', compact('logs'));
    }  

    /**
     * Get all user's logs from DB by user's id
     *
     * @param $currentuserid - id of current logged user
     * 
     * @return array
     */    

    public function getLogEventsOfUserFromDb($currentuserid)
    {
		$logs = UsersLog::where('user_id', $currentuserid)->orderBy('id', 'desc')->paginate(20);

        return $logs;
    }    
}
