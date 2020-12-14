<?php
use \Modules\Table;
use \Modules\Everdio\Library\EProcessing;

$time = time();

$scheduler = new EProcessing\Scheduler;
$scheduler->reset($scheduler->mapping);
$scheduler->Status = "queued";

$execute = new EProcessing\Execute;

foreach ($scheduler->findAll([new Table\Select([$scheduler, $execute]), new Table\Relation($scheduler, [$execute])]) as $row) {

    $scheduler = new $scheduler($row);
    $execute = new $execute($row);
    
    if ((isset($this->request->now)) || isset($execute->Route) && ((in_array(date("i", $time), explode(",", $scheduler->Minute)) || empty($scheduler->Minute)) && (in_array(date("H", $time), explode(",", $scheduler->Hour)) || empty($scheduler->Hour)) && (in_array(date("d", $time), explode(",", $scheduler->Day)) || empty($scheduler->Day)) && (in_array(date("N", $time), explode(",", $scheduler->Weekday)) || empty($scheduler->Weekday)) && (in_array(date("m", $time), explode(",", $scheduler->Month)) || empty($scheduler->Month)))) {

        $cmd = new Components\Core\Controller\Model\Cmd(new Components\Parser\Ini);
        $cmd->path = $this->path;
        
        //$cmd->request = [(string) $scheduler => $scheduler->restore($scheduler->keys)];
        
        if (isset($scheduler->Arguments)) {
            $cmd->arguments = explode(DIRECTORY_SEPARATOR, $scheduler->Arguments);
        }
        
        if (isset($scheduler->Request)) {
            parse_str($scheduler->Request, $request);
            //$cmd->request = $request;
        }
        
        try {
            print_r($cmd);
            echo $cmd->execute($scheduler->Path . DIRECTORY_SEPARATOR . $execute->Route);    
        } catch (\Exeception $ex) {
            echo $ex->getMessage() . PHP_EOL;
        }       
    }
}