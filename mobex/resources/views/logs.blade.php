@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                @if (Auth::check())
                    <div class="card-header">My logs</div>
                    <div id="table_users_notes" class="card-body">
                        <table class="table">                        
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">Description</th>
                              <th scope="col">Time</th>
                            </tr>
                          </thead>
                          <tbody  ref="refNotesTbody">
                            @foreach ($logs as $log)
                                <tr>
                                    <td>
                                        {{ $log->id }}
                                    </td>                                
                                    <td>
                                        {{ $log->desc }}
                                    </td>
                                    <td>
                                        {{ $log->created_at }}
                                    </td>                                
                                </tr>                        
                            @endforeach                        
                          </tbody>
                        </table>

                        {{ $logs->appends(Request::except('page'))->links() }}
                    </div>
                    <div style="text-align: center;font-size: 20px;color: red;margin-bottom: 10px;cursor: pointer;" v-show="is_ready_to_export_csv == false" v-on:click="getLogsForExportCsv">CSV</div>
                    <download-csv v-show="is_ready_to_export_csv == true"
                    class   = "btn btn-default"
                    :data   = "json_data"
                    delimiter = ";"
                    name    = "logs.csv">
                    Download CSV
                    </download-csv>
                @else

                <div style="text-align: center;padding: 20px 0;font-size: 16px;">You are not logged. Please login</div>

                @endif
            </div>
        </div>
    </div>
</div>

@endsection

