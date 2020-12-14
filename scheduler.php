<?php
use \Modules\Everdio\Library\EProcessing;

$time = time();

$scheduler = new EProcessing\Scheduler;
$scheduler->reset($scheduler->mapping);
$scheduler->Status = "queued";

foreach ($scheduler->findAll() as $row) {    
    $scheduler = new $scheduler($row);
    
    $execute = new \Modules\Everdio\Library\EProcessing\Execute($row);
    $execute->find();
    
    $route = new \Modules\Everdio\Library\EProcessing\Route($row);
    $route->find();
    
    if ((isset($route->Route) && isset($execute->Execute)) && ((in_array(date("i", $time), explode(",", $scheduler->Minute)) || (empty($scheduler->Minute) && $scheduler->Minute !== 0)) && (in_array(date("H", $time), explode(",", $scheduler->Hour)) || (empty($scheduler->Hour) && $scheduler->Hour !== 0)) && (in_array(date("d", $time), explode(",", $scheduler->Day)) || empty($scheduler->Day)) && (in_array(date("N", $time), explode(",", $scheduler->Weekday)) || (empty($scheduler->Weekday) && $scheduler->Weekday !==0)) && (in_array(date("m", $time), explode(",", $scheduler->Month)) || (empty($scheduler->Month) && $scheduler->Month !== 0)))) {
        $cmd = new Components\Core\Controller\Model\Cmd(new Components\Parser\Ini);

        if (!empty($scheduler->Request)) {
            $cmd->request->import($scheduler->Request);
        }
        if (!empty($scheduler->Arguments)) {
            $cmd->arguments = explode(DIRECTORY_SEPARATOR, $scheduler->Arguments);
        }
        
        $cmd->request->store($scheduler->restore($scheduler->primary) + $execute->restore(["Execute"]));
        echo sprintf("%s (%s)", $route->Route, $this->dehydrate($cmd->request->restore())) . PHP_EOL;
        ob_flush();
        $cmd->display($route->Route);            
    }
}