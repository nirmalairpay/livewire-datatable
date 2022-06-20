<?php

namespace App\Repositories\Datatable\Interfaces;

interface Datatable
{
    public function query();

    public function columnDefinitions();

    public function columns();

    public function primaryKey();

    public function sortBy();

    public function sortDirection();

    public function pageSizes();

    public function pageSize();

    public function footerSearch();


}