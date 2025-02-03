<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use App\Http\Resources\service\ServiceListResource;
use App\Http\Resources\service\ServiceResource;
use App\Models\Service;
use App\Models\Specialty;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
     
        $query = Service::query()
        ->join('specialties', 'services.specialty_id', '=', 'specialties.id')
        ->select('services.*', 'specialties.name as specialty_name');

        if ($request->has('search')) {
            $search = $request->input('search');

            $query->where(function ($query) use ($search) {
                $query->where('services.name', 'like', "%{$search}%")
                    ->orWhere('specialties.name', 'like', "%{$search}%");
            });
        }


        if ($request->has('sort_direction')) {
            $sortDirection = $request->input('sort_direction');
            if ($sortDirection == '') {
                $request['sort_direction'] = 'desc';
                $request['sort_by'] = 'id';
            }
        }


            $sortBy = $request->input('sort_by', 'id');
            $sortDirection = $request->input('sort_direction', 'desc'); 

            if ($sortBy == 'specialty_name') {
                $query->orderBy('specialties.name', $sortDirection);
            } else {
                $query->orderBy('services.' . $sortBy, $sortDirection);
            }
        
        $perPage = $request->input('per_page', 10);
        $services = $query->paginate($perPage);
        return response()->json([
            "data" => ServiceListResource::collection($services),
            'pagination' => [
                'total' => $services->total(),
                'per_page' => $services->perPage(),
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'from' => $services->firstItem(),
                'to' => $services->lastItem(),
            ],
        ], 200);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable|max:500',
            'specialty_id' => 'required',
            'price' => 'required|numeric|regex:/^-?\d+(\.\d{0,2})?$/|min:0|max:1000',
            'status' => 'required|in:ACTIVO,INACTIVO',
            
        ]);

        try {

            Specialty::findOrFail($validated['specialty_id']);
            Service::create($request->all());

            return response()->json([
                'message' => 'Servicio guardado con éxito',
            ], 201);
        
        } catch (ModelNotFoundException $e) {
            $model = $e->getModel();
            $message = match ($model) {
                Specialty::class => 'Especialidad no encontrada',
                default => 'Elemento no encontrado',
            };
            return response()->json([
                'message' => $message,
            ], 404);

        }
        catch (Exception $e) {
            
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
            $service = Service::findOrFail($id);

            return response()->json([
                'data' => ServiceResource::make($service)
            ], 200);
        } catch (ModelNotFoundException $e) {

            return response()->json([
                'message' => 'Especialidad no encontrado',
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
            'name' => 'required|max:255',
            'description' => 'nullable|max:500',
            'specialty_id' => 'required',
            'price' => 'required|numeric|regex:/^-?\d+(\.\d{0,2})?$/|min:0|max:1000',
            'status' => 'required|in:ACTIVO,INACTIVO',
            
        ]);

        try {
            $service = Service::findOrFail($id);
            if($service->specialty_id != $validated['specialty_id']){
                Specialty::findOrFail($validated['specialty_id']);
                $service->specialty_id = $validated['specialty_id'];
            }
            $service->name = $validated['name'];
            $service->description = $validated['description'];
            $service->price = $validated['price'];
            $service->status = $validated['status'];
            $service->save();

            return response()->json([
                'message' => 'Servicio guardado con éxito',
            ], 201);
        
        } catch (ModelNotFoundException $e) {
            $model = $e->getModel();
            $message = match ($model) {
                Service::class => 'Servicio no encontrado',
                Specialty::class => 'Especialidad no encontrada',
                default => 'Elemento no encontrado',
            };
            return response()->json([
                'message' => $message,
            ], 404);

        }
        catch (Exception $e) {
            
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
        $service = Service::find($id);

        if(!$service){
            return response()->json([
                "message" => "La especialidad no se a encontrado"
            ],404);
        }
    
        $service->delete();
        return response()->json([
            "message" => "Especialidad eliminada"
        ], 200);
    }
}
