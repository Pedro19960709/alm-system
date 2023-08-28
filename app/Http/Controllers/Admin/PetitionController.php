<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Article;
use App\Models\MeasurementUnit;
use App\Models\Petition as ModelController;
use App\Models\PetitionHistory;
use App\Models\PetitionStatus;

use App;
use Auth;
use Carbon\Carbon;
use DB;
use Log;
use Mail;

class PetitionController extends Controller
{
    private $nameModule;
    private $routeName;

    public function __construct()
    {
        $this->nameModule = 'Peticiones';
        $this->routeName = 'petitions';
    }

    public function getIndex()
    {
        return view("admin.{$this->routeName}.index",
        [
            'nameModule' => $this->nameModule,
            'routeName' => $this->routeName,
        ]);
    }

    public function postRows(Request $request)
    {
        $columns = [
            'id',
            'name',
            'remaining_articles',
            'delivered_articles',
            'department_id',
            'area_id',
            'created_at',
            'petition_status_id',
            'id'
        ];

        $USER = Auth::user();

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];

        if($USER->user_type_id != 1)
        {
            $query = ModelController::where([
                ['department_id', $USER->department_id],
                ['area_id', $USER->area_id]
            ]);


            if(!empty($request->input('search.value')))
            {
                $search = $request->input('search.value');

                $query->where([
                    ['department_id', $USER->department_id],
                    ['area_id', $USER->area_id],
                    ['created_at', 'LIKE', "%{$search}%"]
                ]);                
            }
        }
        else
        {
            $query = ModelController::orderBy($order, 'DESC');

            if(!empty($request->input('search.value')))
            {
                $search = $request->input('search.value');

                $query->whereHas('Department', function($department) use ($search) {
                        $department->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhere('created_at', 'LIKE', "%{$search}%");
            }
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $query->limit($limit);
        $query->offset($start);
        $query->orderBy($order, 'DESC');
        $rows = $query->get();
        
        $data = array();

        if(!empty($rows))
        {
            foreach($rows as $row)
            {
                $date = Carbon::parse($row->created_at);

                $deliver = null;
                $pdf = null;
                $cancel = null;
                $history = url("{$this->routeName}/item-history/{$row->id}");

                if($USER->user_type_id == 1 && $row->delivered_articles > 1)
                {
                    $pdf = url("{$this->routeName}/download-pdf/{$row->id}");
                }

                if($USER->user_type_id == 1 && $row->petition_status_id != 3 && $row->petition_status_id != 5)
                {
                    $deliver = url("{$this->routeName}/item-deliver/{$row->id}");
                    $cancel = url("{$this->routeName}/cancel/{$row->id}");
                }
                
                $nestedData['id'] = $row->id;
                $nestedData['articles_id'] = $row->Article->code.' - '.$row->Article->name.' ('.$row->Article->quantity.' '.$row->Article->MeasurementUnit->symbol.')';
                $nestedData['remaining_articles'] = $row->remaining_articles;
                $nestedData['delivered_articles'] = $row->delivered_articles;
                $nestedData['department_id'] = $row->Department->name;
                $nestedData['area_id'] = $row->Area->name;
                $nestedData['created_at'] = $date->format('d-m-Y H:i:s');
                $nestedData['petition_status_id'] = view("admin.tools.petition-status", ['status' => $row->petition_status_id, 'name' => $row->Status->name])->render();
                $nestedData['actions'] = view("admin.tools.petition-actions", ['history' => $history, 'deliver' => $deliver, 'pdf' => $pdf, 'cancel' => $cancel])->render();
                $data[] = $nestedData;
            }
        }

        return [
            'draw'              => intval($request->input('draw')),
            'recordsTotal'      => intval($totalData),
            'recordsFiltered'   => intval($totalFiltered),
            'data'              => $data
        ];
    }

    public function getAdd()
    {
        $measurementUnits = MeasurementUnit::get();
        $articles = Article::where('status', 1)->get();

        return view("admin.{$this->routeName}.add",
        [
            'nameModule' => $this->nameModule,
            'routeName' => $this->routeName,
            'userType' => Auth::user()->user_type_id,
            'units' => $measurementUnits,
            'articles' => $articles
        ]);
    }

    public function postAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required',
            'ordered_articles' => 'required',
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
            $petitionValidStatus = PetitionStatus::where('flag', 0)->orderBy('value', 'asc')->first();
            $firstStatus = PetitionStatus::orderBy('value', 'asc')->first();
            $USER_TRANSACTION = Auth::user();

            DB::beginTransaction();

            foreach($data['article_id'] as $key => $article)
            {
                $article = Article::where('id', $data['article_id'][$key])->firstOrFail();
                $article->stock = $article->stock - intval($data['ordered_articles'][$key]);

                if($article->stock < 0)
                {
                    $article->stock = 0;
                }

                $article->save();

                $newPetition = new ModelController();
                $newPetition->articles_id = $data['article_id'][$key];
                $newPetition->ordered_articles = $data['ordered_articles'][$key];
                $newPetition->remaining_articles = $data['ordered_articles'][$key];
                $newPetition->user_id = $USER_TRANSACTION->id;
                $newPetition->department_id = $USER_TRANSACTION->department_id;
                $newPetition->area_id = $USER_TRANSACTION->area_id;
                $newPetition->petition_status_id = $petitionValidStatus->id;
                $newPetition->save();

                $newHistory = new PetitionHistory();
                $newHistory->petition_id = $newPetition->id;
                $newHistory->user_id = $USER_TRANSACTION->id;
                $newHistory->comment = 'Petición de producto!';
                $newHistory->previous_status_id = $firstStatus->id;
                $newHistory->next_status_id = $petitionValidStatus->id;
                $newHistory->save();
            }

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en PetitionController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en PetitionController -> postAdd() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }

    public function getHistory($id)
    {
        try
        {
            $petition = ModelController::where('id', $id)->firstOrFail();
            $date = Carbon::parse($petition->created_at);

            return view("admin.{$this->routeName}.history", [
                'nameModule' => $this->nameModule,
                'routeName' => $this->routeName,
                'petition' => $petition,
                'timeline' => $petition->History,
                'created_at' => $date->format('d-m-Y H:i:s')
            ]);    
        }
        catch(\Exception $e)
        {
            return abort(404, 'El registro no se encuentra disponible.');
        }
    }

    public function getItemDeliver($id)
    {
        try
        {
            $petition = ModelController::where('id', $id)->firstOrFail();
            $date = Carbon::parse($petition->created_at);

            return view("admin.{$this->routeName}.deliver", [
                'nameModule' => $this->nameModule,
                'routeName' => $this->routeName,
                'petition' => $petition,
                'created_at' => $date->format('d-m-Y H:s:i')
            ]);
        } 
        catch(\Exception $e)
        {
            return abort(404, 'El registro no se encuentra disponible.');
        }
    }

    public function postItemDeliver(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'comment' => 'required'
        ]);

