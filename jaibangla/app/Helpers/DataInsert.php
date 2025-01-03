<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Scheme;
use App\DocumentType;

class DataInsert
{
    /**
     * Checks and processes the operator's data or permissions.
     *
     * @return mixed
     */


    public static function getDocumentDetails($docId)
    {
        if (!$docId) {
            return null; // Return null if the ID is not provided
        }
        $document = DocumentType::select(
            'id',
            'is_profile_pic',
            'doc_size_kb',
            'doc_name',
            'doc_type',
            'doucument_group'
        )->where("id", $docId)->first();

        return $document ? $document->toArray() : null;
    }


    public static function insertBenAcceptRejectInfo(
        $scheme_id = null,
        $created_by_dist_code = null,
        $created_by_local_body_code = null,
        $rejected__reverted_reason = null,
        $comment_message = null,
        $created_at = null,
        $update_at = null,
        $op_type = null,
        $applicantion_id = null,
        $old_data = null,
        $new_data = null
    ) {
        $insertData = [
            'scheme_id' => $scheme_id,
            'created_by_dist_code' => $created_by_dist_code,
            'created_by_local_body_code' => $created_by_local_body_code,
            'rejected__reverted_reason' => $rejected__reverted_reason,
            'comment_message' => $comment_message,
            'created_at' => $created_at,
            'update_at' => $update_at,
            'op_type' => $op_type,
            'applicantion_id' => $applicantion_id,
            'old_data' => $old_data,
            'new_data' => $new_data,
        ];

        $inserted = DB::connection('pgsql')->table('ben_accept_reject_info')->insert($insertData);
        if ($inserted) {
            return true;
        } else {
            return false;
        }

    }

    public static function insertBenAttachDocuments(
        $beneficiaryId = null,
        $schemeId = null,
        $documentType = null,
        $attachedDocument = null,
        $createdByLevel = null,
        $createdBy = null,
        $ipAddress = null,
        $documentExtension = null,
        $documentMimeType = null,
        $distCode = null,
        $localBodyCode = null,
        $docTypeName = null,
        $datetime = null
    ) {
        $query = "SELECT jb_doc.ben_docs_insert_archive(
            in_beneficiary_id := :beneficiaryId,
            in_scheme_id := :schemeId,
            in_document_type := :documentType,
            in_attched_document := :attachedDocument,
            in_created_by_level := :createdByLevel,
            in_created_by := :createdBy,
            in_ip_address := :ipAddress,
            in_document_extension := :documentExtension,
            in_document_mime_type := :documentMimeType,
            in_created_by_dist_code := :distCode,
            in_created_by_local_body_code := :localBodyCode,
            in_doc_type_name := :docTypeName,
            in_datetime := :datetime
        );";

        $doc_inserted = DB::connection('pgsql_encwrite')->select($query, [
            'beneficiaryId' => $beneficiaryId,
            'schemeId' => $schemeId,
            'documentType' => $documentType,
            'attachedDocument' => $attachedDocument,
            'createdByLevel' => $createdByLevel,
            'createdBy' => $createdBy,
            'ipAddress' => $ipAddress,
            'documentExtension' => $documentExtension,
            'documentMimeType' => $documentMimeType,
            'distCode' => $distCode,
            'localBodyCode' => $localBodyCode,
            'docTypeName' => $docTypeName,
            'datetime' => $datetime
        ]);

        if ($doc_inserted) {
            return true;
        } else {
            return false;
        }
    }


}

