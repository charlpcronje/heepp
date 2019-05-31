<?php
use core\extension\database\Model;
use core\extension\ui\view;

class Project extends Console {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index() {
        $this->setData('projects',Model::mold('project','activeMain'));
        $this->setLeftNav('views/projects/nav-list.phtml');
        $this->setWSLeft('views/projects/dashboard.phtml');
    }

    public function openProject($projectId) {
        $project = Model::mold('project')->find($projectId);
        $this->setData('project',$project);

        // Set Project Name
        $this->setHtml('#dashboard-project-name',$project->project_name);
        // Set Project owner company name
        $this->setHtml('#dashboard-project-company-name',$project->company_name);
        // Set project dashboard content
        $this->setHtml('#project-dashboard-content',view::phtml('views/projects/openProject.phtml'));
        // Set current project in JS
        $this->setVar('project',$projectId,'core.console.projects.project');
    }
}
