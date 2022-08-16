<?php

namespace App\Imports;

use App\Models\Top;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TopsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row){
        return new Top([
            'clave_ct'     => $row['clave_ct'],
            'marca'    => $row['marca'],
            'mes'    => $row['mes'],
            'anio'    => $row['anio']
        ]);
    }
}