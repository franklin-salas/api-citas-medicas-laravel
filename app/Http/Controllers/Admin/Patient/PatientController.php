<?php

namespace App\Http\Controllers\Admin\Patient;

use App\Http\Controllers\Controller;
use App\Http\Resources\Patient\PatientListResource;
use App\Http\Resources\Patient\PatientResource;
use App\Models\Patient;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $query = Patient::query();

        if ($request->has("search")) {
            $search = $request->input("search");

            $query->where(function ($query) use ($search) {
                $query->where("name", "like", "%{$search}%")
                    ->orWhere("email", "like", "%{$search}%")
                    ->orWhere("last_name", "like", "%" . $search . "%")
                    ->orWhere("email", "like", "%" . $search . "%")
                    ->orWhere("document", "like", "%{$search}%");
            });
        }

    


        if ($request->has("sort_direction")) {
            $sortDirection = $request->input("sort_direction");
            if ($sortDirection == "") {
                $request["sort_direction"] = "desc";
                $request["sort_by"] = "id";
            }
        }


        if ($request->has("sort_direction")) {

            $sortBy = $request->input("sort_by");
            $sortDirection = $request->input("sort_direction");

            $query->orderBy($sortBy, $sortDirection);
           
        } else {
            $query->orderBy("id", "desc");
        }
        $perPage = $request->input("per_page", 10);
        $patients = $query->paginate($perPage);
        return response()->json([
            "data" => PatientListResource::collection($patients),
            "pagination" => [
                "total" => $patients->total(),
                "per_page" => $patients->perPage(),
                "current_page" => $patients->currentPage(),
                "last_page" => $patients->lastPage(),
                "from" => $patients->firstItem(),
                "to" => $patients->lastItem(),
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
            'antecedent_family' => 'required',
            'antecedent_allergy' => 'required',
            'address' => 'required|max:255',
            'email' => 'nullable|email|unique:users,email', 
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);



        try {

            DB::beginTransaction();

            if ($request->hasFile("avatar")) {
                $path = Storage::putFile("patients", $request->file("avatar"));
                $validated["avatar"] = $path;
            }

            Patient::create($validated);
        
            DB::commit();

            return response()->json([
                'message' => 'Paciente guardado con éxito',
            ], 201);
        
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
            $user = Patient::findOrFail($id);

            return response()->json([
                'data' => PatientResource::make($user)
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => "Paciente no encontrado",
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
            'document' => 'required|max:50',
            'birth_date' => 'required|date_format:Y-m-d',
            'gender' => 'required',
            'antecedent_family' => 'nullable',
            'antecedent_allergy' => 'nullable',
            'address' => 'required|max:255',
            'email' => 'nullable|email',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($request->email && $request->email != ""){
            $email_is_valid = Patient::where("id", "<>", $id)->where("email", $request->email)->first();

            if ($email_is_valid) {
                return response()->json([
                    "message" => "El correo ya esta en uso"
                ], 403);
            }
        }
       

        try {

            DB::beginTransaction();

            $patient = Patient::findOrFail($id);

            if ($request->hasFile('avatar')) {
                if ($patient->avatar) {
                    Storage::delete($patient->avatar);
                }
                $path = Storage::putFile('staffs', $request->file('avatar'));
                $validated['avatar'] = $path;
            }
           
            $patient->update($validated);
            

            DB::commit();

            return response()->json([
                'message' => 'Paciente Actualizado con éxito',
            ], 201);
        } catch (ModelNotFoundException $e) {

            DB::rollBack();

            $model = $e->getModel();

            $message = match ($model) {
                Patient::class => 'Paciente no encontrado',
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
        $patient = Patient::find($id);

        if(!$patient){
            return response()->json([
                "message" => "El paciente no se a encontrado"
            ],404);
        }
       
        $patient->delete();
        return response()->json([
            "message" => "Paciente eliminado"
        ], 200);
    }
}
