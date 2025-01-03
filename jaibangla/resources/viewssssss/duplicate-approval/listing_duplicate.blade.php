<!-- Main content -->
<section class="content">
  <table id="example" class="display" cellspacing="0" width="100%" style="font-size: 14px;">
    <thead>
      <tr role="row">
        <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Serial No</th>
        @if($filters == 'ration')
        <th width="30%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Ration Card No</th>
        @elseif($filters == 'voter')
        <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Voter ID Card</th>
        @elseif($filters == 'bank')
        <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Bank IFSC - Account No</th>
        @endif
        <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">No of Beneficiary</th>
        @if(Auth::user()->designation_id == 'Approver')
        <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column ascending">Action</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @php $i=1; @endphp    
      @foreach($reports as $report)
      <tr>
        <td class="sorting_1">@php print $i++; @endphp</td>
        <td>
          @if($filters == 'ration')
            {{ $report->ration_card_no }}
          @elseif($filters == 'voter') 
            {{ $report->epic_voter_id }}
          @elseif($filters == 'bank') 
            {{ $report->bak_det }} 
          @endif
        </td>
        <td>{{$report->ben_no}}</td>
        @if(Auth::user()->designation_id == 'Approver')
        <td>
          @if($filters != 'bank')
          <form method="POST" action="{{ route('accept-one-approval') }}" id="table-form">
            {{ csrf_field() }}
            <input type="hidden" name="filter" id="filter" value="{{$filters}}">
            <input type="hidden" name="scheme_id" id="scheme_id" value="{{$scheme_id}}">
            <input type="hidden" name="dist_code" id="dist_code" value="{{$dist_code}}">
            @if($filters == 'ration')
            <input type="hidden" name="ration_card" id="ration_card" value="{{$report->ration_card_no}}">
            @elseif($filters == 'voter')
            <input type="hidden" name="ration_card" id="ration_card" value="{{$report->epic_voter_id}}">
            @endif
            <button class="btn btn-info btn-sm"><i class="glyphicon glyphicon-edit"></i> View</button>
          </form>
          @endif
        </td>
        @endif
      </tr>
      @endforeach        
    </tbody>
    <tfoot>
      <tr>
        <th width="10%" rowspan="1" colspan="1">Serial No</th>
        @if($filters == 'ration')
        <th width="30%" rowspan="1" colspan="1">Ration Card No</th>
        @elseif($filters == 'voter')
        <th width="30%" rowspan="1" colspan="1">Voter Id Card</th>
        @elseif($filters == 'bank')
        <th width="30%" rowspan="1" colspan="1">Bank IFSC - Account No</th>
        @endif
        <th width="30%" rowspan="1" colspan="1">No of Beneficiary</th>
        @if(Auth::user()->designation_id == 'Approver')
        <th width="30%" rowspan="1" colspan="1">Action</th>
        @endif
      </tr>
    </tfoot>            
  </table>
</section>

<script>
  $(document).ready(function() {
    $('#example').DataTable( {
      dom: 'Blfrtip',
      "paging": true,
      "pageLength":20,
      "lengthMenu": [[10, 20, 25, 50,100, -1], [10, 20, 25, 50,100, 'All']],
      "scrollX": true,
      buttons: [
       {
           extend: 'pdf',
           title: 'Report',
           messageTop:"Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Scheme : "+$( "#scheme option:selected" ).text()+" \n Criteria Type: "+$( "#filter option:selected" ).text(),
           footer: true,
           pageSize:'A4',
           // orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                columns: [0,1,2],

            }
       },
       {
           extend: 'excel',
           title: 'Report',
           messageTop:"Date:<?php echo date('d/m/Y');  ?>\n Filter Criteria:\n Scheme : "+$( "#scheme option:selected" ).text()+" \n Criteria Type: "+$( "#filter option:selected" ).text(),
           footer: true,
           pageSize:'A4',
           //orientation: 'landscape',
           pageMargins: [ 40, 60, 40, 60 ],
           exportOptions: {
                 columns: [0,1,2],
                stripHtml: false,
            }
       },
      //'pdf','excel','csv','print','copy'
      ]
    });
  });
</script>
