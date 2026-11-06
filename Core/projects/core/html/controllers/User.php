<?php
use core\extension\database\Model;
use core\extension\ui\view;

class User extends Controller {
    public function __construct () {
        parent::__construct(__CLASS__);
    }

    public function index() {
        $this->setData('users',Model::mold('user')->getAll());
        $this->setHeading('View Users')->addBreadcrumb('Users')->addBreadcrumb('View Users','User');
        $this->setBody(View::mold('views/users/index.phtml'));
    }

    public function getUserDetails() {
        return $this->session('user');
    }

    public function login() {
        $user = Model::mold('user','byEmailAndPassword',[
            'email'    => $this->input('email'),
            'password' => $this->input('password')
        ])->current();

        if (is_object($user) && isset($user->id)) {
            $userStd               = new \stdClass();
            foreach($user as $key => $value) {
                $userStd->{$key} = $value;
            }
            $this->session('user',$userStd);
            $this->setNotify('success','Login Successful');
            $this->refreshPage();
        } else {
            $this->setNotify('error','E-Mail and Password does not match');
        }
    }

    public function signOut() {
        unset($_SESSION);
        $this->forget('session.user');
        $this->refreshPage();
    }

    public function myProfile() {
        $userId = $this->session('user.id');
        $this->updateUser($userId);
    }

    public function addUser() {
        $this->setHeading('Users - Add User');
        $this->addBreadcrumb('Users','User');
        $this->addBreadcrumb('Add User','User/addUser');

        $this->setData('companies',Model::mold('company')->getAll());
        $this->setData('permissions',(new UserPermissions())->getPermissions());
        $this->setData('userTypes',Model::mold('userType')->getAll());
        $this->setData('statuses',$this->getStatuses('all'));
        $this->setData('now',CURRENT_TIMESTAMP);
        $this->setBody(View::mold('views/users/viewUser.phtml'));
    }

    public function updateUser($userId) {
        $this->setData('user',Model::mold('user')->find($userId));
        $this->setData('companies',Model::mold('company')->getAll());
        $this->setData('permissions',(new UserPermissions())->getPermissions());
        $this->setData('userTypes',Model::mold('userType')->getAll());
        $this->setData('statuses',$this->getStatuses('all'));
        $this->setData('now',CURRENT_TIMESTAMP);
        $this->addBreadcrumb('Users','User')->addBreadcrumb('My Profile','User/myProfile');
        $this->setHeading('My Profile');
        $this->setBody(View::mold('views/users/viewUser.phtml'));
    }

    public function saveUser() {
        $this->addBreadcrumb('User')->addBreadcrumb('My Profile','User/myProfile');
        $user = Model::mold('user')->find($this->input('id'));
        foreach($this->input() as $key => $value) {
            $user->{$key} = $value;
        }

        if (!empty($this->input('id'))) {
            if (!empty($user->modified)) {
                $propertiesModified = count(get_object_vars($user->modified[$this->input('id')]));
                $properties = 'Properties';
                if ($propertiesModified === 1) {
                    $properties = 'Property';
                }
                $user->save();
                $this->setNotify('success',count($user->modified).' User '.$properties.' Updated and Saved');
                $this->session('user',(object)(array)$user->current());
            } else {
                $this->setNotify('info','No User Properties Changed or Saved');
            }
        } elseif(empty($this->input('id'))) {
            $user->save();
            $this->setNotify('success','User Added');
            $this->back(3);
        }
    }

    public function saveUserPermissions() {
        if ($this->input('user_id') === null) {
            $this->setError('No User Specified to Assign the Permissions');
            return false;
        }

        $model = new Model('userPermission');
        $model->delete([
            'user_id' => $this->input('user_id')
        ]);

        $controllersAdded = 0;
        foreach($this->input('controller') as $key => $value) {
            Model::mold('userPermission')->insert([
                'user_id'    => $this->input('user_id'),
                'type'       => 'controller',
                'controller' => $key
            ]);
            $controllersAdded++;
        }

        $methodAdded = 0;
        foreach($this->input('method') as $key => $value) {
            Model::mold('userPermission')->insert([
                'user_id'    => $this->input('user_id'),
                'type'       => 'method',
                'controller' => $value,
                'method'     => $key
            ]);
            $methodAdded++;
        }
        $this->setNotify('success',$controllersAdded.' Modules and '.$methodAdded.' Functions Assigned');
    }

    public function logs($userId) {
        $user = Model::mold('user')->find($userId);
        $this->setData('user',$user);

        $history = Model::mold('modelHistory','byUserId',[
            'userId' => $userId,
            'start'  => 0
        ])->get();
        $this->setHeading($user->first_name.' '.$user->last_name.' History Log');
        $this->addBreadcrumb('User','User')
            ->addBreadcrumb('View Users','User/index')
            ->addBreadcrumb('User Logs','User/logs/'.$user->id);

        $this->setData('history',$history);
        $this->setBody(View::mold('views/users/logs.phtml'));
    }

    public function logDetail($userId,$logId) {
        $user = Model::mold('user')->find($userId);

        $this->addBreadcrumb('User','User')
            ->addBreadcrumb('View Users','User/index')
            ->addBreadcrumb('User Logs','User/logs/'.$user->id)
            ->addBreadcrumb('Log Detail','User/logDetail/'.$userId.'/'.$logId);

        $this->setData('detail',Model::mold('modelHistory')->find($logId));
        $this->setOffcanvas('Log History Item Detail',View::mold('views/users/logDetail.phtml'),'600px');
    }

    public function deleteUser($userId,$confirm = 'No') {
        $user = Model::mold('user')->find($userId);

        if ($confirm === 'No') {
            $this->setConfirm('Please Confirm','Are you sure you want to delete this "'.$user->first_name.' '.$user->last_name.'" from "'.$user->company_name.'""?','User/deleteUser/'.$userId.'/Yes');
            return false;
        }

        $user->status_id = 4;
        $user->save();
        $this->setNotify('success',$user->first_name.' '.$user->last_name.' from '.$user->company_name.' has been marked as deleted');
    }
}
