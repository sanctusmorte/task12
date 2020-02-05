<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\UsersNote;
use App\UsersLog;

class UsersNotesController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Users Notes Contoller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for displaying user's notes on the page and for adding / editing / deleting notes
    |
    */


    /**
     * Display user's notes on the page
     *
     * @return view
     */

    public function index()
    {
    	if (Auth::user()) {
    		$currentuserid = Auth::user()->id;
    		$notes = $this->getNotesOfUserFromDb($currentuserid);
    	} else {
    		$notes = [];
    	}
    	
    	return view('notes', compact('notes'));
    }

    /**
     * Get all user's notes from DB by user's id
     *
     * @param $currentuserid - id of current logged user
     * 
     * @return array
     */    

    public function getNotesOfUserFromDb($currentuserid)
    {
		$notes = UsersNote::where('user_id', $currentuserid)->orderBy('id', 'desc')->paginate(15);

        return $notes;
    }

    /**
     * Update the user's note by user id and note id
     *
     * @param $request - an array by post request from the page
     * 
     * @return response - a json response to the page
     */      

    public function saveNote(Request $request) 
    {
    	if (Auth::user()) {
    		$currentuserid = Auth::user()->id;

    		// get data from request
    		$noteId = (int)$request->data['fieldId'];
    		$firstName = $request->data['firstName'];
    		$lastName = $request->data['lastName'];
    		$phone = $request->data['phone'];
    		$observations = $request->data['observations'];

    		if (strlen($firstName) == 0 or strlen($lastName) == 0 or strlen($phone) == 0 or strlen($observations) == 0) {
    			$json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'The field/fields cannot be empty!'
    			];
    			return response()->json([$json_response]);    			
    		}

    		// save the note
    		$editNote = UsersNote::where('id', $noteId)->where('user_id', $currentuserid)
    					->update(
    						[
    							'first_name' => $firstName,
    							'last_name' => $lastName,
    							'phone' => $phone,
    							'observations' => $observations,
    						]
    					);

    		// check if the note has changed
    		if ($editNote === 1) {

    			// create new log event
    			$desc = 'Изменил запись # '.$noteId.'';
    			$time = now();
    			$log = $this->addEventLogInDbForUser($currentuserid, $desc, $time);

    			$json_response = [
    				'response' => '200',
    				'status' => 'ok',
    				'message' => 'The note # '.$noteId.' has successfully changed!'
    			];
    		} else {
    			$json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'The note # '.$noteId.' was not changed. Maybe you didn’t make changes fot this note or some error has occurred.'
    			];
    		}

    	// if user not logged	
    	} else {
    		    $json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'You are not logged. You can not change some note. Please login.'
    			];
    	}

    	return response()->json([$json_response]);
    }

    /**
     * Create new note
     *
     * @param $request - an array by post request from the page
     * 
     * @return response - a json response to the page
     */      

    public function createNote(Request $request) 
    {
    	if (Auth::user()) {
    		$currentuserid = Auth::user()->id;

    		// get data from request
    		$firstName = $request->data['firstName'];
    		$lastName = $request->data['lastName'];
    		$phone = $request->data['phone'];
    		$observations = $request->data['observations'];

    		if (strlen($firstName) == 0 or strlen($lastName) == 0 or strlen($phone) == 0 or strlen($observations) == 0) {
    			$json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'The field/fields cannot be empty!'
    			];
    			return response()->json([$json_response]);    			
    		}

    		$createNote = new UsersNote;
        	$createNote->user_id = $currentuserid;
        	$createNote->first_name = $firstName;
        	$createNote->last_name = $lastName;
        	$createNote->phone = $phone;
        	$createNote->observations = $observations;
        	$createNote->save();

        	$createNote = json_decode($createNote, 1);

    		// check if the note has created
    		if ($createNote['id']) {

    			// create new log event
    			$desc = 'Создал запись # '.$createNote['id'].'';
    			$time = now();
    			$log = $this->addEventLogInDbForUser($currentuserid, $desc, $time);

    			$json_response = [
    				'response' => '200',
    				'status' => 'ok',
    				'message' => 'The note # '.$createNote['id'].' has successfully created!'
    			];
    		} else {
    			$json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'The note was not created. Maybe you didn’t make changes fot this note or some error has occurred.'
    			];
    		}

    	// if user not logged	
    	} else {
    		    $json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'You are not logged. You can not create some note. Please login.'
    			];
    	}

    	return response()->json([$json_response]);
    }    


    /**
     * Delete the user's note by user id and note id
     *
     * @param $request - an array by post request from the page
     * 
     * @return response - a json response to the page
     */      

    public function deleteNote(Request $request) 
    {
    	if (Auth::user()) {
    		$currentuserid = Auth::user()->id;

    		// get data from request
    		$noteId = (int)$request->data['fieldId'];

    		// update the note
    		$deleteNote = UsersNote::where('id', $noteId)->where('user_id', $currentuserid)->delete();

    		// check if the note has changed
    		if ($deleteNote === 1) {

    			// create new log event
    			$desc = 'Удалил запись # '.$noteId.'';
    			$time = now();
    			$log = $this->addEventLogInDbForUser($currentuserid, $desc, $time);

    			$json_response = [
    				'response' => '200',
    				'status' => 'ok',
    				'message' => 'The note # '.$noteId.' has successfully deleted!'
    			];
    		} else {
    			$json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'The note # '.$noteId.' was not deleted. Try later'
    			];
    		}

    	// if user not logged
    	} else {
    		    $json_response = [
    				'response' => '404',
    				'status' => 'false',
    				'message' => 'You are not logged. You can not change some note. Please login.'
    			];
    	}

    	return response()->json([$json_response]);
    }    


    /**
     * Create a new log event for each user's action
     *
     * @param $currentuserid - id of logged user
     * @param $desc - the description of the event (delete/edit/add note)
     * 
     * @return void
     */

    public function addEventLogInDbForUser($currentuserid, $desc, $time)
    {
    	$log = new UsersLog;
    	$log->user_id = $currentuserid;
    	$log->desc = $desc;
    	$log->created_at = $time;
    	$log->save();
    }
}
