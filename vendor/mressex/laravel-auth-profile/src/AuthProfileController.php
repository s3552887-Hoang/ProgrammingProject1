<?php

namespace MrEssex\LaravelAuthProfile;

use App\User;
use App\Http\Controllers\Controller;
use Session;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Validator;

class AuthProfileController extends Controller
{

    /**
     * Add auth middleware upon initialisation.
     *
     * @author Kyle Essex
     */
    public function __construct()
    {
        // Allow access to only authenticated users
        $this->middleware('web');
        $this->middleware('auth'); // Some reason adding both middleware is the only way to go!
    }

    /**
     * Get the current user profile.
     *
     * @return View
     * @author Kyle Essex
     */
    public function viewCurrentUserProfile(Request $request)
    {
        // Get the current authenticated user object
        $user = User::find($request->user()->id);
        $locations=DB::table('jobs')->distinct()->select('location')->get();
        $classifications=DB::table('jobs')->distinct()->select('classification')->get();
        $workTypes=DB::table('jobs')->distinct()->select('workType')->get();

        return view('laravelauthprofile::viewprofile', ['user' => $user], compact('locations', 'classifications', 'workTypes')) ;
    }

    /**
     * Validate any alterations and save them.
     *
     * @param Request $request
     * @return back with errors
     * @author Kyle Essex
     */
    public function editCurrentUserProfile(Request $request)
    {
        // Find the current authenticated user object
        $user = User::find($request->user()->id);

        // if input name exists in the request replace name in the user object
        if ($request["username"]) {
            $this->validate($request, [
                'username' => 'max:255|required|unique:users'
            ]);
            $user->username = $request["username"];
        }

        if ($request["firstname"]) {
            $this->validate($request, [
                'firstname' => 'max:255|required'
            ]);
            $user->firstname = $request["firstname"];
        }

        if ($request["lastname"]) {
            $this->validate($request, [
                'lastname' => 'max:255|required'
            ]);
            $user->lastname = $request["lastname"];
        }

        // if input password exists in the request replace password in the user object
        if ($request["password"]) {
            $this->validate($request, [
                'password' => 'min:8|required'
            ]);
            $user->password = bcrypt($request["password"]);
        }

        if($request["location"])
        {
          $this->validate($request,
          [
            'location' => 'max:255'
          ]);
          $user->location = $request["location"];
        }

        if($request["classification"])
        {
          $this->validate($request,
          [
            'classification' => 'max:255'
          ]);
          $user->classification = $request["classification"];
        }

        if($request["workType"])
        {
          $this->validate($request,
          [
            'workType' => 'max:255'
          ]);
          $user->workType = $request["workType"];
        }

        $user->save();

        Session::flash('message', "Profile Successfully Updated!");
        return redirect()->back();
    }
}
