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