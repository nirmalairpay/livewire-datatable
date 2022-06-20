<div>
    <p>Datatable</p>

    <span>Page Size</span>
    <select wire:model="pageSize">
        @foreach ($pageSizes as $size)
        <option value="{{ $size }}">{{ $size }}</option>
        @endforeach
    </select>

    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead id="header">
                <tr>
                @foreach ($columnHeaders as $id => $title)
                    <th scope="col" wire:key="th_{{ $id }}"
                        @if($sortables[$columnNames[$id]] == true) 
                        {{ "wire:click=" . "sort('" . $columnNames[$id] . "')" }} 
                        @endif
                    >
                        {{ $title }}
                        @if($sortBy == $columnNames[$id])
                            @if($sortDirection == 'ASC')
                            <i class="fa fa-fw fa-sort-asc"></i>
                            @endif
                            @if($sortDirection == 'DESC')
                            <i class="fa fa-fw fa-sort-desc"></i>
                            @endif
                        @elseif($sortables[$columnNames[$id]] == true)
                        <i class="fa fa-fw fa-sort"></i>
                        @endif
                    </th>
                @endforeach
                </tr>
            </thead>

            <tbody id="body">
                @foreach ($data as $row)
                <tr wire:key="row_{{ $loop->index }}">
                    @foreach ($columnNames as $id => $name)
                    <td wire:key="col_{{ $loop->parent->index . '_' . $name }}">
                        @if ($dataTypes[$name] == 'string')
                            @isset($row->$name)
                                @isset($stringFormats[$name])
                                    @if ($stringFormats[$name] == 'uppercase')
                                        @php $row->$name = strtoupper($row->$name); @endphp
                                    @endif
                                    @if ($stringFormats[$name] == 'lowercase')
                                        @php $row->$name = strtolower($row->$name); @endphp
                                    @endif
                                    @if ($stringFormats[$name] == 'ucfirst')
                                        @php $row->$name = ucfirst($row->$name); @endphp
                                    @endif
                                    @if ($stringFormats[$name] == 'ucwords')
                                        @php $row->$name = ucwords($row->$name); @endphp
                                    @endif
                                @endisset
                                @isset($stringMaxLengths[$name])
                                    @if (strlen($row->$name) > $stringMaxLengths[$name])
                                        {{ substr($row->$name, 0, $stringMaxLengths[$name]) . "..." }}
                                    @else
                                        {{ $row->$name }}
                                    @endif
                                @else
                                    {{ $row->$name }}
                                @endisset
                            @else
                                {{ $defaults[$name] }}
                            @endisset
                        @endif
                        @if ($dataTypes[$name] == 'date')
                            @isset($row->$name)
                                @isset($dateFormats[$name])
                                    {{ Carbon\Carbon::parse($row->$name)->format($dateFormats[$name]) }}
                                @else
                                    {{ $row->$name }}
                                @endisset
                            @else
                                {{ $defaults[$name] }}
                            @endisset
                        @endif
                        @if ($dataTypes[$name] == 'numeric')
                            @isset($row->$name)
                                @isset($numericFormats[$name])
                                    {{ sprintf($numericFormats[$name], $row->$name) }}
                                @else
                                    {{ $row->$name }}
                                @endif
                            @else
                                {{ $defaults[$name] }}
                            @endisset
                        @endif
                        @if ($dataTypes[$name] == 'enum')
                            @isset($row->$name)
                                @isset($enumValues[$name])
                                    {{ $enumValues[$name][$row->$name] }}
                                @else
                                    {{ $row->$name }}
                                @endif
                            @else
                                {{ $defaults[$name] }}
                            @endisset
                        @endif
                    </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>

            @if ($footerSearch == true)
            <tfoot id="footer">
                <tr>
                    @foreach ($columnNames as $id => $name)
                    <td>
                        @if ($searchables[$name] == true)
                            @if ($dataTypes[$name] == 'string')
                                <input type="text">
                            @endif
                            @if ($dataTypes[$name] == 'date')
                                <input type="date">
                            @endif
                            @if ($dataTypes[$name] == 'numeric')
                                <input type="text">
                            @endif
                            @if ($dataTypes[$name] == 'enum')
                            <select name="footer_search_{{ $name }}" id="footer_search_{{ $name }}">
                                <option value="*" selected>All</option>
                                @if (isset($enumValues[$name]) && count($enumValues[$name]) > 1)
                                    @foreach ($enumValues[$name] as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @endif
                        @else

                        @endif
                    </td> 
                @endforeach
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <nav>
        <ul class="pagination">
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1">Previous</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item active">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Next</a>
            </li>
        </ul>
    </nav>

</div>
