<?php

namespace App\Repositories\Datatable;

use App\Repositories\Datatable\Interfaces\Datatable;
use App\Repositories\Datatable\Traits\DefaultDatatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerRepository implements Datatable
{
    use DefaultDatatable;

    public function __construct()
    {
        Log::channel('object_lifecycle')->info('CustomerRepository Created');
    }

    public function __destruct()
    {
        Log::channel('object_lifecycle')->info('CustomerRepository Destroyed');
    }

    public function query()
    {
        return DB::connection('mysql')->table('ma_user_master')
        ->select('ma_user_master_id', 'firstname', 'lastname', 'addedon', 'user_status');
    }

    public function columnDefinitions()
    {
        return [
            [   
                'name' => 'ma_user_master_id',
                'title' => 'Master ID stsgssd',
                'datatype' => 'numeric',
                'format' => 'Rs %01.2f',
            ],
            [
                'name' => 'firstname',
                'title' => 'First Name',
                'datatype' => 'string',
                'format' => 'uppercase',
                'sortable' => false
            ],
            [
                'name' => 'lastname',
                'title' => 'Last Name',
                'searchable' => false,
                'default' => 'N/A'
            ],
            [
                'name' => 'addedon',
                'title' => 'AddedOn',
                'datatype' => 'date',
                'format' => 'd/m/Y',
                'default' => 'Not Available'
            ],
            [
                'name' => 'user_status',
                'title' => 'Status',
                'datatype' => 'enum',
                'values' => ['Y' => 'Yes', 'N' => 'No', 'L' => 'Locked','O' => 'Blocked', 'D' => 'Deleted']
            ],
        ];
    }

    // public function columns()
    // {
    //     return ['ma_user_master_id', 'firstname', 'lastname'];
    // }

}