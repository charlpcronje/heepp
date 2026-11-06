<?php
namespace core\system\handlers;
use core\Output;
use core\extension\helper\Zip;

class ExceptionHandler {
    public static function handleException($exception) {        
        $result  = [];
        $message = '';
        $i       = $exception->getMessage();
        if ($i == 'Denied') {
            $output = Output::getInstance();
            ProjectLoader::doOutput($output);
        } else {
            //$error = "Uncaught exception: ";
            $error = $exception->getMessage();
            $error .= ' thrown in '.$exception->getFile().' on line: '.$exception->getLine();
            //dd(env('ZIP_HTML',false));
            if (env('compress.output',null,0)) {
                header('Content-type:text/html; charset=utf-8');
                $result['error'][] = $error;
                $compress          = new Zip();
                echo $compress->compress(json_encode($result));
            } else {
                $output = Output::getInstance();
                if (!isset($output->ui)) {
                    $output->ui = new \stdClass();
                }
                $output->ui->notify = ['type' => 'error','message' => $error];
                header('Content-Type: application/json');
                ProjectLoader::doOutput($output);
            }
        }
        $dtime = 'DateTime: '.date('Y-m-d H:i:s')."\n";
        if (isset($error)) {
            $message = 'Message: '.$error."\n";
        }
        $code = 'Code:'.$exception->getCode()."\n\n\n";
        file_put_contents(env('core.path').'logs'.DS.'exceptions.log',$dtime.$message.$code,FILE_APPEND);
    }
}

set_exception_handler([ExceptionHandler::class,'handleException']);
