<?php defined('SYSPATH') or die('No direct script access.');

class Events_EventManager
{
    private $params;
    private $logs;

    private $commitImmediately;

    public $needOneMoreCycle = false;

    public function __construct($commitImmediately = false) {
        $this->commitImmediately = $commitImmediately;
    }

    public function addParam($eventId, $key, $value) {
        if(is_numeric($value))
            $value = round($value);
        $this->params[] = "(" . $eventId . ", '".$key."', " . $value . ")";
    }

    public function addLog($text) {
        $this->logs[] = $text;
    }

    public function commitParams() {
        if ( ! empty($this->params)) {
            $paramsStr = implode(', ', $this->params);
            DB::query(Database::INSERT, "INSERT INTO `event_parameters` (`event_id`, `key`, `value`) VALUES $paramsStr")->execute();
        }
        $this->params = [];
        return true;
    }

    public function commitLogs() {
        if ( ! empty($this->logs)) {
            $logs = [];
            foreach($this->logs as $log) {
                $logs[] = '('.time().', \''.$log.'\')';
                Log::instance()->add(Log::WARNING, $log);
            }
            $logsStr = implode(', ', $logs);
            DB::query(Database::INSERT, "INSERT INTO `errors` (`time`, `text`) VALUES $logsStr")->execute();
        }
        $this->logs = [];
        return true;
    }
}