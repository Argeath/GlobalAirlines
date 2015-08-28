<?php defined('SYSPATH') or die('No direct script access.');

class Maintenance {
    public static function check() {
        $maintenance = false;
        try {
            $q = DB::select()->from('settings')->where('key', '=', 'maintenance')->execute()->as_array();
            if (!empty($q)) {
                $q = $q[0];
                if ($q['value'] == 1) {
                    $maintenance = true;
                }
            }
        } catch (Exception $e) {
            $maintenance = true;
        }

        return $maintenance;
    }
}