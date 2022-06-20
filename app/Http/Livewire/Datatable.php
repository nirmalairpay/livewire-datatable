<?php

namespace App\Http\Livewire;

use Exception;
use Illuminate\Support\Facades\Log;

use App\Providers\DatatableServiceProvider;

use Livewire\Component;

class Datatable extends Component
{

    public $provider = null;

    public $columnNames = [];
    public $columnHeaders = [];
    public $sortables = [];
    public $searchables = [];
    public $defaults = [];
    public $dataTypes = [];

    public $primaryKey = null;

    public $dateFormats = [];
    public $dateMinValues = [];
    public $dateMaxValues = [];
    public $numericFormats = [];
    public $enumValues = [];
    public $stringMaxLengths = [];
    public $stringFormats = [];

    public $sortBy = null;
    public $sortDirection = 'ASC';

    public $pageSizes = [];
    public $pageSize = 10;

    public $columnsIncludedInQuery = false;
    public $footerSearch = true;

    public $data = [];

    public $totalDataCount = 0;

    public $totalFilteredDataCount = 0;

    private $columnDefinitions = [];


    public function render()
    {
        $this->refreshData();
        return view('livewire.datatable');
    }

    public function mount()
    {
        $repository = app(DatatableServiceProvider::$providers[$this->provider]);

        $this->columnDefinitions = $repository->columnDefinitions();
        $this->columnNames = $repository->columns();
        $this->columnsIncludedInQuery = empty($this->columnNames);
        $this->primaryKey = $repository->primaryKey();
        $this->sortBy = $repository->sortBy();
        $this->sortDirection = strtoupper($repository->sortDirection());
        $this->pageSizes = $repository->pageSizes();
        $this->pageSize = $repository->pageSize();
        $this->footerSearch = $repository->footerSearch();
        $this->prepare();
    }

    private function prepare()
    {
        try {
            $i = 0;
            foreach($this->columnDefinitions as $columnDefinition) {

                // Populating Names
                if (array_key_exists('name', $columnDefinition)) {
                    if ($this->columnsIncludedInQuery) {
                        $this->columnNames[$i] = $columnDefinition['name'];
                    }
                } else {
                    throw new Exception('Datatable - Missing required parameter `name`', 409);
                }

                // Populating Titles
                if (array_key_exists('title', $columnDefinition)) {
                    $this->columnHeaders[$i] = $columnDefinition['title'];
                } else {
                    throw new Exception('Datatable - Missing required parameter `title`', 409);
                }

                // Populating Sortables
                if (array_key_exists('sortable', $columnDefinition)) {
                    $this->sortables[$columnDefinition['name']] = $columnDefinition['sortable'];
                } else {
                    $this->sortables[$columnDefinition['name']] = true;
                }

                // Populating Searchables
                if (array_key_exists('searchable', $columnDefinition)) {
                    $this->searchables[$columnDefinition['name']] = $columnDefinition['searchable'];
                } else {
                    $this->searchables[$columnDefinition['name']] = true;
                }

                // Populating Defaults
                if (array_key_exists('default', $columnDefinition)) {
                    $this->defaults[$columnDefinition['name']] = $columnDefinition['default'];
                } else {
                    $this->defaults[$columnDefinition['name']] = 'N/A';
                }

                // Populating DataTypes
                if (array_key_exists('datatype', $columnDefinition)) {
                    $this->dataTypes[$columnDefinition['name']] = $columnDefinition['datatype'];
                
                    if ($columnDefinition['datatype'] == 'date') {

                        // Populating Date Formats
                        if (array_key_exists('format', $columnDefinition)) {
                            $this->dateFormats[$columnDefinition['name']] = $columnDefinition['format'];
                        }

                        // Populating Date Min Values
                        if (array_key_exists('min', $columnDefinition)) {
                            $this->dateMinValues[$columnDefinition['name']] = $columnDefinition['min'];
                        }

                        // Populating Date Max Values
                        if (array_key_exists('max', $columnDefinition)) {
                            $this->dateMaxValues[$columnDefinition['name']] = $columnDefinition['max'];
                        }
                    }

                    if ($columnDefinition['datatype'] == 'numeric') {

                        // Populating Numeric Formats
                        if (array_key_exists('format', $columnDefinition)) {
                            $this->numericFormats[$columnDefinition['name']] = $columnDefinition['format'];
                        }
                    }

                    if ($columnDefinition['datatype'] == 'enum') {

                        // Populating Enum Arrays
                        if (array_key_exists('values', $columnDefinition)) {
                            $this->enumValues[$columnDefinition['name']] = $columnDefinition['values'];
                        }
                    }

                    if ($columnDefinition['datatype'] == 'string') {

                        // Populating Max-Lengths
                        if (array_key_exists('max-length', $columnDefinition)) {
                            $this->stringMaxLengths[$columnDefinition['name']] = $columnDefinition['max-length'];
                        }

                        // Populating String Formats
                        if (array_key_exists('format', $columnDefinition)) {
                            $this->stringFormats[$columnDefinition['name']] = $columnDefinition['format'];
                        }
                    }

                } else {
                    $this->dataTypes[$columnDefinition['name']] = 'string';
                }

                $i++;
            }

            if (empty($this->columnNames)) {
                throw new Exception('Datatable - Empty Column name array', 409);
            }

            if (empty($this->primaryKey)) {
                $this->primaryKey = $this->columnNames[0];
            }

            if (empty($this->sortBy)) {
                $this->sortBy = $this->columnNames[0];
            }
        } catch (Exception $e) {
            throw $e;
            if ($e->getCode() != 409) {
                throw new Exception('Datatable - Unable to prepare datatable');
            } else {
                throw $e;
            }
        }
    }

