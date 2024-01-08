<?php

namespace Modules\Item\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemLotsGroupCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function toArray($request) {
        return $this->collection->transform(function($row, $key) {

            $orphaned = (!$row->item->lots_enabled)
                ? 'SI'
                : 'NO' ;

            return [
                'id' => $row->id,
                'code' => $row->code,
                'quantity' => $row->quantity,
                'item_description' => $row->item->description,
                'brand' => isset($row->item->brand->name) ? $row->item->brand->name : '-',
                'date_of_due' => $row->date_of_due,
                'item_id' => $row->item_id,
                'orphaned' => $orphaned,
                'is_base' => (bool) ($row->quantity == 0),
            ];
        });
    }
}
