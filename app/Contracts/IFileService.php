<?php

namespace App\Contracts;

interface IFileService
{
   public function uploadFile(array $data);
   public function deleteFile($id, $type = null);
   public function deleteFinalFile($projectId, $id);
   public function deleteFinalFiles($data);
   public function getById($id);
   public function convertingPDF($data);
}