    public function refreshData()
    {
        $repository = app(DatatableServiceProvider::$providers[$this->provider]);

        $dataCountQuery = $repository->query()
                            ->when(!$this->columnsIncludedInQuery, fn($query) => $query->select($this->columnNames))
                            ->selectRaw("COUNT($this->primaryKey) AS datatableDataCount")->limit(1);

        $filteredDataCountQuery = $repository->query()
                            ->when(!$this->columnsIncludedInQuery, fn($query) => $query->select($this->columnNames))
                            ->selectRaw("COUNT($this->primaryKey) AS datatableFilteredDataCount")->limit(1);

        $query = $repository->query()
                    ->when(!$this->columnsIncludedInQuery, fn($query) => $query->select($this->columnNames))
                    ->when($this->sortBy, fn($query) => $query->orderBy($this->sortBy, $this->sortDirection))
                    ->limit($this->pageSize);

        Log::channel('query_time')->info('------------- Query Time Measuring ------------------');
        Log::channel('query_time')->info('Data Count Query : '. $this->getSqlWithBindings($dataCountQuery));

        $start_time = microtime(true);
        $this->totalDataCount = $dataCountQuery->first()->datatableDataCount;

        Log::channel('query_time')->info('Total Data Count: ' . $this->totalDataCount);
        Log::channel('query_time')->info('Time Consumed: ' . microtime(true) - $start_time);

        Log::channel('query_time')->info('Filtered Data Count Query : '. $this->getSqlWithBindings($filteredDataCountQuery));

        $start_time = microtime(true);
        $this->totalFilteredDataCount = $filteredDataCountQuery->first()->datatableFilteredDataCount;

        Log::channel('query_time')->info('Total Filtered Data Count: ' . $this->totalFilteredDataCount);
        Log::channel('query_time')->info('Time Consumed: ' . microtime(true) - $start_time);

        Log::channel('query_time')->info('Query : '. $this->getSqlWithBindings($query));

        $start_time = microtime(true);
        $this->data = $query->get()->toArray();

        Log::channel('query_time')->info('Time Consumed: ' . microtime(true) - $start_time);
        Log::channel('query_time')->info('-----------------------------------------------------');
    }

    private function getSqlWithBindings($query)
    {
        $sql = $query->toSql();
        foreach($query->getBindings() as $binding)
        {
            $value = is_numeric($binding) ? $binding : "'".$binding."'";
            $sql = preg_replace('/\?/', $value, $sql, 1);
        }
        return $sql;
    }

    public function sort($column)
    {
        if (!empty($this->sortables)) {
            if ($column == $this->sortBy) {
                $this->sortDirection = $this->sortDirection == 'ASC' ? 'DESC' : 'ASC';
            } else {
                $this->sortBy = $column;
                $this->sortDirection = 'ASC';
            }
        }

    }

}
