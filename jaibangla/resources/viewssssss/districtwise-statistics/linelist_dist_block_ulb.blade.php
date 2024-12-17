@extends('districtwise-statistics.base')
@section('action-content')
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="container" align="center">
              <font size="3" class="text-primary" style="float: left;"><b>{{$msg}}</b></font>   
            </div>
        </div>

      <div class="box">
  
          <!-- /.box-header -->
          <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="col-sm-12">
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                    <thead>
                      <tr role="row">
                        <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="city: activate to sort column ascending">Serial No</th>
                        <th width="40%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="state: activate to sort column ascending">District/Block/Municipllity</th>
                        <th width="40%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Action (Download)</th>
                      </tr>
                    </thead>
                    <tbody>
                    @php $i=1; @endphp
                    @foreach($linelist as $list)
                        <tr role="row" class="odd">
                          <td width="10%">@php print $i++; @endphp</td>
                          <td width="40%">
                              <?php 
                                if ($level == 'S') {
                                  // Scheme ID
                                  $filter = $scheme;
                                  $data = array('level'=>$level,'filter'=>$filter);
                                  print 'West Bengal';      
                                }
                                else if ($level == 'D') {
                                  // District Code
                                  $filter =  $list->district_code;
                                  $data = array('level'=>$level,'filter'=>$filter);
                                  print '('.$list->district_code.') '.$list->district_name;
                                }
                                else{
                                    if ($block_ulb == 2) {
                                      $filter =  $list->block_code;
                                      print '('.$list->block_code.') '.$list->block_name;
                                    }
                                    else{
                                      $filter = $list->urban_body_code;
                                      print '('.$list->urban_body_code.') '.$list->urban_body_name;
                                    }
                                  $data = array('level'=>$level,'filter'=>$filter,'block_ulb'=>$block_ulb);    
                                }
                                // $data = array('level'=>$level,'filter'=>$filter);
                                $query = http_build_query(array('aParam' => $data)); 
                              ?>
                          </td>
                          <td width="40%">
                            <a href="{{ url('generateExcel/'.$query) }}" target="_target"><button class="btn btn-success">Excel</button></a>
                            
                            <a href="#"><button class="btn btn-warning">Statistics</button></a>
                          </td>
                      </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th width="10%" rowspan="1" colspan="1">Serial No</th>
                        <th width="40%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="state: activate to sort column ascending">District/Block/Municipality</th>
                        <th width="40%" rowspan="1" colspan="1">Action</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>

            </div>
          </div>
          <!-- /.box-body -->
        </div>
    </section>
    <!-- /.content -->
  
@endsection