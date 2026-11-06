<?php
use core\Controller as CoreController;
use core\Heepp;
use core\extension\database\Model;
use core\extension\ui\view;

class Controller extends CoreController {

    public function __construct($controller = __CLASS__) {
        parent::__construct($controller);
    }

    protected function addHistory($element,$request) {
        $element = str_replace('.','_',$element);
        $this->session('history.'.$element,$request);
    }

    protected function getValueStore($store = null) {
        $storeData = Model::keyStore('byStoreAndUser',[
            'store' => $store,
            'userId' => $this->session('user.id')
        ]);
        foreach($storeData as $key => $data) {
            $this->setData($key,$data);
        }
        return $storeData;
    }

    protected function saveValueStore($store,$keyValues = []) {
        $actionTaken = (object)[
            'updated' => 0,
            'added'   => 0
        ];
        $storeData = Model::mold('keyStore','byStoreAndUser',[
            'store' => $store,
            'userId' => 0
        ]);
        $userStoreData = Model::mold('keyStore','byStoreAndUser',[
            'store' => $store,
            'userId' => $this->session('user.id')
        ]);

        foreach($storeData as $key => $data) {
            if (!isset($userStoreData[$key])) {
                $userKeyData = new Model('keyDataStore');
                $userKeyData->insert([
                    'user_id'       =>  $this->session('user.id'),
                    'key_store'     =>  $data->key_store,
                    'key_index'     =>  $data->key_index,
                    'key_value'     =>  $this->input($data->key_index,$data->key_value),
                    'key_label'     =>  $data->key_label,
                    'key_detail'    =>  $data->key_detail,
                    'key_order'     =>  $data->key_order,
                    'key_action'    =>  $data->key_action,
                    'key_authority' =>  $data->key_authority,
                    'key_state'     =>  $data->key_state,
                    'created_at'    =>  CURRENT_TIMESTAMP,
                    'updated_at'    =>  CURRENT_TIMESTAMP,
                    'actioned_by'   =>  $this->session('user.first_name').' '.$this->session('user.last_name')
                ]);
                $actionTaken->added++;
            } else {
                if ($userStoreData[$key]->key_value != $this->input($data->key_index)) {
                    $userData = Model::mold('keyDataStore')->find($userStoreData[$key]->id);
                    $userData->key_value = input($data->key_index,$userData->key_value);
                    $userData->updated_at = CURRENT_TIMESTAMP;
                    $userData->save();
                    $actionTaken->updated++;
                }
            }
        }
        return $actionTaken;
    }

    protected function getValueFromStore($store = null,$key = null) {
        return Model::dataStore((object)['byStoreKeyAndUser' => [
            'userId' => $this->session('user.id'),
            'store'  => $store,
            'key'    => $key
        ]])->current();
    }

    protected function saveStoreValue($store,$key,$value,$keyStatus = 'On') {
        $dataStore = Model::dataStore((object)['byStoreKeyAndUser' => [
            'userId' => $this->session('user.id'),
            'store'  => $store,
            'key'    => $key
        ]]);

        if ($dataStore->getResults()) {
            $dataStore->data_key = $key;
            $dataStore->key_status = $keyStatus;
            $dataStore->updated_at = CURRENT_TIMESTAMP;
            $dataStore->data_value = $value;
            $dataStore->save();
        } else {
            $dataStore->insert([
                'user_id' => $this->session('user.id'),
                'data_store' => $store,
                'data_key' => $key,
                'data_value' => $value,
                'key_status' => 'On',
                'created_at' => CURRENT_TIMESTAMP,
                'updated_at' => CURRENT_TIMESTAMP
            ]);
        }
    }
}
