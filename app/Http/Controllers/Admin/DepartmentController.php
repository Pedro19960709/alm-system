<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Department as ModelController;

use DB;
use Log;

class DepartmentController extends Controller
{
    private $nameModule;
    private $routeName;

    public function __construct()
    {
        $this->nameModule = 'Departamentos';
        $this->routeName = 'departments';
    }

    public function getIndex()
    {
        return view("admin.{$this->routeName}.index",
        [
            'nameModule' => $this->nameModule,
            'routeName' => $this->routeName
        ]);
    }

    public function postRows(Request $request)
    {
        $columns = [
            'id',
            'name',
            'status',
            'id'
        ];

        $totalData = ModelController::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if(empty($request->input('search.value')))
        {
            $rows = ModelController::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        }
        else
        {
            $search = $request->input('search.value');

            $rows = ModelController::where('name', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = ModelController::where('name', 'LIKE', "%{$search}%")
                ->orWhere('id', 'LIKE', "%{$search}%")
                ->count();
        }

        $data = array();

        if(!empty($rows))
        {
            foreach($rows as $row)
            {
                $delete = null;
                $edit = url("{$this->routeName}/edit/{$row->id}");
                
                if($row->status == 1)
                {
                    $delete = url("{$this->routeName}/delete/{$row->id}");
                }

                $nestedData['id'] = $row->id;
                $nestedData['name'] = strtoupper($row->name);
                $nestedData['status'] = view('admin.tools.status', ['status' => $row->status])->render();
                $nestedData['options'] = view('admin.tools.actions', ['edit' => $edit, 'delete' => $delete])->render();
                $data[] = $nestedData;
            }
        }

        return [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => intval($totalFiltered),
            'data' => $data
        ];
    }

    public function getAdd()
    {
        return view("admin.{$this->routeName}.add", 
            [
                'nameModule' => $this->nameModule,
                'routeName' => $this->routeName
            ]
        );
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails())
        {
            return [
                'type' => 'error',
                'message' => $validator->errors()->first()
            ];
        }

        $data = $request->input();

        try
        {
            DB::beginTransaction();

            $newDepartment = new ModelController();
            $newDepartment->name = strtoupper($data['name']);
            $newDepartment->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en DepartmentController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en DepartmentController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }

    public function getEdit($id)
    {
        try
        {
            $rowEdit = ModelController::where('id', $id)->firstOrFail();

            return view("admin.{$this->routeName}.edit", 
                [
                    'nameModule' => $this->nameModule,
                    'routeName' => $this->routeName,
                    'rowEdit' => $rowEdit
                ]
            );
        }
        catch(Exception $e)
        {
            return abort(404, 'El registro no existe!');
        }
    }

    public function postEdit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required'
        ]);

        if($validator->fails())
        {
            return [
                'type' => 'error',
                'message' => $validator->errors()->first()
            ];
        }

        $data = $request->input();

        try
        {
            DB::beginTransaction();
            
            $editDepartment = ModelController::where('id', $id)->firstOrFail();
            $editDepartment->name = strtoupper($data['name']);
            $editDepartment->status = $data['status'];
            $editDepartment->save();
            
            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en DepartmentController -> postEdit() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en DepartmentController -> postEdit() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }

    public function getDelete($id)
    {
        try
        {
            $department = ModelController::where('id', $id)->firstOrFail();
            
            if($department->status == 0)
            {
                return [
                    'type' => 'error',
                    'message' => 'El registro ya se encuentra deshabilitado!'
                ];
            }

            DB::beginTransaction();

            $department->status = 0;
            $department->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en DepartmentController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en DepartmentController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }
}
