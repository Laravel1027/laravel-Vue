<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileRequest;
use App\Contracts\IFileService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\ProjectFile;
use App\Project;
use App\Proof;

class FileController extends Controller
{
    private $fileService;

    public function __construct(IFileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function upload(Request $request)
    {   
        try {
        // print_r($request);exit;
            if ($request->photos->getMimeType() == 'application/pdf') {
                $convertedImages = $this->fileService->convertingPDF($request->all());

                return ApiResponse::success('File converted successfully', $convertedImages);
            } else {
                $project_file = $this->fileService->uploadFile($request->all());
            // exit($project_file);
                return ApiResponse::success('File saved successfully', $project_file);
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error saving file');
        }
    }

    public function deleteFile($id, Request $request)
    {
        if ($id > 0) {
            $type = $request->get('type');
            try {
                $delete_result = $this->fileService->deleteFile($id, $type);
                return ApiResponse::success('File removed successfully', $delete_result);
            } catch (\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'Error removing the file');
            }
        }
        return ApiResponse::error('001', 'Error removing the file');
    }

    /**
     * Delete project final file
     * @param $projectId
     * @param $id
     * @return array
     */
    public function deleteFinalFile($projectId, $id)
    {
        try {
            $delete_result = $this->fileService->deleteFinalFile($projectId, $id);
            if ($delete_result) {
                $object_type = get_class($delete_result);
                $reflection = new \ReflectionClass($object_type);
                return ApiResponse::success('File removed successfully', [$reflection->getShortName()]);
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error removing the file');
        }
    }

    /**
     * Delete project final files
     * @param Request $request
     * @return array
     */
    public function deleteFinalFiles(Request $request)
    {
        try {
            $delete_result = $this->fileService->deleteFinalFiles($request->all());
            return ApiResponse::success('Files removed successfully', []);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error removing the files');
        }
    }

    public function getById($file_id)
    {
        if ($file_id > 0) {
            $file = $this->fileService->getById($file_id);
            if ($file) {
                return ApiResponse::success('Resource resolved successfully', $file);
            }
        }
        return ApiResponse::error('001', 'Error resolving the file');
    }

    public function videoCaptureUpload(){
        $projectId = $_POST['projectId'];
        $revision_id = $_POST['revision_id'];
		$capture_time = $_POST['capture_time'];
		
        // $upload_dir = public_path();
        // exit($upload_dir);
        $upload_dir = public_path() . "/../storage/app/public/pictures/proof/";

        $img = $_POST['imageData'];
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $name = $this->generateRandomString(40) . '.jpeg';

        $file = $upload_dir . $name;
        $success = file_put_contents($file, $data);

        $for_db_path = "pictures/proof/" . $name;
        $project_file = new ProjectFile();
        $project = new Project();
        $data = [];
        $data['path'] = $for_db_path;
        $data['thumb_path'] = $for_db_path;
        $data['owner_type'] = "proof";
        $data['file_type'] = "picture";
		$data['capture_time'] = $capture_time;

        $project_file->path = $data['path'];
        $project_file->thumb_path = $data['thumb_path'];
        $project_file->owner_type = $data['owner_type'];
        $project_file->file_type = $data['file_type'];
		$project_file->capture_time = $data['capture_time'];
		
        if (!array_key_exists('revision_id', $data)) {
            $project_file->revision_id = $project->getActiveRevision($projectId)->id;
        } else {
            $project_file->revision_id = $data['revision_id'];
        }
        $project_file->save();

        $proof = new Proof();
        $proof->status = 'created';
        $proof->projectFiles()->associate($project_file);
        $proof->revision()->associate($project_file->revision);
        $proof->save();
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}