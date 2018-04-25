<?php
namespace core\element\centred\tz;
use core\Element;
use core\extension\api\client\RestClient;
use core\extension\ui\view;

class messages extends Element {
    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }
    
    public function render() {
        $messageObj = new \Message();
        $resMessages = $messageObj->getMessages();
        $messages = [];
        if (!empty($resMessages)) {
            foreach($resMessages as $message) {
                if ($message->is_read != 1) {
                    $messages[] = $message;
                }
            }
            if (count($resMessages) > 0) {
                $this->setData('messages',$resMessages);
            } else {
                $this->setData('messages',[0 => (object)[
                    'title'      => 'Inbox Empty',
                    'created_at' => date('Y-m-d H:i:s'),
                    'message'    => 'You currently have no unread messages',
                    'is_read'    => 0,
                    'hideButton' => true
                ]]);
            }
        }
        $this->setData('spacerHeight',306 - (count(@$messages)+1) * 110);
        return (new view('messages.phtml',__DIR__))->html;
    }
}
