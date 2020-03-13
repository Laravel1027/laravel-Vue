<?php

namespace App\Http\Controllers\API\V1;


use App\Contracts\API\V1\IAPIProjectService;
use App\Contracts\API\V1\IAPIAuthService;
use App\Contracts\IFileService;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\ErrorResource;
use App\Http\Resources\API\V1\ExtensionResource;
use App\Http\Resources\API\V1\ProjectResource;
use App\Http\Resources\API\V1\SuccessResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Image;

/**
 * Class APIController
 * Controller for API requests
 * @package App\Http\Controllers\API\V1
 */
class APIController extends Controller
{
    protected $projectService;
    protected $fileService;
    protected $authService;

    /**
     * APIController constructor.
     * @param IAPIProjectService $projectService
     * @param IAPIAuthService $authService
     * @param IFileService $fileService
     */
    public function __construct(IAPIProjectService $projectService, IAPIAuthService $authService, IFileService $fileService)
    {
        $this->projectService = $projectService;
        $this->fileService = $fileService;
        $this->authService = $authService;
    }

    /**
     * Get required data for Prooflo extension
     * @param $userId
     * @param $projectId
     * @return ErrorResource|ExtensionResource
     */
    public function getExtensionData($userId, $projectId)
    {
        try {
            $user = $this->authService->getUserById($userId);
            $project = $this->projectService->getProjectById($projectId);
            $lastRevision = $project->revisions->last();
            $response = [
                'token' => $user->token->token,
                'project_hash' => $project->project_hash,
                'redirect_url' => url('/loadProofer/'.$project->project_hash.'/'.$lastRevision->id)
            ];

            return new ExtensionResource((object)$response);
        } catch (\Exception $e) {
            report($e);

            $response = [
                'status' => 'API exception error occurred',
                'code' => 400,
                'message' => $e->getMessage(),
            ];

            return new ErrorResource((object)$response);
        }
    }

    /**
     * Get user projects
     * @param Request $request
     * @return ErrorResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function projects(Request $request)
    {
        try {
            $user = $this->authService->getUserByToken($request->header('Authorization'));
            $projects = $this->projectService->getProjects($user);

            return ProjectResource::collection($projects);
        } catch (\Exception $e) {
            report($e);

            $response = [
                'status' => 'Error: API exception error occurred',
                'code' => 400,
                'message' => $e->getMessage(),
            ];

            return new ErrorResource((object) $response);
        }
    }

    /**
     * Upload project proofs
     * @param Request $request
     * @return ErrorResource|SuccessResource
     */
    public function uploadProofs(Request $request)
    {
        $projectHash = $request->project_hash;

        if ($projectHash) {
            $proofs = $request->proofs;

            if ($proofs) {
                try {
                    $user = $this->authService->getUserByToken($request->header('Authorization'));
                    $project = $this->projectService->getProjectByHash($projectHash);
                    $lastRevision = $project->revisions->last();
                    foreach ($proofs as $proof) {
                        $image = base64_decode($proof);
                        $stream_image = Image::make($image)->encode('jpg')->stream();

                        $uploadData = [
                            'project_id' => $project->id,
                            'revision_id' => $lastRevision->id,
                            'file_type' => 'picture',
                            'owner_type' => 'proof',
                            'photos' => $stream_image,
                            'user_id' => $user->id
                        ];

                        $this->fileService->uploadFile($uploadData, 'API');
                    }

                    $resposne = [
                        'code' => 200,
                        'message' => 'Your proofs uploaded successfully',
                        'redirect_url' => url('/loadProofer/'.$projectHash.'/'.$lastRevision->id)
                    ];

                    return new SuccessResource((object) $resposne);


                } catch (\Exception $e) {
                    report($e);

                    $response = [
                        'status' => 'Error: API exception error occurred',
                        'code' => 400,
                        'message' => $e->getMessage(),
                    ];

                    return new ErrorResource((object) $response);
                }
            }

            $response = [
                'status' => 'Validation Error',
                'code' => 422,
                'message' => 'Project images are required'
            ];

            return new ErrorResource((object) $response);
        }

        $response = [
            'status' => 'Validation Error',
            'code' => 422,
            'message' => 'Project hash key is required'
        ];

        return new ErrorResource((object) $response);
    }
}