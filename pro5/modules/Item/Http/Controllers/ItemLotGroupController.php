<?php

namespace Modules\Item\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Item\Exports\ItemLotsGroupExport;
use Carbon\Carbon;
use Modules\Item\Http\Requests\ItemLotGroupRequest;
use Modules\Item\Http\Resources\ItemLotsGroupCollection;
use Modules\Item\Models\ItemLotsGroup;

class ItemLotGroupController extends Controller
{

    public function index()
    {
        return view('item::item-lots-group.index');
    }


    public function columns()
    {
        return [
            'code' => 'Lote',
            'date_of_due' => 'Fecha',
            'quantity' => 'Cantidad',
            'item_description' => 'Producto',
        ];
    }


    public function records(Request $request)
    {
        $records = $this->getRecords($request);

        return new ItemLotsGroupCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function getRecords($request){

        if($request->column == 'item_description'){

            $records = ItemLotsGroup::whereHas('item', function($query) use($request){
                            $query->where('description', 'like', "%{$request->value}%")->latest();
                        });

        }else{
            $records = ItemLotsGroup::where($request->column, 'like', "%{$request->value}%")->latest();
        }

        return $records;
    }


    public function record($id)
    {
        return ItemLotsGroup::findOrFail($id);
    }


    public function store(ItemLotGroupRequest $request)
    {

        $id = $request->input('id');
        $record = ItemLotsGroup::findOrFail($id);
        $record->code = $request->code;
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Lote editada con éxito':'Lote registrada con éxito',
        ];

    }

    public function export(Request $request)
    {
        $records = $this->getRecords($request)->get();

        return (new ItemLotsGroupExport)
                ->records($records)
                ->download('Lotes_'.Carbon::now().'.xlsx');

    }

}
