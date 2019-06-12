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
        $this->setHtml('#dashboard-project-name',$project->project_name);

        $this->setHtml('#project-dashboard-content',view::phtml('views/projects/openProject.phtml'));
    }
}
