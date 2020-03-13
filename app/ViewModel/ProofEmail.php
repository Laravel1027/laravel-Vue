<?php
namespace App\ViewModel;

class ProofEmail
{
    public $email;
    public $name;
    public $project_name;
    public $company_name;
    public $project_hash;
    public $project_id;
    public $email_type;
    public $sent_by;
    public $sent_by_name;
    public $revision_id;

    public function __construct($email, $name, $project_name, $company_name, $project_hash, $project_id, $email_type, $sent_by, $revision_id, $sent_by_name, $final_files = [], $comment = '', $issue_number = '')
    {
        $this->email = $email;
        $this->name = $name;
        $this->project_name = $project_name;
        $this->company_name = $company_name;
        $this->project_hash = $project_hash;
        $this->project_id = $project_id;
        $this->email_type = $email_type;
        $this->sent_by = $sent_by;
        $this->revision_id = $revision_id;
        $this->sent_by_name = $sent_by_name;
        $this->final_files = $final_files;
        $this->comment = $comment;
        $this->issue_number = $issue_number;
    }
}