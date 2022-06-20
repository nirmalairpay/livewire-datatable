<?php

namespace App\Repositories\Datatable\Traits;

trait DefaultDatatable {

    function columns() 
    {
        return [];
    }

    function primaryKey()
    {
        return null;
    }

    public function sortBy()
    {
        return null;
    }

    public function sortDirection()
    {
        return 'ASC';
    }

    public function pageSizes()
    {
        return [10, 20, 30, 50];
    }

    public function pageSize()
    {
        return 10;
    }

    public function footerSearch()
    {
        return true;
    }

}