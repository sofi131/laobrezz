<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\User\CreateUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;  //  add
use App\Models\User;
use App\Models\Role; //add
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('can:user list', ['only' => ['index','show']]);
        $this->middleware('can:user create', ['only' => ['create','store']]);
        $this->middleware('can:user edit', ['only' => ['edit','update']]);
        $this->middleware('can:user delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // add
        $users = (new User)->newQuery();
        if (request()->has('search')) {
            $users->where('name', 'Like', '%' . request()->input('search') . '%');
        }
        if (request()->query('sort')) {
            $attribute = request()->query('sort');
            $sort_order = 'ASC';
            if (strncmp($attribute, '-', 1) === 0) {
                $sort_order = 'DESC';
                $attribute = substr($attribute, 1);
            }
            $users->orderBy($attribute, $sort_order);
        } else {
            $users->latest();
        }
        $users = $users->paginate(5);
        return view('admin.user.index',compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // add
        $roles = Role::all();
        return view('admin.user.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Http\Requests\Admin\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
     // public function store(Request $request)
    public function store(StoreUserRequest $request, CreateUser $createUser)
    {
        // add
        // validate from request
        // we separate our validation by using Laravel Form Request Validation
        /* $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);*/
        // Create a user
        /*$user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        // Assign role to user
        if(! empty($request->roles)) {
            $user->assignRole($request->roles);
        }*/
       // $userService->createUser($request);

       // $userService->assignRole($request, $user);

        $createUser->handle($request);

        // Redirect with message
        return redirect()->route('user.index')
            ->with('message','User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // add
        $roles = Role::all();
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');
        return view('admin.user.show', compact('user', 'roles', 'userHasRoles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // add
        $roles = Role::all();
        $userHasRoles = array_column(json_decode($user->roles, true), 'id');
        return view('admin.user.edit', compact('user', 'roles', 'userHasRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // add
        /*$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable','confirmed', Rules\Password::defaults()],
        ]);*/
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        if($request->password){
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }
        $roles = $request->roles ?? [];
        $user->syncRole($roles);
        return redirect()->route('user.index')
            ->with('message','User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // delete
        $user->delete();

        return redirect()->route('user.index')
            ->with('message','User deleted successfully');
    }

    public function accountInfo()
    {
        $user = \Auth::user();
        return view('admin.user.account_info', compact('user'));
    }

    public function accountInfoStore(Request $request)
    {
        $request->validateWithBag('account', [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.\Auth::user()->id],
        ]);
        $user = \Auth::user()->update($request->except(['_token']));
        if ($user) {
            $message = "Account updated successfully.";
        } else {
            $message = "Error while saving. Please try again.";
        }
        return redirect()->route('admin.account.info')->with('account_message', $message);
    }

    public function changePasswordStore(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'old_password' => ['required'],
            'new_password' => ['required', Rules\Password::defaults()],
            'confirm_password' => ['required', 'same:new_password', Rules\Password::defaults()],
        ]);
        $validator->after(function ($validator) use ($request) {
            if ($validator->failed()) return;
            if (! Hash::check($request->input('old_password'), \Auth::user()->password)) {
                $validator->errors()->add(
                    'old_password', 'Old password is incorrect.'
                );
            }
        });
        $validator->validateWithBag('password');
        $user = \Auth::user()->update([
            'password' => Hash::make($request->input('old_password')),
        ]);
        if ($user) {
            $message = "Password updated successfully.";
        } else {
            $message = "Error while saving. Please try again.";
        }
        return redirect()->route('admin.account.info')->with('password_message', $message);
    }
}
