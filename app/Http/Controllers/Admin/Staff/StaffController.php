<?php

namespace App\Http\Controllers\Admin\Staff;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserListResource;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $query = User::query()
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('users.*', 'roles.name as role_name');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($query) use ($search) {
                $query->where('users.name', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere("users.last_name", "like", "%" . $search . "%")
                    ->orWhere("users.email", "like", "%" . $search . "%")
                    ->orWhere('users.document', 'like', "%{$search}%");
            });
        }

        $query->whereHas("roles",function($q){
            $q->where("name","not like","%DOCTOR%");
        });


        if ($request->has('sort_direction')) {
            $sortDirection = $request->input('sort_direction');
            if ($sortDirection == '') {
                $request['sort_direction'] = 'desc';
                $request['sort_by'] = 'id';
            }
        }


        if ($request->has('sort_direction')) {

            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction');


            if ($sortBy == 'role_name') {
                $query->orderBy('roles.name', $sortDirection);
            } else {
                $query->orderBy('users.' . $sortBy, $sortDirection);
            }
        } else {
            $query->orderBy('users.id', 'desc');
        }
        $perPage = $request->input('per_page', 10);
        $users = $query->paginate($perPage);
        return response()->json([
            "data" => UserListResource::collection($users),
            'pagination' => [
                'total' => $users->total(),
                'per_page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
            ],
        ], 200);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required',
            'last_name' => 'required|max:150',
            'mobile' => 'required|max:20',
            'document' => 'max:50',
            'birth_date' => 'required|date_format:Y-m-d',
            'gender' => 'required',
            'education' => 'required|max:255',
            'designation' => 'required',
            'address' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'role_id' => 'required',  //|exists:roles,id
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);



        try {

            DB::beginTransaction();

            if ($request->hasFile('avatar')) {
                $path = Storage::putFile('staffs', $request->file('avatar'));
                $validated['avatar'] = $path;
            }

            $validated['password'] = bcrypt($validated['password']);

            $user = User::create($validated);

            $role = Role::findOrFail($request->role_id);
            $user->assignRole($role);
            // "Fri Oct 08 1993 00:00:00 GMT-0500 (hora estándar de Perú)"
            // Eliminar la parte de la zona horaria (GMT-0500 y entre paréntesis)
            // $date_clean = preg_replace('/\(.*\)|[A-Z]{3}-\d{4}/', '', $request->birth_date);

            DB::commit();

            return response()->json([
                'message' => 'Empleado guardado con éxito',
            ], 201);
        } catch (ModelNotFoundException $e) {

            DB::rollBack();

            $model = $e->getModel();

            $message = match ($model) {
                Role::class => 'Rol no encontrado',
                default => 'Elemento no encontrado',
            };

            return response()->json([
                'message' => $message,
            ], 404);
        } catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Se produjo un error en la operación',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // $date_clean = preg_replace('/\(.*\)|[A-Z]{3}-\d{4}/', '', $request->birth_date);

    // $request->request->add(["birth_date" => Carbon::parse($date_clean)->format("Y-m-d h:i:s")]);

    // $request->request->add(["birth_date" => Carbon::parse($request->birth_date, 'GMT')->format("Y-m-d h:i:s")]);

    public function update(Request $request, string $id)
    {

        // return response()->json([

        //     "message" =>  $request->all()
        // ], 403);
    
        $request->validate([
            'name' => 'required',
            'last_name' => 'required|max:150',
            'mobile' => 'required|max:20',
            'document' => 'max:50',
            'birth_date' => 'required|date_format:Y-m-d',
            'gender' => 'required',
            'education' => 'required|max:255',
            'designation' => 'required',
            'address' => 'required|max:255',
            'email' => 'required|email',   //'required|email|unique:users,email,' . $id,
            'password' => 'min:6',
            'confirm_password' => 'same:password',
            'role_id' => 'required',  //|exists:roles,id
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $users_is_valid = User::where("id", "<>", $id)->where("email", $request->email)->first();

        if ($users_is_valid) {
            return response()->json([

                "message" => "El correo ya esta en uso"
            ], 403);
        }

        try {

            DB::beginTransaction();

            $user = User::findOrFail($id);

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::delete($user->avatar);
                }
                $path = Storage::putFile('staffs', $request->file('avatar'));
                $user->avatar = $path;

            }
    
            if ($request->password) {
                $user->password= bcrypt($request->password);
            }
       
           
            $user->name = $request->name;
            $user->last_name = $request->last_name;
            $user->mobile = $request->mobile;
            $user->document = $request->document;
            $user->birth_date = $request->birth_date;
            $user->gender = $request->gender;
            $user->education = $request->education;
            $user->designation = $request->designation;
            $user->address = $request->address;
            $user->email = $request->email;
            $user->save();
    
            if ($request->role_id != $user->roles()->first()->id) {
                $role_old = Role::findOrFail($user->roles()->first()->id);
                $user->removeRole($role_old);
    
                $role_new = Role::findOrFail($request->role_id);
                $user->assignRole($role_new);
            }

            DB::commit();

            return response()->json([
                "message" => "Empleado actualizado con éxito"
            ], 201);
        } catch (ModelNotFoundException $e) {

            DB::rollBack();

            $model = $e->getModel();

            $message = match ($model) {
                User::class => 'Empleado no encontrado',
                Role::class => 'Rol no encontrado',
                default => 'Elemento no encontrado',
            };

            return response()->json([
                'message' => $message,
            ], 404);

        }catch (Exception $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Se produjo un error en la operación',
                'error' => $e->getMessage(),
            ], 500);
        }

       
    }

    public function show(string $id)
    {

        try {
            $user = User::findOrFail($id);

            return response()->json([
                'data' => UserResource::make($user)
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Empleado no encontrado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json([
                "message" => "El empleado no se a encontrado"
            ],404);
        }
       
        $user->delete();
        return response()->json([
            "message" => "Empleado eliminado"
        ], 200);
    }



}
