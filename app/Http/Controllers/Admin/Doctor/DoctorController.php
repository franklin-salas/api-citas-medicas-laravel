<?php

namespace App\Http\Controllers\Admin\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Resources\user\DoctorResource;
use App\Http\Resources\User\UserListResource;
use App\Models\DoctorSchedule;
use App\Models\ScheduleDay;
use App\Models\ScheduleHour;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      
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

        $query->whereHas("roles", function ($q) {
            $q->where("name", "like", "%DOCTOR%");
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

    /**
     * Store a newly created resource in storage.
     */
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
            'specialty_id' => 'required',  //|exists:roles,id
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $schedule_hours = json_decode($request->schedule_hours, true);

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



            foreach ($schedule_hours as $key => $schedule_hour) {
                if (sizeof($schedule_hour) > 0) {
                    $schedule_day = ScheduleDay::create([
                        "user_id" => $user->id,
                        "day" => $key,
                    ]);

                    foreach ($schedule_hour as $hour) {
                        ScheduleHour::create([
                            "schedule_day_id" => $schedule_day->id,
                            "doctor_schedule_id" => $hour["id"],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Doctor guardado con éxito',
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


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'data' => DoctorResource::make($user)
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Doctor no encontrado',
                'error' => $e->getMessage(),
            ], 404);
        }
        catch (Exception $e) {

            return response()->json([
                'message' => 'Doctor Error Servicio',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
            'email' => 'required|email',
            'password' => 'nullable|min:6',
            'confirm_password' => 'same:password',
            'role_id' => 'required',  //|exists:roles,id
            'specialty_id' => 'required',  //|exists:roles,id
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
        $users_is_valid = User::where("id", "<>", $id)->where("email", $request->email)->first();

        if ($users_is_valid) {
            return response()->json([

                "message" => "El correo ya esta en uso"
            ], 403);
        }
        $schedule_hours = json_decode($request->schedule_hours, true);

        try {

            DB::beginTransaction();

            $user = User::findOrFail($id);

           
            if (is_null($validated['password'])) {
                unset($validated['password']);
                unset($validated['confirm_password']);
            }

            if (is_null($validated['avatar'])) {
                unset($validated['avatar']);
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar) {
                    Storage::delete($user->avatar);
                }
                $path = Storage::putFile('staffs', $request->file('avatar'));
                $validated['avatar'] = $path;
            }

            if ($request->input("password")) {
                $validated['password'] = bcrypt($validated['password']);
            }

            $user->update($validated);

            if ($request->role_id != $user->roles()->first()->id) {
                $role_old = Role::findOrFail($user->roles()->first()->id);
                $user->removeRole($role_old);

                $role_new = Role::findOrFail($request->role_id);
                $user->assignRole($role_new);
            }

            foreach ($user->scheduleDays as $key => $scheduleDay) {
                $scheduleDay->delete();
            }

            foreach ($schedule_hours as $key => $schedule_hour) {
                if (sizeof($schedule_hour) > 0) {
                    $schedule_day = ScheduleDay::create([
                        "user_id" => $user->id,
                        "day" => $key,
                    ]);

                    foreach ($schedule_hour as $hour) {
                        ScheduleHour::create([
                            "schedule_day_id" => $schedule_day->id,
                            "doctor_schedule_id" => $hour["id"],
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Doctor Actualizado con éxito',
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                "message" => "El empleado no se a encontrado"
            ], 404);
        }

        $user->delete();
        return response()->json([
            "message" => "Empleado eliminado"
        ], 200);
    }

    public function scheduleHour()
    {


        $schedule_hours = DoctorSchedule::all();
        $hours_days = collect([]);
        foreach ($schedule_hours->groupBy("hour") as $key => $schedule_hour) {
            $hours_days->push([
                "hour" => Carbon::parse((date("Y-m-d") . " " . $key . ":00:00"))->format("h:i A"),
                "items" => $schedule_hour->map(function ($item) {
                    return [
                        "id" => $item->id,
                        "hour_start" => Carbon::parse((date("Y-m-d") . " " . $item->hour_start))->format("h:i A"),
                        "hour_end" => Carbon::parse((date("Y-m-d") . " " . $item->hour_end))->format("h:i A"),
                    ];
                })
            ]);
        }
        return response()->json([
            'data' => $hours_days
        ], 200);
    }
}
