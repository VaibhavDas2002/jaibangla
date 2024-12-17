<table id="example1" class="table table-bordered table-condensed table-hover table-striped" cellspacing="0" width="100%">
  <thead style="font-size: 12px;">
    <tr>
      <th>District</th>
      <th>Age Below 60 years</th>
      <th>Age Between 60-69 years</th>
      <th>Age Between 70-79 years</th>
      <th>Age Between 80-89 years</th>
      <th>Age Between 90-99 years</th>
      <th>Age Above 100 years</th>
    </tr>
  </thead>
  <tbody>
    @foreach($result as $k)
    <tr>
      <td>{{ $k->district_name }}</td>
      <td>{{ $k->age_below_60 }}</td>
      <td>{{ $k->age_60_70 }}</td>
      <td>{{ $k->age_70_80 }}</td>
      <td>{{ $k->age_80_90 }}</td>
      <td>{{ $k->age_90_100 }}</td>
      <td>{{ $k->age_above_100 }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <th></th><th></th><th></th><th></th><th></th><th></th><th></th>
    </tr>
  </tfoot>
</table>

<script>
  $(document).ready(function() {
    $('#example1').DataTable( {
      dom: 'Blfrtip',
      "scrollX": true,
      "paging": false,
      "searchable": false,
      "bFilter": false,
      "bInfo": false,
      "ordering": false,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      buttons: [
        {
          extend: 'pdf',
          title: "Old Aged Pension Age Cohort wise Beneficiary Report- @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
          messageTop:"Date:@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
          text:'<i class="fa fa-file-pdf-o"></i> PDF',
          footer: true,
          pageSize:'A4',
          orientation: 'landscape',
          // className: "btn btn-danger",
          pageMargins: [ 40, 60, 40, 60 ],
          exportOptions: {
            columns: [0,1,2,3,4,5,6],
          }
       },
       {
          extend: 'excel',
          title: "Old Aged Pension Age Cohort wise Beneficiary Report- @php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
          messageTop:"Date:@php date_default_timezone_set('Asia/Kolkata');$date = \Carbon\Carbon::createFromFormat('F j, Y g:i:a', date('F j, Y g:i:a'));$date = $date->format('F j, Y g:i:a'); echo    $date ;@endphp",
          text:'<i class="fa fa-file-excel-o"></i> Excel',
          footer: true,
          pageSize:'A4',
          //orientation: 'landscape',
          // className: "btn btn-success",
          pageMargins: [ 40, 60, 40, 60 ],
          exportOptions: {
            columns: [0,1,2,3,4,5,6],
            stripHtml: false,
          }
        }
      ],
      "footerCallback": function ( row, data, start, end, display ) {
        var api = this.api(), data;

        // Remove the formatting to get integer data for summation
        var intVal = function ( i ) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '')*1 :
                typeof i === 'number' ?
                    i : 0;
        };

        // Total over this page
        total_1 = api
          .column( 1, { page: 'current'} )
          .data()
          .reduce( function (a, b) {
              return intVal(a) + intVal(b);
        }, 0 );
        total_2 = api
          .column( 2, { page: 'current'} )
          .data()
          .reduce( function (a, b) {
              return intVal(a) + intVal(b);
        }, 0 );
        total_3 = api
          .column( 3, { page: 'current'} )
          .data()
          .reduce( function (a, b) {
              return intVal(a) + intVal(b);
        }, 0 );
        total_4 = api
          .column( 4, { page: 'current'} )
          .data()
          .reduce( function (a, b) {
              return intVal(a) + intVal(b);
        }, 0 );
        total_5 = api
          .column( 5, { page: 'current'} )
          .data()
          .reduce( function (a, b) {
              return intVal(a) + intVal(b);
        }, 0 );
        total_6 = api
          .column( 6, { page: 'current'} )
          .data()
          .reduce( function (a, b) {
              return intVal(a) + intVal(b);
        }, 0 );
  
        // Update footer
        $( api.column( 0 ).footer() ).html(
          "Total: "
        );
        $( api.column( 1 ).footer() ).html(
          total_1
        );
        $( api.column( 2 ).footer() ).html(
          total_2
        );
        $( api.column( 3 ).footer() ).html(
          total_3
        );
        $( api.column( 4 ).footer() ).html(
          total_4
        );
        $( api.column( 5 ).footer() ).html(
          total_5
        );
        $( api.column( 6 ).footer() ).html(
          total_6
        );
      } 
    });
    // $('.buttons-excel').removeClass('dt-button');
    // $('.buttons-pdf').removeClass('dt-button');
  });
</script>