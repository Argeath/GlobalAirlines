<?php defined('SYSPATH') or die('No direct script access.');

class Prints {
	static function printErrors() {
		try {
			$msgs = Session::instance()->get('errs');
			if (!$msgs) {
				return;
			}

			foreach ($msgs as $k => $msg) {
				if ($msg[1] > time()) {
					echo "<div class='alert alert-danger'>";
					echo $msg[0];
					echo "</div>";
				} else {
					array_splice($msgs, $k, 1);
				}
			}
			Session::instance()->set('errs', $msgs);
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
	static function printMsg() {
		try {
			$msgs = Session::instance()->get('msgs');
			if (!$msgs) {
				return;
			}

			foreach ($msgs as $k => $msg) {
				if ($msg[1] > time()) {
					echo "<div class='alert alert-success'>";
					echo $msg[0];
					echo "</div>";
				} else {
					array_splice($msgs, $k, 1);
				}
			}
			Session::instance()->set('msgs', $msgs);
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	static function colorNumber($x, $format = true) {
		try {
			$l = $x;
			if ($format) {
				$x = formatCash($x);
			}

			$t = "";
			if ($l > 0) {
				$t = "<span style='color: green;'>+" . $x . "</span>";
			} elseif ($l < 0) {
				$t = "<span style='color: red;'>" . $x . "</span>";
			} else {
				$t = $x;
			}

			return $t;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	static function colorBgNumber($x, $format = true, $wal = true) {
		try {
			$l = $x;
			$waluta = "";
			if ($wal) {
				$waluta = " " . WAL;
			}

			if ($format) {
				$x = formatCash($x);
			}

			$t = "";
			if ($l > 0 || ! is_numeric($x)) {
				$t = '<span class="text-rounded" style="background: rgb(15, 163, 15);">+' . $x . $waluta . '</span>';
			} elseif ($l < 0) {
				$t = '<span class="text-rounded" style="background: red;">' . $x . $waluta . '</span>';
			} else {
				$t = $x;
			}

			return $t;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	static function colorBgText($x, $color, $addit = "") {
		try {
			$t = '<span class="text-rounded" style="background: ' . $color . ';" ' . $addit . '>' . $x . '</span>';
			return $t;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	static function rusureButton($text, $name, $value = "", $classes = []) {
		try {
			$t = '<div class="button-group rusureButtonGroup">
					  <span class="button-group-addon" style="padding: 6px 5px 7px 10px;">
						<input type="checkbox" id="cancelCheckbox">
					  </span>
					  <button id="rusureButton" name="' . $name . '" value="' . $value . '" disabled="disabled" class="btn btn-small ' . implode(' ', $classes) . '">' . $text . '</button>
				</div>';
			return $t;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

};