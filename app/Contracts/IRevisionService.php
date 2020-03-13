<?php

namespace App\Contracts;

interface IRevisionService
{
   public function create($project_id);
   public function remove($project_id);
   public function getRevisionById($revision_id);
   public function getLastRevision($project_id);
   public function getRevisionByProject($project_id);
   public function changeRevisionStatus($revision_id, $status);
}