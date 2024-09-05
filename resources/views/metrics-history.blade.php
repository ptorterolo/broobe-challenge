@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="col-12">
                <div class="page-body">
                    <div class="container-xl">
                        <div class="card">
                        <div class="card-body">
                            <div id="table-default" class="table-responsive">
                            <table  class="table">
                                <thead>
                                <tr>
                                    <th><button class="table-sort" data-sort="sort-url">URL</button></th>
                                    <th><button class="table-sort" data-sort="sort-accesibility">ACCESIBILITY</button></th>
                                    <th><button class="table-sort" data-sort="sort-seo">SEO</button></th>
                                    <th><button class="table-sort" data-sort="sort-performance">PERFORMANCE</button></th>
                                    <th><button class="table-sort" data-sort="sort-bestPractices">BEST PRACTICES</button></th>
                                    <th><button class="table-sort" data-sort="sort-strategy">STRATEGY</button></th>
                                    <th><button class="table-sort" data-sort="sort-datetime">DATETIME</button></th>
                                </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    @foreach ($metrics as $metric)                                        
                                        <tr>
                                            <td class="sort-url">{{ $metric->url }}</td>
                                            <td class="sort-accesibility">{{ $metric->accesibility_metric }}</td>
                                            <td class="sort-seo">{{ $metric->seo_metric }}</td>
                                            <td class="sort-performance">{{$metric->performance_metric}}</td>
                                            <td class="sort-bestPractices">{{$metric->best_practices_metric}}</td>
                                            <td class="sort-strategy">{{$metric->strategy_name}}</td>
                                            <td class="sort-datetime" data-date="{{ strtotime($metric->created_at) }}">{{$metric->created_at}}</td>
                                        </tr>
                                    @endforeach    
                                    @if (count($metrics) === 0)
                                        <tr>
                                            <td colspan="7" class="text-center">No data available</td>
                                        </tr>
                                    @endif
                                
                                </tbody>
                            </table>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
        
    </div>
@endsection
<script src="{{ asset('js/list.min.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
      const list = new List('table-default', {
      	sortClass: 'table-sort',
      	listClass: 'table-tbody',
      	valueNames: [ 'sort-url', 'sort-accesibility', 'sort-seo', 'sort-performance','sort-bestPractices','sort-strategy',{ attr: 'data-date', name: 'sort-datetime' },
      	]
      });
      })
</script>