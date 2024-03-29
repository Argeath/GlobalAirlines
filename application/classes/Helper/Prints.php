<?php defined('SYSPATH') or die('No direct script access.');

class Helper_Prints {
	static function printErrors() {
		try {
			$messages = Session::instance()->get('errs');
			if (!$messages) {
				return false;
			}

			foreach ($messages as $k => $msg) {
				if ($msg[1] > time()) {
					echo "<div class='alert alert-danger'>";
					echo $msg[0];
					echo "</div>";
				} else {
					array_splice($messages, $k, 1);
				}
			}
			Session::instance()->set('errs', $messages);
			return true;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}
	static function printMsg() {
		try {
			$messages = Session::instance()->get('msgs');
			if (!$messages) {
				return false;
			}

			foreach ($messages as $k => $msg) {
				if ($msg[1] > time()) {
					echo "<div class='alert alert-success'>";
					echo $msg[0];
					echo "</div>";
				} else {
					array_splice($messages, $k, 1);
				}
			}
			Session::instance()->set('msgs', $messages);
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

			$t = $x;
			if ($l > 0) {
				$t = "<span style='color: green;'>+" . $x . "</span>";
			} elseif ($l < 0) {
				$t = "<span style='color: red;'>" . $x . "</span>";
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
			$currency = "";
			if ($wal) {
				$currency = " " . WAL;
			}

			if ($format) {
				$x = formatCash($x);
			}

			$t = $x;
			if ($l > 0 || ! is_numeric($x)) {
				$t = '<span class="text-rounded" style="background: rgb(15, 163, 15);">+' . $x . $currency . '</span>';
			} elseif ($l < 0) {
				$t = '<span class="text-rounded" style="background: red;">' . $x . $currency . '</span>';
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