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
        background-image: url('../images/ajaxgif.gif');
        background-repeat: no-repeat;
        background-position: center;
        z-index: 10000000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        /* For IE8 and earlier */
    }

    .panel-heading .accordion-toggle:after {
        /* symbol for "opening" panels */
        font-family: 'Glyphicons Halflings';
        /* essential for enabling glyphicon */
        content: "\e114";
        /* adjust as needed, taken from bootstrap.css */
        float: right;
        /* adjust as needed */
        color: grey;
        /* adjust as needed */
    }

    .panel-heading .accordion-toggle.collapsed:after {
        /* symbol for "collapsed" panels */
        content: "\e080";
        /* adjust as needed, taken from bootstrap.css */
    }
</style>
@extends('layouts.app-template-datatable_new')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Frequently Asked Questions
        </h1>
        <ol class="breadcrumb">
            <i class="fa fa-clock-o"></i> Date : <span style="font-size: 12px; font-weight: bold;"><span class='date-part'></span>&nbsp;&nbsp;<span class='time-part'></span></span>
        </ol>
    </section>
    <section class="content">
        <div class="box box-default">
            <div class="box-body">
                <div class="panel-group" id="accordion">
                    @php $i=1; @endphp
                    @foreach($faq as $data)
                    @php $href = "#collapse".$i; $href_body = "collapse".$i; @endphp
                    @php 
                        if($i == 1) {
                            $active_class = "panel-collapse collapse";
                        } else {
                            $active_class = "panel-collapse collapse";
                        } 
                    @endphp
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="@php print $href; @endphp">
                                    @php print 'Q '.$i.'.'; @endphp {{ $data->question }}
                                </a>
                            </h4>
                        </div>
                        <div id="@php print $href_body; @endphp" class="@php print $active_class; @endphp">
                            <div class="panel-body">
                                <b>Answer :</b> {{ $data->answer }} <br/>
                                @if($data->link_url != '')
                                    <br/><b>URL : </b> {{ $data->link_url }} 
                                @endif
                                @if($data->image_url != '')
                                <div align="center">
                                    <br/><img class="img-thumbnail" src="<?php echo asset("jaibangla/storage/app/faq_screenshot/$data->image_url")?>"></img>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @php $i++; @endphp
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection
<script src="{{ asset ("/bower_components/AdminLTE/plugins/jQuery/jquery-2.2.3.min.js") }}"></script>
<script>
    $(document).ready(function() {
        // Live Clock
        var interval = setInterval(function() {
            var momentNow = moment();
            $('.date-part').html(momentNow.format('DD-MMMM-YYYY'));
            $('.time-part').html(momentNow.format('hh:mm:ss A'));
        }, 100);
    });
</script>