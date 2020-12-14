<?php
use \Modules\Everdio\Library\EProcessing;

if (isset($this->request->SchedulerId)) {
    $scheduler = new EProcessing\Scheduler;
    $scheduler->store($this->request->restore());
    $scheduler->find();
    
    if (isset($scheduler->SchedulerId)) {
        $execute = new \Modules\Everdio\Library\EProcessing\Execute;
        $execute->ExecuteId = $scheduler->ExecuteId;
        $execute->find();
        if (isset($execute->Execute)) {
            
            $scheduler->Status = "active";
            $scheduler->save();

            $task = new EProcessing\Task;
            $task->SchedulerId = $scheduler->SchedulerId;        

            try { 
                $this->execute(sprintf("/../../%s", $execute->Execute));
                $task->Output = $this->sanitize(nl2br(ob_get_contents() . PHP_EOL . sprintf("%ss",  round(microtime(true) - $this->time, 3))));
                $scheduler->Status = "queued";
            } catch (\Exception $exception) {
                $task->Output = $this->sanitize(nl2br($exception->getMessage() . PHP_EOL . $exception->getTraceAsString() . PHP_EOL . sprintf("%ss",  round(microtime(true) - $this->time, 3))));
                $scheduler->Status = "error";
            }

            $scheduler->save();
            $task->save();
        } else {
            throw new \LogicException(sprintf("unknown task to execute %s", $this->dehydrate($scheduler->restore())));
        }
    } else {
        throw new \LogicException(sprintf("unknown scheduled task (%s)", $this->dehydrate($this->restore(["path"]))));
    }
} else {
    throw new \LogicException(sprintf("not a scheduled task (%s)", $this->dehydrate($this->restore(["path"]) + $this->request->restore())));
}