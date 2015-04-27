<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_Auction extends ORM {
	protected $_table_name = 'auctions';
	protected $_has_many = array(
		'auctionPosts' => array(
			'model' => 'AuctionPost',
			'foreign_key' => 'auction_id',
		),
	);
	protected $_belongs_to = array(
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
		'UserPlane' => array(
			'model' => 'UserPlane',
			'foreign_key' => 'plane_id',
		),
	);

	public function rules() {
		return array(
			'minprice' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
			),
		);
	}

	public function getHighestBid() {
		try {
			$highest = NULL;
			foreach ($this->auctionPosts->find_all() as $auction) {
				if ($auction->price > $highest) {
					$highest = $auction;
				}
			}

			return $highest;
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

	public function getEndDate() {
		try {
			$date = new DateTime('@' . $this->end);
			return $date->format('H:i d.m.Y');
		} catch (Exception $e) {
			errToDb('[Exception][' . __CLASS__ . '][' . __FUNCTION__ . '][Line: ' . $e->getLine() . '][' . $e->getMessage() . ']');
		}
		return false;
	}

}