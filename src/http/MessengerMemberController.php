<?php

namespace Increment\Messenger\Http;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Increment\Messenger\Models\MessengerMember;

class MessengerMemberController extends APIController
{
    function __construct(){
      $this->model = new MessengerMember();
    }
}
