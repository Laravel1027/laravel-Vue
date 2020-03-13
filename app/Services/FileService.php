<?php

namespace App\Services;

use App\CommentFile;
use App\Events\PdfPageConverted;
use App\Events\PdfReaded;
use App\Events\PdfUploaded;
use App\IssueFile;
use App\Project;
use App\ProjectFile;
use App\UnreadComment;
use Illuminate\Support\Facades\Storage;
use Laravel\Spark\Notification;
use App\Contracts\IFileService;
use Illuminate\Support\Str;
use App\Issue;
use App\Comment;
use Image;

class FileService implements IFileService
{
    public function uploadFile(array $data, $requestType = null)
    {
        $proofService = new ProofService(
            new NotificationService(new Notification()),
            new UnreadCommentsService(new UnreadComment())
        );

        if ($requestType) {
            try {
                if ($requestType == 'PDF') {
                    $file = $data['photos'];

                    $stream_image = Image::make(Storage::disk('public')->get($file))->encode('jpg', 95)->stream();
                    $stream_thumb = Image::make(Storage::disk('public')->get($file))->encode('jpg', 95)->stream();
                    $thumb = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';
                    $big = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';

                    Storage::disk('public')->put($thumb, $stream_thumb);
                    Storage::disk('public')->put($big, $stream_image);
                    Storage::disk('public')->delete($file);
                } else {
                    $name = 'pictures/' . Str::random(40) . '.jpg';
                    Storage::disk('public')->put($name, $data['photos']);
                    $stream_image = Image::make(Storage::disk('public')->get($name))->encode('jpg', 95)->stream();

                    $stream_thumb = Image::make(Storage::disk('public')->get($name))->encode('jpg', 95)->stream();
                    $thumb = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';
                    $big = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';

                    Storage::disk('public')->put($thumb, $stream_thumb);
                    Storage::disk('public')->put($big, $stream_image);
                    Storage::disk('public')->delete($name);
                }
            } catch (\Exception $e) {
                return null;
            }
        } else {
            if ($data['owner_type'] == 'final') {
                $file = Storage::disk('public')->putFile('final_files', $data['photos']);
                $project = Project::where('id', $data['project_id'])->first();
                $finalFile = $project->finalFiles()->create(['path' => $file]);
                return [
                    'id' => $finalFile->id,
                    'path' => $finalFile->path,
                ];
            }
            $image = Storage::disk('public')->putFile('pictures', $data['photos']);

            $stream_image = Image::make(Storage::disk('public')->get($image))->encode('jpg', 95)->stream();

            $stream_thumb = Image::make(Storage::disk('public')->get($image))->encode('jpg', 95)->stream();

            $thumb = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';
            $big = 'pictures/' . $data['owner_type'] . '/' . Str::random(40) . '.jpg';

            Storage::disk('public')->put($thumb, $stream_thumb);
            Storage::disk('public')->put($big, $stream_image);
            Storage::disk('public')->delete($image);
        }

        if ($thumb && $big) {
            $data['thumb_path'] = $thumb;
            $data['path'] = $big;

            switch ($data['owner_type']) {
                case 'issue':
                    $issue = Issue::findOrFail($data['issue_id']);
                    $uploadedFile = $issue->files()->create([
                        'path' => $big,
                        'thumb_path' => $thumb,
                        'user_id' => $data['user_id']
                    ]);
                    break;
                case 'comment':
                    $comment = Comment::findOrFail($data['comment_id']);
                    $uploadedFile = $comment->files()->create([
                        'path' => $big,
                        'thumb_path' => $thumb,
                        'user_id' => $data['user_id']
                    ]);
                    break;
                case 'proof':
                    $uploadedFile = ProjectFile::store($data);
                    $proof = $proofService->createProof($uploadedFile);
                    break;
            }

            return [
                'id' => $uploadedFile->id,
                'path' => $uploadedFile->path,
                'thumb' => $uploadedFile->thumb_path
            ];
        }
    }

