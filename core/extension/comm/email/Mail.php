<?php
namespace core\extension\comm\email;
use core\Heepp;
use core\extension\comm\email\PHPMailer\PHPMailer;
use core\extension\Extension;
use core\extension\ui\view;
use core\system\log;

class Mail extends Extension {
    public static $from;
    public static $recipient;
    public static $subject;
    public static $view;

    public function __construct() {
        parent::__construct(__CLASS__);
    }

    /**
     * @param array  $recipient []
     * @param null   $subject
     * @param        $view
     * @param array  $from
     *
     * @return object
     */
    public static function send($recipient = [],$subject = null,$view = null,$from = null) {
        if (!isset($from)) {
            $from = (object)self::setFromDefaut();
        }
        self::$from      = $from;
        self::$recipient = (object)$recipient;

        if (Heepp::data('app.email.driver') == 'smtp') {
            return self::smtp($subject,$view);
        }

        if (Heepp::data('app.email.driver') == 'log') {
            log::info([
               'from'      => self::$from,
               'recipient' => self::$recipient,
               'subject'   => $subject,
               'html'      => view::phtml($view)
            ],'Mail');
        }
    }

    private static function smtp($subject,$view) {
        $mail = new PHPMailer;
        $mail->SetFrom(self::$from->address,self::$from->alias);
        $mail->AddAddress(self::$recipient->address,self::$recipient->alias);
        $mail->Subject  = $subject;
        $mail->Body     = view::phtml($view);
        // Send Failed
        if(!$mail->Send()) {
            return (object)[
                'success' => false,
                'error'   => $mail->ErrorInfo
            ];
        }
        // Send Success
        return (object)[
            'success' => true
        ];
    }

    public static function setFromDefaut() {
        return (object)[
            'address' => Heepp::data('app.email.from.address'),
            'alias'   => Heepp::data('app.email.from.alias')
        ];
    }
}
