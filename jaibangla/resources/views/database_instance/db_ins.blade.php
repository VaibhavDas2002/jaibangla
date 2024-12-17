<style type="text/css">
    .has-error {
        border-color: #cc0000;
        background-color: #ffff99;
    }

    .preloader1 {
        position: fixed;
        top: 40%;
        left: 52%;
        z-index: 999;
    }

    .preloader1 {
        background: transparent !important;
    }

    #loadingDi {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    .loadingDivModal {
        position: absolute;
        top: 0px;
        right: 0px;
        width: 100%;
        height: 100%;
        background-color: #fff;
        background-image: url('../images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    #updateDiv {
        border: 1px solid #d9d9d9;
        padding: 8px;
        box-shadow: 0 1px 1px rgb(0 0 0 / 10%);
    }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
    <div class="content-wrapper">
        <!-- <div class="preloader1"><img src="{{ asset('images/ZKZg.gif') }}" width="150px" id="loader_img"></div> -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Jai Bangla
            </h1>

        </section>
        <section class="content">
            <div class="box box-default">
                <div class="box-body">
                    <div id="loadingDi"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-3">
                                <select name="select_server" id="select_server" class="form-control">
                                    <option value="pgsql_mis">Jai Bangla Main</option>
                                    <option value="pgsql_encread">Jai Bangla Document</option>
                                </select>
                            </div>
                            <div class="form-group col-md-3">
                                <button class="btn btn-primary" title="Run" id="run"><i class="fa fa-play"></i> Play</button>&nbsp;&nbsp;
                                <button class="btn btn-success exportToExcel" type="button" ><i class="fa fa-download"></i> Excel</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">Query</div>
                        <div class="panel-body" style="padding: 0;">
                            <textarea class="form-control" rows="4" placeholder="Write Query" style="resize: vertical; font-size: 14px;"
                                name="query_editor" id="query_editor"></textarea>
                        </div>
                    </div>

                    <div id="res_div">
                        <div class="panel panel-default">
                            <div class="panel-heading" id="panel_head">Output Result</div>
                            <div class="panel-body" style="padding: 5px; font-size: 12px;">
                                <div class="table-responsive table2excel" id="result">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    </div>