    public function getById($id)
    {
        if ($id > 0) {
            return ProjectFile::findOrFail($id);
        }
    }

    /**
     * @param $id
     * @param $type
     * @return mixed
     */
    public function deleteFile($id, $type = null)
    {
        switch ($type) {
            case 'issue':
                $file = IssueFile::findOrFail($id);
                break;
            case 'comment':
                $file = CommentFile::findOrFail($id);
                break;
            default:
                $file = ProjectFile::findOrFail($id);
                break;
        }

        $path = $file->path;
        $thumb_path = $file->thumb_path;
//        $owner_type = $file->owner_type;

        //Deleting file from server
        Storage::disk('public')->delete($path);
        Storage::disk('public')->delete($thumb_path);

        //TODO: Change Logic when deleting single proof
        //Updating data in database
//        if ($owner_type == 'proof') {
//            //Deleting file data from database
//            $file->delete();
//            $proof = Proof::where('project_files_id', $id)->first();
//            $proof->delete();
//            return $proof;
//        }

        return $file->delete();
    }

    /**
     * Delete final file
     * @param $projectId
     * @param $id
     * @return mixed
     */
    public function deleteFinalFile($projectId, $id)
    {
        $project = Project::where('id', $projectId)->first();
        $file = $project->finalFiles()->where('id', $id)->first();
        Storage::disk('public')->delete($file->path);
        $file->delete();
        return $file;
    }

    /**
     * Delete project final files
     * @param $data
     * @return mixed
     */
    public function deleteFinalFiles($data)
    {
        $project = Project::where('id', $data['projectId'])->first();
        $files = $project->finalFiles()->whereIn('id', $data['ids'])->get();
        if ($files) {
            foreach ($files as $file) {
                Storage::disk('public')->delete($file->path);
                $file->delete();
            }
        }
        return $project;
    }

    /**
     * Converting PDF pages to images
     * @param $data
     * @return array
     * @throws \ImagickException
     */
    public function convertingPDF($data)
    {
        $convertedImages = [];

        //Saving PDF file as temporary file
        $pdf = Storage::disk('public')->putFile('pdf', $data['photos']);

        //Broadcast PdfUploaded event
        broadcast(new PdfUploaded());

        if ($data['owner_type'] == 'final') {
            $project = Project::where('id', $data['project_id'])->first();
            $finalFile = $project->finalFiles()->create(['path' => $pdf]);
            return [
                'id' => $finalFile->id,
                'path' => $finalFile->path,
                'type' => 'pdf'
            ];
        }

        //Reading PDF file
        $pdflib = new \ImalH\PDFLib\PDFLib();
        $pdflib->setPdfPath('storage/' . $pdf);
        $pdflib->setOutputPath('storage/pictures');
        $pdflib->setImageFormat(\ImalH\PDFLib\PDFLib::$IMAGE_FORMAT_JPEG);
        $pdflib->setDPI(300);
        $pdflib->setPageRange(1, $pdflib->getNumberOfPages());
        $pdflib->convert();

        //Get total number of pages
        $totalPages = $pdflib->getNumberOfPages();

        //Broadcast PdfReaded event
        broadcast(new PdfReaded($totalPages));


        //Create proofs from converted images
        for ($i = 1; $i <= $totalPages; $i++) {
            $data['photos'] = 'pictures/page-'.$i.'.jpg';
            $file = $this->uploadFile($data, 'PDF');

            //Broadcast PdfPageConverted event
            broadcast(new PdfPageConverted($i,$file ? true : false));

            //Saving converted image data
            if ($file) {
                $convertedImages[] = [
                    'id' => $file['id'],
                    'path' => $file['path'],
                    'thumb' => array_key_exists('thumb_path', $file) ? $file['thumb_path'] : ''
                ];
            }
        }

        //Deleting temporary PDF file
        Storage::disk('public')->delete($pdf);

        //Return converted images
        return $convertedImages;
    }
}