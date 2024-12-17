<div class="row" style="position: relative;">
    <div class="col-md-12">
        <h3 style="text-align: center; color:Blue;">Beneficiary ID: {{$row->id}}</h3>
        <h4 style="text-align: center; color:green;">Scheme Name: {{$scheme_name}}</h4>
    </div>
    @if ($scheme_id == 17 || $scheme_id == 18)
        <div class="col-md-6">
            <h3 style="text-align: center;">Phase:{{$row->app_phase}}</h3>
        </div>
    @endif
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Personal Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Name :</strong> {{$row->ben_fname}} {{$row->ben_mname}}
                    {{$row->ben_lname}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Gender:</strong> {{$row->gender}}</div>
            </div>
            @if(!is_null($row->dob))
                <div class="col-md-6">
                    <div><strong>Date of Birth (DD-MM-YYYY):</strong>
                        {{date('d/m/Y', strtotime($row->dob)) }}</div>

                </div>
            @endif
            <div class="col-md-6">
                <div><strong>Father's Name :</strong> {{$row->father_fname}}
                    {{$row->father_mname}}
                    {{$row->father_lname}}
                </div>
            </div>

            <div class="col-md-6">
                <div><strong>Mother's Name :</strong> {{$row->mother_fname}}
                    {{$row->mother_mname}}
                    {{$row->mother_lname}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Caste:</strong> {{$row->caste}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Marital Status:</strong> {{$row->marital_status}}</div>
            </div>
            @if ($scheme_id == 11)
                <div class="col-md-6">
                    <div><strong>Husband's Name :</strong> {{$row->husband_fname}}
                        {{$row->husband_mname}}
                        {{$row->husband_lname}}
                    </div>
                </div>
            @endif

            <div class="col-md-6">
                <div><strong>Spouse Name :</strong> {{$row->spouse_fname}}
                    {{$row->spouse_mname}}
                    {{$row->spouse_lname}}
                </div>
            </div>

            <div class="col-md-6">
                <div><strong>Monthly Family Income(Rs.):</strong>
                    {{$row->mothly_income}}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Personal Identification Number(S)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Digital Ration Card No.:</strong> {{$row->ration_card_no}}
                </div>
            </div>

            <div class="col-md-6">
                <div><strong>AHL TIN: </strong>{{$row->ahl_tin}}</div>
            </div>
            @if($scheme_id == 2)
                        @php
                            if (trim($row->aadhar_exits) != '') {
                                $sel_aadhar_exits = $row->aadhar_exits;
                            } else {
                                if (trim($row->aadhar_no) != '' && strlen($row->aadhar_no) == 12) {
                                    $sel_aadhar_exits = 1;
                                } else {
                                    $sel_aadhar_exits = 0;
                                }
                            }
                            if ($sel_aadhar_exits == 1) {
                                $sel_aadhar_exits_text = 'YES';
                            } else {
                                $sel_aadhar_exits_text = 'NO';
                            }
                        @endphp
                        <div class="col-md-6">
                            <div><strong>Applicant have the Aadhaar Number:</strong>
                                {{$sel_aadhar_exits_text}}</div>
                        </div>
                        @if($sel_aadhar_exits == 1)
                            <div class="col-md-6">
                                <div><strong>Aadhaar No., if available:</strong> {{$row->aadhar_no}}
                                </div>
                            </div>
                        @endif
                        @if($sel_aadhar_exits == 0)
                            <div class="col-md-6">
                                <div><strong>Reason for Which Aadhaar Cannot be Generated:</strong>
                                    {{$row->withoutaadhar_cause}}</div>
                            </div>
                        @endif
            @endif

            <div class="col-md-6">
                <div><strong>Aadhaar No., if available:</strong> {{$row->aadhar_no}}
                </div>
            </div>

            <div class="col-md-6">
                <div><strong>EPIC/Voter Id.No.: </strong> {{$row->epic_voter_id}}</div>

            </div>

            <div class="col-md-6">
                <div><strong>PAN, if available:</strong> {{$row->pan_no}}</div>

            </div>

            <div class="col-md-6">
                <div><strong>BPL Seq No., if avaiable:</strong> {{$row->bpl_seq_no}}
                </div>

            </div>

            <div class="col-md-6">
                <div><strong>BPL Id No., if avaiable:</strong> {{ $row->bpl_id_no }}
                </div>

            </div>

            <div class="col-md-6">
                <div><strong>BPL Total Score, if avaiable:</strong>
                    {{$row->bpl_total_score}}</div>

            </div>
        </div>

    </div>
</div>


<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Contact Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>State:</strong> West Bengal</div>

            </div>
            <div class="col-md-6">
                <div><strong>Assembly Constitution:</strong> {{$row->assembly_name}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>District:</strong> {{$district_name}}</div>
            </div>

            <div class="col-md-6">
                <div><strong>Block/Municipality/Corp:</strong>{{$block_name}}</div>
            </div>

            <div class="col-md-6">
                <div><strong>GP/Ward No.:</strong>{{$gp_name}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Village/Town/City:</strong> {{$row->village_town_city}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>House/Premise No.:</strong> {{$row->house_premise_no}}
                </div>
            </div>
            <div class="col-md-6">
                <div><strong>Post Office:</strong> {{$row->post_office}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Pin Code:</strong> {{$row->pincode}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Police Station:</strong>{{$row->police_station}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Mobile Number:</strong>{{$row->mobile_no}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Email Id., if available:</strong> {{$row->email}}
                </div>
            </div>
        </div>
    </div>
</div>


<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Bank Details</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <div><strong>Bank Name:</strong> {{$row->bank_name}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Bank Branch Name:</strong> {{$row->branch_name}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>Bank Account No.:</strong> {{$row->bank_code}}</div>
            </div>
            <div class="col-md-6">
                <div><strong>IFS Code:</strong>{{$row->bank_ifsc}}</div>
            </div>
        </div>
    </div>
</div>


<div class="box box-primary collapsed-box">
    <div class="box-header with-border">
        <h3 class="box-title">Enclosure List(Self Attested)</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                    class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            @foreach($docs as $doc)
                    @if($doc->attched_document != "")
                            <div class="col-md-4">
                                <strong>{{$doc->doc_type_name}} :</strong>]
                            </div>
                            <div class="col-md-8" style="padding-bottom: 30px; ">
                                <?php        $document_mime_type = $doc->document_mime_type;
                        if ($document_mime_type == 'image/jpeg') {
                            $image_extension = 'jpg';
                        } else if ($document_mime_type == 'image/png') {
                            $image_extension = 'png';
                        } else if ($document_mime_type == 'application/pdf') {
                            $image_extension = 'pdf';
                        }
                        $row_image = "data:image/" . $image_extension . ";base64," . $doc->attched_document; ?>
                                @if(strtolower($image_extension) == 'jpg' || strtolower($image_extension) == 'png')
                                    <div class="col-md-12" style="border:1px solid #dcdfdf">
                                        <a class="example-image-link" href="{{$row_image}}" data-lightbox="example-1">
                                            <img class="example-image" src="{{$row_image}}" alt="image-1" width="200" height="180" /></a>
                                    </div>
                                @elseif(strtolower($image_extension) == 'pdf')
                                    <div class="col-md-12" style="border:1px solid #dcdfdf">
                                        <a id="link"
                                            href="{{route('jbDownload', ['scheme_id' => $doc->scheme_id, 'created_by_dist_code' => $doc->created_by_dist_code, 'beneficiary_id' => $doc->beneficiary_id, 'document_type' => $doc->document_type])}}"
                                            target="_blank" style="color: #4324ef" width="">Download PDF
                                            Document</a>
                                    </div>
                                @else
                                    <div class="col-md-12" style="border:1px solid #dcdfdf">
                                        <p>No File Found</p>
                                    </div>
                                @endif
                            </div>
                    @endif
            @endforeach
        </div>
    </div>
</div>
<!--Additional Details  -->
@if ($scheme_id == 17 || $scheme_id == 2 || $scheme_id == 10)


    <div class="box box-primary collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title">Additional Details</h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse" fdprocessedid="pcxrxb"><i
                        class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                @if($scheme_id == 2)
                    <div class="col-md-6">
                        <div><strong>Type of Disability:</strong> {{$row->type_disability}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>Percentage of Disablity:</strong>
                            {{$row->percentage_disability}}</div>
                    </div>
                    <div class="col-md-6">
                        <div><strong>Certifying Authority:</strong> {{$row->certifying_auth}}
                        </div>
                    </div>
                @endif
                @if ($scheme_id == 17)
                    <div class="row">
                        <div class="col-md-6">
                            <h3 style="text-align: center; color:red;">Application
                                ID:{{$row->id}}

                            </h3>
                        </div>
                        <div class="col-md-6">
                            <h3 style="text-align: center;">Phase:{{$row->app_phase}}

                            </h3>
                        </div>
                        <div class="col-md-6">
                            <div><strong>Temple Type:</strong> {{$row->temple_type}}</div>
                        </div>

                    </div>
                @endif

                @if($scheme_id == 10)
                    @if($row->sm_flag == 1)
                        <div class="row color1">
                            <div class="col-md-12">
                                <h3>Sarasori Mukhyamantri</h3>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="">Mobile Number</label>
                                <span id="" class="text-danger">{{$row->sm_mobile_no}}</span>
                            </div>
                        </div>
                        <br />
                    @endif
                @endif
            </div>
        </div>
    </div>

@endif