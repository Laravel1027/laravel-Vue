<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProjectResource
 * API Resource for projects
 * @package App\Http\Resources\API\V1
 */
class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $thumbnail = '';
        $lastRevision = $this->revisions->last();
        if ($lastRevision) {
            $lastProof = $lastRevision->proofs->last();
            $lastProof
                ? $thumbnail = url('/storage/'.$lastProof->projectFiles->thumb_path)
                : '';
        }

        return [
            'name' => $this->name,
            'company' => $this->company,
            'project_hash' => $this->project_hash,
            'thumbnail' => $thumbnail ? $thumbnail : '',
            'url' => url('/loadProofer/'.$this->project_hash.'/'.$lastRevision->id)
        ];
    }
}
