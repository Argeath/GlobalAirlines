<?php defined('SYSPATH') OR die('No direct access allowed.');
class Model_AuctionPost extends ORM {
	protected $_table_name = 'auctions_posts';
	protected $_belongs_to = array(
		'auction' => array(
			'model' => 'Auction',
		),
		'user' => array(
			'model' => 'User',
			'foreign_key' => 'user_id',
		),
	);

	public function rules() {
		return array(
			'price' => array(
				// Uses Valid::not_empty($value);
				array('not_empty'),
			),
		);
	}

}