<?php

namespace App\Http\Controllers\Admin\Specialty;

use App\Http\Controllers\Controller;
use App\Http\Resources\Specialty\SpecialtyListResource;
use App\Http\Resources\Specialty\SpecialtyResource;
use App\Models\Specialty;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SpecialtyController extends Controller
{
    public function index(Request $request)
    {
    
        $query = Specialty::query();
          
        if ($request->has('search')) {
            $search = $request->search;
            // $search = trim($search);
            
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('status','like',"%".$search."%");
        }      
        if ($request->has('sort_direction')) {
            $sortDirection = $request->input('sort_direction');
            if ($sortDirection == '') {
                $request['sort_direction'] = 'desc';
                $request['sort_by'] = 'id';
            }
            $sortBy = $request->input('sort_by');
            $sortDirection = $request->input('sort_direction');
            $query->orderBy( $sortBy, $sortDirection);
        }else {
            $query->orderBy('id', 'desc');
        }

     

        $perPage = $request->input('per_page', 10);
        $specialty = $query->paginate($perPage);
        return response()->json([
            "data" => SpecialtyListResource::collection($specialty),
            'pagination' => [
                'total' => $specialty->total(),
                'per_page' => $specialty->perPage(),
                'current_page' => $specialty->currentPage(),
                'last_page' => $specialty->lastPage(),
                'from' => $specialty->firstItem(),
                'to' => $specialty->lastItem(),
            ],
        ], 200);
    }


    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|max:255|unique:specialties,name',
            'status' => 'required|required|in:ACTIVO,INACTIVO',
            
        ]);

            $specialty = Specialty::create($validated);

            return response()->json([
                'message' => 'Especialidad guardado con éxito',
            ], 201); 
        
    }

    public function update(Request $request, string $id)
    { 
    
        $request->validate([
            'name' => 'required|max:255|unique:specialties,name,'. $id,
            'status' => 'required|required|in:ACTIVO,INACTIVO',
            
        ]);


        try {
            DB::beginTransaction();
            $specialty = Specialty::findOrFail($id);
            $specialty->name = $request->name;
            $specialty->status = $request->status;
            $specialty->save();
            DB::commit();

            return response()->json([
                "message" => "Especialidad actualizada con éxito"
            ], 201);
        } catch (ModelNotFoundException $e) {

            $model = $e->getModel();

            $message = match ($model) {
                Specialty::class => 'Especialidad no encontrada',
                default => 'Elemento no encontrado',
            };
            DB::rollBack();

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
            $specialty = Specialty::findOrFail($id);

            return response()->json([
                'data' => SpecialtyResource::make($specialty)
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Especialidad no encontrado',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(string $id)
    {
        $specialty = Specialty::find($id);

        if(!$specialty){
            return response()->json([
                "message" => "La especialidad no se a encontrado"
            ],404);
        }
    
        $specialty->delete();
        return response()->json([
            "message" => "Especialidad eliminada"
        ], 200);
    }


}
