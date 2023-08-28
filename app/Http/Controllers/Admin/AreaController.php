<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Area as ModelController;
use App\Models\Department;

use DB;
use Log;

class AreaController extends Controller
{
    private $nameModule;
    private $routeName;

    public function __construct()
    {
        $this->nameModule = 'Áreas';
        $this->routeName = 'areas';
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
                $nestedData['department_id'] = $row->Department->name;
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
        $departments = Department::where('status', 1)->get();

        return view("admin.{$this->routeName}.add", 
            [
                'nameModule' => $this->nameModule,
                'routeName' => $this->routeName,
                'departments' => $departments
            ]
        );
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'department_id' => 'required'
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

            $newArea = new ModelController();
            $newArea->name = strtoupper($data['name']);
            $newArea->department_id = $data['department_id'];
            $newArea->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en AreaController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en AreaController -> postAdd() -> '.$e->getMessage());
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
            $departments = Department::where('status', 1)->get();

            return view("admin.{$this->routeName}.edit", 
                [
                    'nameModule' => $this->nameModule,
                    'routeName' => $this->routeName,
                    'rowEdit' => $rowEdit,
                    'departments' => $departments
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
            'department_id' => 'required',
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
            
            $editArea = ModelController::where('id', $id)->firstOrFail();
            $editArea->name = strtoupper($data['name']);
            $editArea->department_id = $data['department_id'];
            $editArea->status = $data['status'];
            $editArea->save();
            
            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en AreaController -> postEdit() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en AreaController -> postEdit() -> '.$e->getMessage());
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
            $area = ModelController::where('id', $id)->firstOrFail();
            
            if($area->status == 0)
            {
                return [
                    'type' => 'error',
                    'message' => 'El registro ya se encuentra deshabilitado!'
                ];
            }

            DB::beginTransaction();

            $area->status = 0;
            $area->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en AreaController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en AreaController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }
}
