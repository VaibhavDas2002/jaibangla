<style>
    .required-field::after {
        content: "*";
        color: red;
    }
</style>

<div class="row">
    <!-- Hidden Input for District Code -->
    <input type="hidden" name="dist_code" id="dist_code" value="{{ $dist_code }}" class="client-js-district1">

    <!-- Urban/Rural Filter -->
    <div class="form-group col-md-3">
        <label for="filter_1" class="control-label ">Select Filter Criteria: Urban/Rural</label>
        <select name="filter_1" id="filter_1" class="form-control client-js-urban1" required>
            <option value="">-----Select----</option>
            <option value="1">Urban</option>
            <option value="2">Rural</option>
        </select>

        <span id="error_filter_1" class="text-danger"></span>
    </div>

    <!-- Block/Sub Division Filter -->
    <div class="form-group col-md-3">
        <label for="filter_2" class="control-label ">Select Filter Criteria: Block/Sub Division</label>
        <select name="filter_2" id="filter_2" class="form-control client-js-localbody1" required>
            <option value="">-----Select----</option>
        </select>
        <span id="error_filter_2" class="text-danger"></span>
    </div>

</div>

<script src="{{ URL::asset('js/master-data-v2.js') }}"></script>



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
    });
</script>