@endsection
<script src="{{ asset('/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>
<script src="{{ asset("js/jquery.table2excel.js") }}"></script>
<script>
    $(document).ready(function() {
        $('#loadingDi').hide();
        $(".exportToExcel").click(function(e){
            var currentdate = new Date(); 
            var datetime = "Date: " + currentdate.getDate() + "-" + (currentdate.getMonth()+1) + "-" + currentdate.getFullYear() + " "  
                + currentdate.getHours() +currentdate.getMinutes() +currentdate.getSeconds();
            var c_date=datetime;
            // alert(c_date);
            $(".table2excel").table2excel({
                // exclude CSS class
                exclude: ".noExl",
                name: "Sheet1",
                filename: "Report"+c_date, //do not include extension
                fileext: ".xls" // file extension
            }); 
        });
        $('#schema').click(function() {
            
            // alert('HI!');
            $.ajax({
                url: "{{ url('fetch-schema-name') }}",
                type: "POST",
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    //  console.log(JSON.stringify(response.getSchemaName));
                    var html = '';
                    for (let index = 0; index < response.getSchemaName.length; index++) {
                        var element = response.getSchemaName[index].table_schema;
                        // console.log(element);
                        html += '<li class="treeview" style="height: auto;">';
                        html += '<a href="#"><i class="fa fa-link"></i> ' + element + '';
                        html += '    <span class="pull-right-container">';
                        html += '        <i class="fa fa-angle-left pull-right"></i>';
                        html += '    </span>';
                        html += '</a>';
                        html += '<ul class="treeview-menu" style="display: none;">';
                        html += '    <li class="treeview" style="height: auto;">';
                        html +=
                            '        <a href="#" onclick="getTableFunction(this.id)" id="' +
                            element + '"><i class="fa fa-table"></i> Tables';
                        html += '            <span class="pull-right-container">';
                        html +=
                            '                <i class="fa fa-angle-left pull-right"></i>';
                        html += '            </span>';
                        html += '        </a>';
                        html +=
                            '        <ul class="treeview-menu" style="display: none;" id="tableName' +
                            element + '">';
                        html += '        </ul>';
                        html += '    </li>';
                        html += '    <li class="treeview" style="height: auto;">';
                        html +=
                            '        <a href="#" onclick="getFunctionNameFunction(this.id)" id="' +
                            element + '"><i class="fa fa-table"></i> Functions';
                        html += '            <span class="pull-right-container">';
                        html +=
                            '                <i class="fa fa-angle-left pull-right"></i>';
                        html += '            </span>';
                        html += '        </a>';
                        html +=
                            '        <ul class="treeview-menu" style="display: none;" id="functionName' +
                            element + '">';
                        html += '        </ul>';
                        html += '    </li>';
                        html += '    <li class="treeview" style="height: auto;">';
                        html +=
                            '        <a href="#" onclick="getSequenceNameFunction(this.id)" id="' +
                            element + '"><i class="fa fa-table"></i> Sequences';
                        html += '            <span class="pull-right-container">';
                        html +=
                            '                <i class="fa fa-angle-left pull-right"></i>';
                        html += '            </span>';
                        html += '        </a>';
                        html +=
                            '        <ul class="treeview-menu" style="display: none;" id="sequenceName' +
                            element + '">';
                        html += '        </ul>';
                        html += '    </li>';
                        html += '</ul>';
                        html += '</li>';
                    }
                    $('#main_schema_div').html('');
                    $('#main_schema_div').html(html);

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    ajax_error(jqXHR, textStatus, errorThrown);
                }
            });
        });

        $('#run').click(function() {
            var stringQuery = btoa($('#query_editor').val());
            var textarea = document.getElementById("query_editor");  
            var selection = (textarea.value).substring(textarea.selectionStart,textarea.selectionEnd);
            if (selection != '') {
                var stringQuery = btoa(selection);
            } 
            var select_server = $('#select_server').val();
            $('#loadingDi').show();
            $.ajax({
                type: 'POST',
                url: "{{ route('query_result') }}",
                data: {
                    stringQuery: stringQuery,
                    selectServer: select_server,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    $('#loadingDi').hide();
                    if (response) {
                        $('#result').html('');
                        $('#result').html(response.data);
                    } else {
                        $('#result').html('<h2 style="font-size: 20px;"></h2>');
                        $('#result').text('Data Not Found');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#loadingDi').hide();
                    var msg = "<strong>Query Exception.</strong><br/>";
                    if (jqXHR.status !== 422 && jqXHR.status !== 400) {
                        msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
                    } else {
                        if (jqXHR.responseJSON.hasOwnProperty('exception')) {
                            msg += "Exception: <strong>" + jqXHR.responseJSON
                                .exception_message + "</strong>";
                        } else {
                            msg += "Error(s):<strong><ul>";
                            $.each(jqXHR.responseJSON, function(key, value) {
                                msg += "<li>" + value + "</li>";
                            });
                            msg += "</ul></strong>";
                        }
                    }
                    $('#result').html(msg);
                    // ajax_error(jqXHR, textStatus, errorThrown)
                }
            });
        });
    });
    // Get Table Name
    function getTableFunction(schama_name) {
        // console.log(schemaName);
        // console.log(schemaName);
        $.ajax({
            url: "{{ url('fetch-table-name') }}",
            type: "POST",
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                schamaName: schama_name
            },
            success: function(response) {
                var html = '';
                for (let index = 0; index < response.getTableName.length; index++) {
                    var TableName = response.getTableName[index].table_name;
                    html += '<li class="treeview" style="height: auto;">';
                    html += '<a href="#"><i class="fa fa-link"></i> ' + TableName + '';
                    html += '    <span class="pull-right-container">';
                    html += '        <i class="fa fa-angle-left pull-right"></i>';
                    html += '    </span>';
                    html += '</a>';
                    html += '<ul class="treeview-menu" style="display: none;">';
                    html += '    <li class="treeview" style="height: auto;">';
                    html += '        <a href="#" onclick="getColumnFunction(this.id)" id="' + TableName +
                        '.' + schama_name + '"><i class="fa fa-table"></i> Columns';
                    html += '            <span class="pull-right-container">';
                    html += '                <i class="fa fa-angle-left pull-right"></i>';
                    html += '            </span>';
                    html += '        </a>';
                    html += '        <ul class="treeview-menu" style="display: none;" id="columnName' +
                        schama_name + '' + TableName + '">';
                    html += '        </ul>';
                    html += '    </li>';
                    html += '</ul>';
                    html += '</li>';
                }
                // console.log(html);
                $('#tableName' + schama_name).html('');
                $('#tableName' + schama_name).html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                ajax_error(jqXHR, textStatus, errorThrown)
            }
        });
    }
    // Get Column Name
    function getColumnFunction(value) {
        var myArray = value.split(".");
        var schema_name = myArray[1];
        var table_name = myArray[0];
        $.ajax({
            url: "{{ url('fetch-column-name') }}",
            type: "POST",
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                schamaName: schema_name,
                tableName: table_name
            },
            success: function(response) {
                var html = '';
                for (let index = 0; index < response.getColumnName.length; index++) {
                    var ColumnName = response.getColumnName[index].column_name;
                    // console.log(TableName);
                    // console.log(element);
                    html += '<li><a href="#"><i class="fa fa-circle-o"></i> ' + ColumnName + '';

                    html += '</a></li>';

                }
                // console.log(html);
                $('#columnName' + schema_name + table_name).html('');
                $('#columnName' + schema_name + table_name).html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                ajax_error(jqXHR, textStatus, errorThrown)
            }
        });
    }

    // Get Functions names
    function getFunctionNameFunction(value) {
        $.ajax({
            url: "{{ url('fetch-function-name') }}",
            type: "POST",
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                schamaName: value
            },
            success: function(response) {
                var html = '';
                for (let index = 0; index < response.getFunctionName.length; index++) {
                    var ColumnName = response.getFunctionName[index].function_name;
                    // console.log(TableName);
                    // console.log(element);
                    html += '<li><a href="#"><i class="fa fa-circle-o"></i> ' + ColumnName + '';
                    html += '</a></li>';

                }
                // console.log(html);
                $('#functionName' + value).html('');
                $('#functionName' + value).html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                ajax_error(jqXHR, textStatus, errorThrown)
            }
        });
    }

    // Get Sequence Name
    function getSequenceNameFunction(value) {
        $.ajax({
            url: "{{ url('fetch-sequence-name') }}",
            type: "POST",
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                sequenceName: value
            },
            success: function(response) {
                var html = '';
                for (let index = 0; index < response.getSequenceName.length; index++) {
                    var SequenceName = response.getSequenceName[index].sequence_name;
                    // console.log(TableName);
                    // console.log(element);
                    html += '<li><a href="#"><i class="fa fa-circle-o"></i> ' + SequenceName + '';
                    html += '</a></li>';

                }
                // console.log(html);
                $('#sequenceName' + value).html('');
                $('#sequenceName' + value).html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                ajax_error(jqXHR, textStatus, errorThrown)
            }
        });
    }

    function ajax_error(jqXHR, textStatus, errorThrown) {
        var msg = "<strong>Failed to Load data.</strong><br/>";
        if (jqXHR.status !== 422 && jqXHR.status !== 400) {
            msg += "<strong>" + jqXHR.status + ": " + errorThrown + "</strong>";
        } else {
            if (jqXHR.responseJSON.hasOwnProperty('exception')) {
                msg += "Exception: <strong>" + jqXHR.responseJSON.exception_message + "</strong>";
            } else {
                msg += "Error(s):<strong><ul>";
                $.each(jqXHR.responseJSON, function(key, value) {
                    msg += "<li>" + value + "</li>";
                });
                msg += "</ul></strong>";
            }
        }
        $.alert({
            title: 'Error!!',
            type: 'red',
            icon: 'fa fa-warning',
            content: msg,
        });
    }
</script>
