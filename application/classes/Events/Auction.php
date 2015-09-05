<?php defined('SYSPATH') or die('No direct script access.');

class Events_Auction extends Events_Event {
    protected function doWork() {
        $auctionId = $this->parameters['auction'];

        $auction = ORM::factory('Auction', $auctionId);
        if ($auction->loaded()) {

            if ($auction->canceled == 0) {

                $highest = $auction->getHighestBid();
                if ($highest) { //Samolot sprzedany

                    $auction->UserPlane->user_id = $highest->user_id;
                    $auction->UserPlane->save();
                    $msg = 'Wygrano licytacje za ' . $highest->price . '. Samolot od teraz należy do Ciebie.';
                    $highest->user->sendMiniMessage('Wygrano licytacje.', $msg, $this->event->when);

                } else {
                    $msg = 'Nie udało ci się sprzedał samolotu na aukcjach.';
                   $this->event->user->sendMiniMessage('Nie udało się sprzedać samolotu.',
                       $msg, $this->event->when);
                }
            }
        } else {
            $this->addInfo('EventLoop', 1);
        }
    }
}