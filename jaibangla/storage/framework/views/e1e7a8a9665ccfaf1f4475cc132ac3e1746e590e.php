<div class="modal fade" id="confirm-submit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h2 class="modal-title" style="text-align: center;"> Confirm Submit </h2>

            </div>
            <div class="modal-body">
                <h4 style="text-align: center;">Are you sure you want to submit the following details?</h4>
                <!-- We display the details entered by the user here -->
                <div class="section1">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="modal_field_name"></div>
                            <div class="modal_field_value" id=""> <img
                                    src="<?php echo e(url('/')); ?>/bower_components/Emblem_of_West_Bengal.png" width="180px"
                                    height="200px"></div>
                        </div>
                        <div class="col-md-6">
                            <div align="center">
                                <div class="modal_field_name"></div>
                                <div class="modal_field_value" id="">
                                    <p>
                                    <h2>Government of West Bengal (<?php echo e($scheme_name); ?>)</h2>
                                    </p>
                                </div>
                                <p>
                                <h2>Jai Bangla Pension Scheme</h2>
                                </p>
                                <!--  <p><h3> Information Form for SC/ST Pension Scheme 2020</h3></p></div> -->
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="modal_field_name"></div>
                            <div class="modal_field_value" id=""> <img id="passport_image_view_modal" src="#" alt=""
                                    width="200px" height="200px" /></div>
                        </div>
                    </div>
                    <div class="section1">
                        <div class="row color1">
                            <div class="col-md-12">
                                <h2>Personal Details</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Application Type:</div>
                                <div class="modal_field_value" id="entry_type_modal"></div>
                            </div>
                        </div>
                        <div class="row modalDuareSarkar">
                            <div class="col-md-6">
                                <div class="modal_field_name" style="margin-right:6%;"><?php echo e($ds_phase_text); ?> Registration
                                    no.</div>
                                <div class="modal_field_value" id="ds_registration_no_modal"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="modal_field_name" style="margin-right:6%;"><?php echo e($ds_phase_text); ?> Date:</div>
                                <div class="modal_field_value" id="ds_date_modal"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Name:</div>
                                <div class="modal_field_value" id="name_modal"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="modal_field_name" style="margin-right:6%;">Gender:</div>
                                <div class="modal_field_value" id="gender_modal"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="modal_field_name" style="margin-right:6%;">Date of Birth:</div>
                                <div class="modal_field_value" id="dob_modal"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Father's Name:</div>
                                <div class="modal_field_value" id="father_name_modal"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Mother's Name:</div>
                                <div class="modal_field_value" id="mother_name_modal"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="modal_field_name" style="margin-right:6%;">Caste:</div>
                                <div class="modal_field_value" id="caste_category_modal"></div>
                            </div>

                            <div class="col-md-4 caste_certificate_no_section">
                                <div class="modal_field_name" style="margin-right:6%;">Caste Certificate No.:</div>
                                <div class="modal_field_value" id="caste_certificate_no_modal"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="modal_field_name" style="margin-right:6%;">Marital Status:</div>
                                <div class="modal_field_value" id="marital_status_modal"></div>
                            </div>
                        </div>
                        <?php if($scheme_id == 2): ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="modal_field_name" style="margin-right:6%;">Type of Disablity:</div>
                                    <div class="modal_field_value" id="disablity_type_modal"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="modal_field_name" style="margin-right:6%;">Percentage of Disablity:</div>
                                    <div class="modal_field_value" id="disablity_type_percentage_modal"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="modal_field_name" style="margin-right:6%;">Designation:</div>
                                    <div class="modal_field_value" id="disability_designation_authority_modal"></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="modal_field_name" style="margin-right:6%;">Authority Name:</div>
                                    <div class="modal_field_value" id="disablity_type_authority_modal"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Spouse Name, if applicable:</div>
                                <div class="modal_field_value" id=spouse_name_modal></div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Monthly Family Income(Rs.):</div>
                                <div class="modal_field_value" id=monthly_income_modal></div>
                            </div>
                        </div>
                    </div>
                    <div class="section1">
                        <div class="row color1">
                            <div class="col-md-12">
                                <h2>Personal Identification Number(S)</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="modal_field_name">Digital Ration Card No.:</div>
                                <div class="modal_field_value" id="ration_card_no_modal"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="modal_field_name">AHL TIN:</div>
                                <div class="modal_field_value" id="ahl_tin_modal"></div>
                            </div>
                        </div>
                        <div class="row">
                            <?php if($scheme_id == 2): ?>
                                <div class="col-md-6">
                                    <div class="modal_field_name">Applicant have the Aadhaar Number?</div>
                                    <div class="modal_field_value" id="aadhar_exits_modal"></div>
                                </div>
                                <div class="col-md-6" id="aadhar_exits_div_modal">
                                    <div class="modal_field_name">Aadhaar No.:</div>
                                    <div class="modal_field_value" id="aadhar_no_modal"></div>
                                </div>
                                <div class="col-md-6" id="withoutaadhar_div_modal">
                                    <div class="modal_field_name">Reason for Which Aadhaar Cannot be Generated</div>
                                    <div class="modal_field_value" id="withoutaadhar_cause_modal"></div>
                                </div>

                                <div class="col-md-6">
                                    <div class="modal_field_name">EPIC/Voter Id.No.:</div>
                                    <div class="modal_field_value" id="epic_voter_id_modal"></div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-6">
                                <div class="modal_field_name">Aadhaar No., if available:</div>
                                <div class="modal_field_value" id="aadhar_no_modal"></div>
                              </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">PAN, if available:</div>
                                <div class="modal_field_value" id="pan_no_modal"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="modal_field_name" style="margin-right:6%;">BPL Seq No., if avaiable:</div>
                                <div class="modal_field_value" id="bpl_seq_no_modal"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="modal_field_name" style="margin-right:6%;">BPL Id No., if avaiable:</div>
                                <div class="modal_field_value" id="bpl_id_no_modal"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="modal_field_name" style="margin-right:6%;">BPL Total Score, if avaiable:
                                </div>
                                <div class="modal_field_value" id="bpl_total_score_modal"></div>
                            </div>


                        </div>


                    </div>
                    <div class="section1 ">
                        <div class="row color1">
                            <div class="col-md-12">
                                <h2>Contact Details</h2>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">State:</div>
                                <div class="modal_field_value" id="state_modal"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="modal_field_name">Assembly Constitution:</div>
                                <div class="modal_field_value" id="asmb_cons_modal"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="modal_field_name">District:</div>
                                <div class="modal_field_value" id="district_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">Block/Municipality/Corp:</div>
                                <div class="modal_field_value" id="block_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">GP/Ward No.:</div>
                                <div class="modal_field_value" id="gp_ward_modal"></div>
                            </div>



                            <div class="col-md-12">
                                <div class="modal_field_name">Village/Town/City:</div>
                                <div class="modal_field_value" id="village_modal"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="modal_field_name">House/Premise No.:</div>
                                <div class="modal_field_value" id="house_modal"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="modal_field_name">Post Office:</div>
                                <div class="modal_field_value" id="post_office_modal"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="modal_field_name">Pin Code:</div>
                                <div class="modal_field_value" id="pin_code_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">Police Station:</div>
                                <div class="modal_field_value" id="police_station_modal"></div>
                            </div>




                            <div class="col-md-12">
                                <div class="modal_field_name">Number of years Dwelling in WB:</div>
                                <div class="modal_field_value" id="residency_period_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">Mobile Number:</div>
                                <div class="modal_field_value" id="mobile_no_modal"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="modal_field_name">Email Id., if available:</div>
                                <div class="modal_field_value" id="email_modal"></div>
                            </div>

                        </div>

                    </div>

                    <div class="section1">
                        <div class="row color1">
                            <div class="col-md-12">
                                <h2>Bank Account Details</h2>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="modal_field_name">Bank Name:</div>
                                <div class="modal_field_value" id="name_of_bank_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">Bank Branch Name:</div>
                                <div class="modal_field_value" id="bank_branch_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">Bank Account No.:</div>
                                <div class="modal_field_value" id="bank_account_number_modal"></div>
                            </div>

                            <div class="col-md-12">
                                <div class="modal_field_name">IFSC Code:</div>
                                <div class="modal_field_value" id="bank_ifsc_code_modal"></div>
                            </div>
                        </div>
                    </div>

                    <?php if($scheme_id == 17): ?>
                        <div class="section1">
                            <div class="row color1">
                                <div class="col-md-12">
                                    <h2>Land Details (In case of Dwelling House)</h2>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="modal_field_name">Name of the Mouza:</div>
                                    <div class="modal_field_value" id="mouza_name_modal"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">J.L.No:</div>
                                    <div class="modal_field_value" id="land_jlno_modal"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="modal_field_name">Khatian No:</div>
                                    <div class="modal_field_value" id="khatian_no_modal"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">Plot No:</div>
                                    <div class="modal_field_value" id="plot_no_modal"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">Area:</div>
                                    <div class="modal_field_value" id="land_area_modal"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">In the Name of:</div>
                                    <div class="modal_field_value" id="land_holdername_modal"></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($scheme_id == 13): ?>

                        <div class="section1">
                            <div class="row color1">
                                <div class="col-md-12">
                                    <h2>Land Details</h2>
                                </div>
                            </div>
                            <div class="row">
                                <table id="landListModal" class="table table-bordred table-striped" cellspacing="0"
                                    width="100%">
                                    <thead>
                                        <tr role="row" class="sorting_asc" style="font-size: 12px;">
                                            <th>Serial No</th>
                                            <th>Block</th>
                                            <th>Mouza</th>
                                            <th>JL NO.</th>
                                            <th>Khatian No.</th>
                                            <th>Daag No.</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>

                            <div class="row">

                                <div class="col-md-12">
                                    <div class="modal_field_name">Select Cultivation by Applicant(Yes/No):</div>
                                    <div class="modal_field_value" id="cultivation_by_applicant_modal"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="modal_field_name">Source of Present Income:</div>
                                    <div class="modal_field_value" id="source_income_modal"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="modal_field_name">Any other Benefits received:</div>
                                    <div class="modal_field_value" id="any_other_benefitis_modal"></div>
                                </div>

                            </div>


                        </div>


                        <div class="section1">
                            <div class="row color1">
                                <div class="col-md-12">
                                    <h2>Family Members</h2>
                                </div>
                            </div>
                            <div class="row">
                                <table id="memberListModal" class="table table-bordred table-striped" cellspacing="0"
                                    width="100%">
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

                                    </tbody>
                                </table>
                            </div>


                        </div>
                    <?php endif; ?>


                    <div class="section1">
                        <div class="row color1">
                            <div class="col-md-12">
                                <h2>Self Declaration</h2>
                            </div>
                        </div>
                        <div class="row">
                            <?php if(in_array($scheme_id, [1, 3, 5, 6, 7, 10, 11, 13, 17])): ?>
                                <div class="col-md-12 aadhar-text-modal">
                                    <div class="modal_field_name">I <span id="av_status_modal">give</span> consent to the
                                        use of the Aadhaar No.for authenticating my identity for social security pension (In
                                        case Aadhaar no. provided by the applicant)</div>
                                </div>
                            <?php endif; ?>
                            <div class="col-md-12">
                                <div class="modal_field_name">Presently, I am reciving following pension(s) from:</div>
                                <div class="modal_field_value" id="receive-pension-modal"></div>
                            </div>
                            <?php if(in_array($scheme_id, [2, 10, 11, 13, 17])): ?>
                                <div class="col-md-12" style="
                                                                                                float: left;
                                                                                                font-weight: 700;
                                                                                                margin-right: 1%;
                                                                                                padding-top: 1%;
                                                                                                margin-top: 1%;">
                                    <div class="">In case the applicant is receiving pension from other sources:</div>
                                    <ul>
                                        <li>1.<span id="receiving_pension_other_source_1_txt"></span></li>
                                        <li>2.<span id="receiving_pension_other_source_2_txt"></span></li>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if(in_array($scheme_id, [1, 2, 3, 5, 6, 7, 10, 11, 13, 17])): ?>
                                <div class="col-md-12">
                                    <div class="modal_field_name">Presently, I am receiving the following social Security
                                        Pension/s </div>
                                    <div class="modal_field_value" id="checkbox-tick-modal">Nil</div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <?php if(in_array($scheme_id, [11])): ?>
                                <div class="col-md-12">
                                    <div class="modal_field_name">I hereby declare that i have not done remarriage</div>
                                    <div class="modal_field_value" id="receive-pension-modal"></div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="row">
                            <?php if(in_array($scheme_id, [17])): ?>
                                <div class="col-md-12">
                                    <div class="modal_field_name">I <span id="ssp_y_n_modal"></span> a beneficiary
                                        of any other Social Security pension scheme or a recipient of Government
                                        pension or pension from any other organization.
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">I <span id="pucca_house_y_n_modal"></span> Pucca
                                        dwelling house.</div>
                                </div>
                            <?php endif; ?>
                            <?php if(in_array($scheme_id, [1, 3, 5, 6, 7, 10, 13, 17])): ?>
                                <div class="col-md-12">
                                    <div class="modal_field_name">In the event of my death, I hereby nominate
                                        (Please mention Name, Address &
                                        Relationship)</div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">Name:</div>
                                    <div class="modal_field_value" id="nominate_name_modal"></div>
                                </div>

                                <div class="col-md-12">
                                    <div class="modal_field_name">Address:</div>
                                    <div class="modal_field_value" id="nominate_address_modal"></div>
                                </div>
                                <div class="col-md-12">
                                    <div class="modal_field_name">Relationship:</div>
                                    <div class="modal_field_value" id="nominate_relationship_modal"></div>
                                </div>
                                <?php if($scheme_id == 2): ?>
                                    <div class="col-md-12">
                                        <div class="modal_field_name">to receive the rest amount payable to me till my death
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <div class=""><img src="<?php echo e(asset('images/ZKZg.gif')); ?>" id="submit_loader" width="150px"
                            height="150px">
                    </div>
                </div>
                <div class="modal-footer" style="text-align: center;">
                    <button type="button" class="btn btn-default btn-lg" data-dismiss="modal"
                        modal-cancel>Cancel</button>
                    <button type="submit" id="submit" value="Submit" name="final_submit"
                        class="btn btn-success success btn-lg modal-submit">Submit </button>
                    <button type="button" id="submitting" value="Submit" class="btn btn-success success btn-lg"
                        disabled>Submitting please wait</button>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo e(asset('js/FormEntry/submit_modal.js')); ?>"></script>