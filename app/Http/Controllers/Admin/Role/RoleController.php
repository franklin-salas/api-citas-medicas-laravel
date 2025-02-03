<?php

namespace App\Http\Controllers\Admin\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $req)
    {
        $name = $req->search?? '';
        $roles = Role::where("name","like","%".$name."%")->orderBy("id","desc")->get();

        return response()->json([
            "roles" => $roles->map(function($rol) {
                return [
                    "id" => $rol->id,
                    "name" => $rol->name,
                    "permision" => $rol->permissions,
					"permision_pluck" => $rol->permissions->pluck("name"),
                    "created_at" => $rol->created_at->format("Y-m-d h:i:s"),
                ];
            })
        ]);
    }

   
    /**
     * Store a newly created resource in storage.
     */
 
    public function store(Request $request)
    {
    // Validar los datos del request
    $request->validate([
        'name' => 'required',
    ]);

    // Verificar si el rol ya existe
    $is_role = Role::where("name", $request->name)->first();
    if ($is_role) {
        return response()->json([
            "message" => "El nombre del rol ya existe"
        ], 403);
    }

    try {
        // Iniciar una transacción
        DB::beginTransaction();

        // Crear el rol
        $role = Role::create(['guard_name' => 'api', 'name' => $request->name]);

        // Asignar permisos al rol
        foreach ($request->permisions  as $key => $permission) {
            if (Permission::where('name', $permission)->exists()) {
                $role->givePermissionTo($permission);
            }
        }

        // Guardar cambios
        $role->save();

        // Confirmar la transacción
        DB::commit();

        return response()->json([
            "message" => "Rol guardado con éxito"
        ], 201);

    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();

        return response()->json([
            "message" => "Error al crear el rol",
            "error" => $e->getMessage()
        ], 500);
    }
}


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
       $role = Role::findOrFail($id);
        return response()->json([
            "id" => $role->id,
            "name" => $role->name,
            "permision" => $role->permissions,
            "permision_pluck" => $role->permissions->pluck("name"),
            "created_at" => $role->created_at->format("Y-m-d h:i:s")
        ],200);
    }

   
    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     $is_role = Role::where("id","<>",$id)->where("name",$request->name)->first();
    //     if($is_role){
    //      return response()->json([
    //          "message" => "El nombre del rol ya existe"
    //      ],403);
    //     }
        
        
    //      $role = Role::findById($id);
    //     $role->name = $request->name;
    //     $role->save();
    
    //     $role->syncPermissions($request->permisions);
    //     //  foreach ($request->permisions as $key => $permision) {
    //     //      $role->givePermissionTo($permision);
    //     //  }
 
    //      return response()->json([
    //          "message" => "ok"
            
    //      ],201);
    // }

    public function update(Request $request, string $id)
{
    // Validar los datos del request
    $request->validate([
        'name' => 'required',
       
    ]);

    // Verificar si otro rol con el mismo nombre ya existe
    $is_role = Role::where("id", "<>", $id)->where("name", $request->name)->first();
    if ($is_role) {
        return response()->json([
            "message" => "El nombre del rol ya existe"
        ], 403);
    }
      // Encontrar el rol por ID
      $role = Role::findById($id);
      if (!$role) {
          return response()->json([
              "message" => "Rol no encontrado"
          ], 404);
      }

    try {
        // Iniciar una transacción
        DB::beginTransaction();

        // Actualizar el nombre del rol
        $role->name = $request->name;
        $role->save();

        // Sincronizar los permisos del rol
        $role->syncPermissions($request->permisions);

        // Confirmar la transacción
        DB::commit();

        return response()->json([
            "message" => "Rol actualizado con éxito"
        ], 200);

    } catch (\Exception $e) {
        // Revertir la transacción en caso de error
        DB::rollBack();

        return response()->json([
            "message" => "Error al actualizar el rol",
            "error" => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        if(!$role){
            return response()->json([
                "message" => "El rol no se a encontrado"
            ],404);
        }


        $role->delete();

        return response()->json([
            "message" => "Rol eliminado"
           
        ],200);
    }
}