        if($validator->fails())
        {
            return [
                'type' => 'error',
                'message' => $validator->errors()->first()
            ];
        }

        $data = $request->input();

        if(isset($data['remaining_articles']))
        {
            if(intval($data['remaining_articles']) < intval($data['delivered_articles']))
            {
                return [
                    'type' => 'error',
                    'message' => 'La cantidad de productos a entregas es mayor a los productos restantes!'
                ];
            }
        }

        try
        {
            $SEND_MAIL = false;
            $petition = ModelController::where('id', $id)->firstOrFail();
            $petitionValidStatus = PetitionStatus::where([
                    ['flag', 0],
                    ['value', '>', $petition->Status->value]
                ])
                ->orderBy('value', 'asc')
                ->first();
            $completeStatus = PetitionStatus::where([
                ['flag', 1],
                ['value', '>', 1000]
            ])
            ->first();

            DB::beginTransaction();

            $history = new PetitionHistory();
            $history->petition_id = $id;
            $history->user_id = Auth::user()->id;
            $history->comment = ucfirst($data['comment']);

            if(isset($data['delivered_articles']))
            {
                $history->article = 1;
                $history->delivered_articles = $data['delivered_articles'];
                $history->previous_status_id = $petition->petition_status_id;
                $petition->delivered_articles = intval($petition->delivered_articles) + intval($data['delivered_articles']);
                $petition->remaining_articles = intval($petition->remaining_articles) - intval($data['delivered_articles']);
                
                if($petition->remaining_articles == 0)
                {
                    $history->next_status_id = $completeStatus->id;
                    $petition->delivered_at = date('Y-m-d H:i:s', strtotime('now'));
                    $SEND_MAIL = true;
                }
                else
                {
                    if(is_null($petitionValidStatus))
                    {
                        $history->next_status_id = $petition->petition_status_id;
                    }
                    else
                    {
                        $history->next_status_id = $petitionValidStatus->id;
                    }

                    $SEND_MAIL = true;
                }
            }
            else
            {
                $history->previous_status_id = $petition->petition_status_id;
                $history->next_status_id = $petition->petition_status_id;
            }
            
            $history->save();

            $petition->petition_status_id = $history->next_status_id;
            $petition->save();

            DB::commit();

            if($SEND_MAIL == true)
            {
                $this->sendMail($id);
            }

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en PetitionController -> postItemDeliver -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en PetitionController -> postItemDeliver -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }

    public function cancelPetition(Request $request, $id)
    {
        $data = $request->input();
        
        if(!isset($data['comment']) && strlen($data['comment']) < 3)
        {
            return [
                'type' => 'error',
                'message' => 'Falta el comentario o el comentario no es valido!'
            ];
        }

        try
        {
            $petition = ModelController::where('id', $id)->firstOrFail();

            $cancelStatus = PetitionStatus::where('flag', 1)
                ->orderBy('value', 'desc')
                ->first();

            DB::beginTransaction();

            $history = new PetitionHistory();
            $history->petition_id = $id;
            $history->user_id = Auth::user()->id;
            $history->comment = $data['comment'];
            $history->previous_status_id = $petition->petition_status_id;
            $history->next_status_id = $cancelStatus->id;
            $history->save();

            $petition->petition_status_id = $cancelStatus->id;
            $petition->delivered_at = date('Y-m-d H:i:s', strtotime('now'));
            $petition->save();

            DB::commit();

            return [
                'type' => 'success',
                'message' => ''
            ];
        }
        catch(\QueryException $e)
        {
            DB::rollback();
            Log::error('Error en PetitionController -> cancelPetition() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'Error en base de datos!'
            ];
        }
        catch(\Exception $e)
        {
            DB::rollback();
            Log::error('Error en PetitionController -> cancelPetition() -> '.$e->getMessage());
            return [
                'type' => 'error',
                'message' => 'No se pudo realizar la operación!'
            ];
        }
    }

    public function generatePDF($id)
    {
        try
        {
            $petition = ModelController::where('id', $id)->firstOrFail();
            $firstHistory = PetitionHistory::where('petition_id', $id)->firstOrFail();
            $lastHistory = PetitionHistory::where([
                ['petition_id', $id],
                ['article', 1]])
                ->orderBy('id', 'desc')
                ->firstOrFail();
            
            $view = view('admin.pdf.deliver-format', [
                'firstHistory' => $firstHistory, 
                'lastHistory' => $lastHistory, 
                'petition' => $petition,
            ]);

            $pdf = App::make('snappy.pdf.wrapper');
            $pdf->setOption('print-media-type', true);
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('images', true);
            $pdf->setOption('margin-bottom', 10);

            $pdf->loadHTML($view->render());
            $pdfContent = '';

            return $pdf->inline();
        } 
        catch(\Exception $e)
        {
            Log::error('Error en PetitionController -> generatePDF() -> '.$e->getMessage());
        }
    }

    private static function sendMail($id)
    {
        try
        {
            $petition = ModelController::where('id', $id)->firstOrFail();
            $firstHistory = PetitionHistory::where('petition_id', $id)->firstOrFail();
            $lastHistory = PetitionHistory::where([
                ['petition_id', $id],
                ['article', 1]])
                ->orderBy('id', 'desc')
                ->firstOrFail();            

            $email = Auth::user()->email;
            $userName = Auth::user()->name;

            $view = view('admin.mail.item-deliver', [
                'firstHistory' => $firstHistory, 
                'lastHistory' => $lastHistory, 
                'petition' => $petition,
                'userName' => $userName
            ]);

            $contents = $view->render();

            $view = view('admin.pdf.deliver-format', [
                'firstHistory' => $firstHistory, 
                'lastHistory' => $lastHistory, 
                'petition' => $petition,
            ]);

            $pdf = App::make('snappy.pdf.wrapper');
            $pdf->setOption('print-media-type', true);
            $pdf->setOption('enable-local-file-access', true);
            $pdf->setOption('images', true);
            $pdf->setOption('margin-bottom', 10);

            $pdf->loadHTML($view->render());
            $pdfContent = '';
            $pdfContent = $pdf->output();
    
            Mail::send(array(), array(), function($message) use ($contents, $email, $pdfContent)
            {
                $message->from('almacen@unedl.com', 'UNEDL-ALMACEN');
                $message->to($email);
                $message->subject('Asunto: Confirmación de entrega de producto');
                $message->html($contents, 'text/html');
                $message->attachData($pdfContent, 'formato-de-entrega.pdf');
            });   
        }
        catch(\Exception $e)
        {
            Log::error('Error en PetitionController -> sendMail() -> '.$e->getMessage());
        }
    } 
}
