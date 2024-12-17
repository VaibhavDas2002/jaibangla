<div class="tab-pane fade" id="fm_details">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4><b>Family Details</b></h4>
        </div>
        <div class="panel-body">
            <button type="button" name="btn_add_fm" id="btn_add_fm" class="btn btn-primary pull-right"
                data-toggle="modal" data-target="#addMemberModal">Add Family Members</button>
            <br />
            <table id="memberList" class="table table-bordred table-striped" cellspacing="0" width="100%">
                <thead>
                    <tr role="row" class="sorting_asc" style="font-size: 12px;">
                        <th>Serial No</th>
                        <th>Name </th>
                        <th>Address</th>
                        <th>Age in Years</th>
                        <th>Profession</th>
                        <th>Monthly Income(Rs.)</th>
                        <th>Relation with Applicant</th>
                        <th>Dependent on Applicant</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($type == $op_type)
                                    @if($row->member_json != '')
                                                    @php
                                                        $family_list_arr = json_decode($row->member_json);
                                                        $i = 1;
                                                    @endphp
                                                    @foreach($family_list_arr as $result)

                                                                <tr id="id_{{$i}}">
                                                                    <td>{{$i}}</td>
                                                                    <td>{{trim($result->f_member_name)}}</td>
                                                                    <td>{{$result->f_member_address}}</td>
                                                                    <td>{{$result->f_member_age}}</td>
                                                                    <td>{{$result->f_member_profession}}</td>
                                                                    <td>{{$result->f_member_monthly_income}}</td>
                                                                    <td>{{$result->f_member_relationship}}</td>
                                                                    <td>{{$result->f_member_dependent_by_applicant}}</td>
                                                                    <td class='del-mem delete-member'><a class='btn btn-danger' href='javascript:viod(0)'
                                                                            onclick='delete_member({{$i}})'>Delete</a></td>
                                                                </tr>
                                                                @php
                                                                    $i = $i + 1;
                                                                @endphp
                                                    @endforeach
                                    @endif

                    @endif




                </tbody>
            </table>
            <br />


            <div class="col-md-12" align="center">
                <button type="button" name="previous_btn_fm_details" id="previous_btn_fm_details"
                    class="btn btn-info btn-lg">Previous</button>
                <button type="button" name="btn_fm_details" id="btn_fm_details"
                    class="btn btn-success btn-lg">Next</button>
            </div>
            <br />
        </div>
    </div>
</div>




<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12" id="">
                        <label class="required-field">Name</label>
                        <input type="text" name="f_member_name" id="f_member_name" class="form-control txtOnly"
                            placeholder="Member Name" maxlength="255" value="" tabindex="4" />
                        <span id="error_f_member_name" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-12" id="">
                        <label class="required-field">Address</label>
                        <input type="text" name="f_member_address" id="f_member_address" class="form-control"
                            placeholder="Address" value="" tabindex="4" />
                        <span id="error_f_member_address" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-12" id="">
                        <label class="required-field">Age in Years</label>
                        <input type="text" name="f_member_age" id="f_member_age" class="form-control NumOnly"
                            placeholder="Age" maxlength="3" value="" tabindex="4" />
                        <span id="error_f_member_age" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-12" id="">
                        <label class="">Profession</label>
                        <input type="text" name="f_member_profession" id="f_member_profession"
                            class="form-control special-char" placeholder="Profession" maxlength="255" value=""
                            tabindex="4" />
                        <span id="error_f_member_profession" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-12" id="">
                        <label class="">Monthly Income(Rs.)</label>
                        <input type="text" name="f_member_monthly_income" id="f_member_monthly_income"
                            class="form-control price-field" placeholder="Monthly Income(Rs.)" maxlength="9" value=""
                            tabindex="85">
                        <span id="error_f_member_monthly_income" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-12" id="">
                        <label class="">Relation with Applicant</label>
                        <input type="text" name="f_member_relationship" id="f_member_relationship"
                            class="form-control special-char" placeholder="Relation with Applicant" maxlength="100"
                            value="" tabindex="4" />
                        <span id="error_f_member_relationship" class="text-danger"></span>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="">Select Dependent on Applicant (Yes/No)</label>
                        <select class="form-control " name="f_member_dependent_by_applicant"
                            id="f_member_dependent_by_applicant">
                            <option value="Yes" @if(old('dependent_by_applicant') == "Yes") selected @endif>Yes</option>
                            <option value="No" @if(old('dependent_by_applicant') == "No") selected @endif>No</option>
                        </select>
                        <span id="error_f_member_dependent_by_applicant" class="text-danger"></span>
                    </div>


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn_addMember">Add</button>
            </div>
        </div>
    </div>
</div>



<script src="{{ asset('js/FormEntry/family.js') }}"></script>

<script>
    var type = $("#type").val();
    if (type == 2 || type == 3) {
        function delete_member(sl_no) {
            //$("#memberList").find("tr:gt(0)").remove();
            var confirm_y_n = confirm("Are you sure?");
            if (confirm_y_n) {
                var row = $("#memberList tr#id_" + sl_no);
                var siblings = row.siblings();
                $("#memberList tr#id_" + sl_no).remove();
                siblings.each(function (index) {
                    // *
                    $(this)
                        .children("td")
                        .first()
                        .text(index + 1); // *
                });
            }
        }
    }
</script>