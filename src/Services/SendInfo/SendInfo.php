<?php
namespace arabic0513\Eimzo\Services\SendInfo;

use arabic0513\Eimzo\Services\SendInfo\SendInfoInterface;

class SendInfo {
    public function __construct(SendInfoInterface $sendProvider)
    {
        $sendProvider->sendMessage();
    }


}
