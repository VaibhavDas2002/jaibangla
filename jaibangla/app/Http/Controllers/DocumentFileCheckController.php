<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DocumentFileCheckController extends Controller
{
    public function indexFileCheck() {
        return view('check_file_documents');
    }

    public function checkingFileDocuments(Request $request)
    {
        // dd($request->all());
        if ($request->hasFile('doc_file')) {
            $doc_file = $request->file('doc_file');
            $img_data = file_get_contents($doc_file);
            $u_extension_file = $doc_file->getClientOriginalExtension();
            $u_extension = strtolower($u_extension_file);
            $mime_type = $doc_file->getMimeType();
            $errorMsg='File format is right.';
            $extension='';
            if (strtolower($mime_type) == 'image/jpeg') {
                if ($u_extension == 'jpg' || $u_extension == 'jpeg') {
                    $extension = $u_extension;
                } else {
                    $errors = array();
                    $errorMsg = "Invalid file format!!";
                }
            } else if (strtolower($mime_type) == 'image/png') {
                $extension = 'png';
            } else if (strtolower($mime_type) == 'image/gif') {
                $extension = 'gif';
            } else if (strtolower($mime_type) == 'application/pdf') {
                $extension = 'pdf';
            } else {
                $errors = array();
                $errorMsg = "Invalid file format!!";
            }
            if ($u_extension != $extension) {
                $errors = array();
                $errorMsg = "Invalid file format!!";
            }

            print "<h2>".$errorMsg . "</h2>";
            echo "<h3>Extension : " . $u_extension . ", Type : " . $mime_type . "</h3>"; 
            // $base64 = base64_encode($img_data);
        }
        else {
            print "<h2>File not found!!</h2>";
        }
    }
}
