<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Article as ModelController;
use App\Models\MeasurementUnit;

use DB;
use Log;

class ArticleController extends Controller
{
    private $nameModule;
    private $routeName;

    public function __construct()
    {
        $this->nameModule = 'Artículos';
        $this->routeName = 'articles';
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
            'code',
            'name',
            'stock',
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
                ->orWhere('code', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = ModelController::where('name', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%")
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
                $nestedData['code'] = $row->code;
                $nestedData['name'] = strtoupper($row->name)." (".$row->quantity." ".$row->MeasurementUnit->symbol.")";
                $nestedData['stock'] = $row->stock;
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
        $measurementUnits = MeasurementUnit::get();

        return view("admin.{$this->routeName}.add", 
            [
                'nameModule' => $this->nameModule,
                'routeName' => $this->routeName,
                'units' => $measurementUnits,
            ]
        );
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required',
            'measurement_units_id' => 'required',
            'quantity' => 'required',
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

            $newArticle = new ModelController();
            $newArticle->code = strtoupper($data['code']);;
            $newArticle->name = strtoupper($data['name']);
            $newArticle->measurement_units_id = $data['measurement_units_id'];
            $newArticle->quantity = $data['quantity'];

            if(isset($data['stock']))
            {
                $newArticle->stock = $data['stock'];
            }

            $newArticle->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en ArticleController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en ArticleController -> postAdd() -> '.$e->getMessage());
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
            $measurementUnits = MeasurementUnit::get();

            return view("admin.{$this->routeName}.edit", 
                [
                    'nameModule' => $this->nameModule,
                    'routeName' => $this->routeName,
                    'rowEdit' => $rowEdit,
                    'units' => $measurementUnits,
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
            'code' => 'required',
            'name' => 'required',
            'measurement_units_id' => 'required',
            'quantity' => 'required',
            'stock' => 'required',
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
            
            $editArticle = ModelController::where('id', $id)->firstOrFail();
            $editArticle->code = strtoupper($data['code']);
            $editArticle->name = strtoupper($data['name']);
            $editArticle->stock = $data['stock'];
            $editArticle->status = $data['status'];
            $editArticle->measurement_units_id = $data['measurement_units_id'];
            $editArticle->quantity = $data['quantity'];
            $editArticle->save();
            
            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en ArticleController -> postEdit() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en ArticleController -> postEdit() -> '.$e->getMessage());
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
            $article = ModelController::where('id', $id)->firstOrFail();
            
            if($article->status == 0)
            {
                return [
                    'type' => 'error',
                    'message' => 'El registro ya se encuentra deshabilitado!'
                ];
            }

            DB::beginTransaction();

            $article->status = 0;
            $article->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en ArticleController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en ArticleController -> getDelete() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }
}
