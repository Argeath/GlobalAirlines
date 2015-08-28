<?php defined('SYSPATH') or die('No direct script access.');

class Events_Event
{
    protected $event;
    protected $user;
    protected $parameters;

    private $info;

    private $checkStart;
    private $checkStop;

    private $returnedData;

    public $manager;

    public function __construct(Events_EventManager $manager, $event, $user) {
        $this->manager = $manager;
        $this->event = $event;
        if($user)
            $this->user = $user;

        $this->parameters = $event->parameters->find_all()->as_array('key', 'value');
    }

    protected function doWork() {
        return [];
    }

    public function execute() {
        $this->initStats();
        try {
            $this->doWork();
        } catch (Exception $e) {
            errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
        }

        $this->finishStats();
        $ret = $this->prepareReturn();
        Log::instance()->add(Log::INFO, json_encode($ret, JSON_PRETTY_PRINT));
        return $ret;

    }

    private function initStats() {
        if(Kohana::$environment == Kohana::DEVELOPMENT)
            $this->checkStart = microtime_float();
    }

    private function finishStats() {
        if(Kohana::$environment == Kohana::DEVELOPMENT)
            $this->checkStop = microtime_float();
    }

    private function prepareReturn() {
        $ret = [
            'id'        => $this->event->id,
            'type'      => $this->event->type,
            'user'      => $this->event->user->id,
            'time'      => microtime_float(),
        ];
        if( ! empty($this->info))
            $ret = array_merge($ret, $this->info);

        if(Kohana::$environment == Kohana::DEVELOPMENT) {
            $stats = [
                'executionTime' => $this->checkStop - $this->checkStart,
            ];
            $ret = array_merge($ret, $stats);
        }
        $this->returnedData = $ret;
        return $ret;
    }

    protected function addInfo($key, $value) {
        $this->info[$key] = $value;
    }

    public function toString() {
        if( ! $this->returnedData)
            return "";

        $str = "";
        foreach($this->returnedData as $key => $value) {
            $str .= "[".$key.": ".$value."]";
        }
        return $str;
    }

}