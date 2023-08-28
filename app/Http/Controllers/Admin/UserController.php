<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Area;
use App\Models\Department;
use App\Models\User as ModelController;
use App\Models\UserType;

use DB;
use Log;

class UserController extends Controller
{
    private $nameModule;
    private $routeName;

    public function __construct()
    {
        $this->nameModule = 'Usuarios';
        $this->routeName = 'users';
    }

    public function getIndex()
    {
        return view('admin.users.index',
        [
            'nameModule' => $this->nameModule,
            'routeName' => $this->routeName
        ]);
    }

    public function postRows(Request $request)
    {
        $columns = [
            'id',
            'user_type_id',
            'name',
            'email',
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
                $edit = url("users/edit/{$row->id}");

                if($row->status == 1)
                {
                    $delete = url("users/delete/{$row->id}");
                }

                $nestedData['id'] = $row->id;
                $nestedData['user_type_id'] = $row->Type->name;
                $nestedData['name'] = ucwords($row->name);
                $nestedData['email'] = $row->email;
                $nestedData['department_id'] = $row->Department->name;
                $nestedData['area_id'] = $row->Area->name;
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
        $usersTypes = UserType::get();
        $departments = Department::where('status', 1)->get();
        $areas = Area::where('status', 1)->get();

        return view('admin.users.add', 
            [
                'nameModule' => $this->nameModule,
                'routeName' => $this->routeName,
                'usersTypes' => $usersTypes,
                'departments' => $departments,
                'areas' => $areas
            ]
        );
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type_id' => 'required',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8',
            'department_id' => 'required',
            'area_id' => 'required'
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

            $newUser = new ModelController();
            $newUser->user_type_id = $data['user_type_id'];
            $newUser->name = $data['name'];
            $newUser->email = $data['email'];
            $newUser->password = bcrypt($data['password']);
            $newUser->department_id = $data['department_id'];
            $newUser->area_id = $data['area_id'];
            $newUser->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en UserController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error al guardar en la base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en UserController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error no controlado!'
            ];
        }
    }

    public function getEdit($id)
    {
        try
        {
            $usersTypes = UserType::get();
            $departments = Department::where('status', 1)->get();
            $areas = Area::where('status', 1)->get();
            $rowEdit = ModelController::where('id', $id)->firstOrFail();
    
            return view('admin.users.edit',
                [
                    'nameModule' => $this->nameModule,
                    'routeName' => $this->routeName,
                    'rowEdit' => $rowEdit,
                    'usersTypes' => $usersTypes,
                    'departments' => $departments,
                    'areas' => $areas
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
            'user_type_id' => 'required',
            'name' => 'required',
            // 'email' => 'required|unique:users,email',
            'status' => 'required',
            'department_id' => 'required',
            'area_id' => 'required'
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
            
            $rowEdit = ModelController::where('id', $id)->firstOrFail();
            $rowEdit->user_type_id = $data['user_type_id'];
            $rowEdit->name = $data['name'];
            $rowEdit->email = $data['email'];
            $rowEdit->status = $data['status'];
            $rowEdit->department_id = $data['department_id'];
            $rowEdit->area_id = $data['area_id'];

            if(isset($data['password']))
            {
                $rowEdit->password = bcrypt($data['password']);
            }

            $rowEdit->save();
            
            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en UserController -> postEdit() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en la base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en UserController -> postEdit() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error no controlado!'
            ];
        }
    }

    public function getDelete($id)
    {
        try
        {
            $user = ModelController::where('id', $id)->firstOrFail();

            if($user->status == 0)
            {
                return [
                    'type' => 'error',
                    'message' => 'El registro ya se encuentra deshabilitado!'
                ];
            }

            DB::beginTransaction();
            
            $user->status = 0;
            $user->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en UserController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en la base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en UserController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error no controlado!'
            ];
        }
    }
}
