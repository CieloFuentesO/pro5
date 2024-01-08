<?php

namespace Modules\Inventory\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransferExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    protected $records;
    protected $format;

    public function records($records) {
        $this->records = $records;
        return $this;
    }

    public function format($format) {
        $this->format = $format;

        return $this;
    }

    public function view(): View {

        return view('inventory::reports.transfer.export', [
            'records' => $this->records,
            'format' => $this->format
        ]);
    }
}
