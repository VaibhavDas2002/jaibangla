<!-- Hidden Input for District Code -->
<input type="hidden" name="dist_code" id="dist_code" value="<?php echo e($dist_code); ?>" class="client-js-district1">

<!-- Urban/Rural Filter -->
<div class="form-group col-md-3">
    <label for="rural_urban_code" class="control-label ">Select Filter Criteria: Urban/Rural</label>
    <select name="rural_urban_code" id="rural_urban_code" class="form-control client-js-urban1" required>
        <option value="">-----Select----</option>
        <option value="1">Urban</option>
        <option value="2">Rural</option>
    </select>

    <span id="error_filter_1" class="text-danger"></span>
</div>

<!-- Block/Sub Division Filter -->
<div class="form-group col-md-3">
    <label for="blk_ulb_code" class="control-label ">Select Filter Criteria: Block/Sub Division</label>
    <select name="blk_ulb_code" id="blk_ulb_code" class="form-control client-js-localbody1" required>
        <option value="">-----Select----</option>
    </select>
    <span id="error_filter_2" class="text-danger"></span>
</div>

<!-- GP/Ward Filter

<div class="form-group col-md-4" id="divBodyCode">
    <label class="required-field">GP/Ward No</label>

    <select name="gp_ward" id="gp_ward" class="form-control  client-js-gpward1">
        <option value="">--Select --</option>


    </select>
    <span id="error_gp_ward" class="text-danger"></span>
</div> -->



<script src="<?php echo e(URL::asset('js/master-data-v2.js')); ?>"></script>



<script>
    $(document).ready(function () {
        $('.client-js-urban1').change(function () {
            select_district_code = $('.client-js-district1').val();
            select_body_type = $('.client-js-urban1').val();
            var htmlOption = '<option value="">--Select--</option>';
            if (select_body_type == 2) {
                $.each(blocks, function (key, value) {
                    if (value.district_code == select_district_code) {
                        htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                    }
                });
            } else if (select_body_type == 1) {
                $.each(subDistricts, function (key, value) {
                    if (value.district_code == select_district_code) {
                        htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
                    }
                });
            }
            else {
                $('.client-js-localbody1').html('<option value="">--Select--</option>');
            }

            $('.client-js-localbody1').html(htmlOption);
        });

        // $('.client-js-localbody1').change(function () {
        //     select_district_code = $('.client-js-district1').val();
        //     select_body_type = $('.client-js-urban1').val();
        //     selected_body_code = $('.client-js-localbody1').val();
        //     var htmlOption = '<option value="">--Select--</option>';
        //     if (select_body_type == 2) {
        //         $.each(gps, function (key, value) {
        //             if ((value.district_code == select_district_code) && (value.block_code == selected_body_code)) {
        //                 htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
        //             }
        //         });
        //     } else if (select_body_type == 1) {
        //         $.each(ulb_wards, function (key, value) {
        //             if ((value.urban_body_code == selected_body_code)) {
        //                 htmlOption += '<option value="' + value.id + '">' + value.text + '</option>';
        //             }
        //         });
        //     }
        //     $('.client-js-gpward1').html(htmlOption);
        // });

    });
</script>