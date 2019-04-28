<?php

namespace Increment\Messenger\Http;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Increment\Messenger\Models\MessengerMessage;
use Carbon\Carbon;
use App\Events\Message;

class MessengerMessageController extends APIController
{
    function __construct(){
      $this->model = new MessengerMessage;
    }

    public function create(Request $request){
      $data = $request->all();
      $this->insertDB($data);
      $data['account'] = $this->retrieveAccountDetails($data['account_id']);
      $data['created_at_human'] =  Carbon::now()->copy()->tz('Asia/Manila')->format('F j, Y');
      broadcast(new Message($data))->toOthers();
      return $this->response();
    }
    public function retrieve(Request $request){
      $data = $request->all();
      $this->model = new MessengerMessage();
      $this->retrieveDB($data);
      $result = $this->response['data'];
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $this->response['data'][$i]['account'] = $this->retrieveAccountDetails($result[$i]['account_id']);
          $this->response['data'][$i]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $result[$i]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y');
          $i++;
        }
      }
      return $this->response();
    }

    public function getLastMessageSupport($messengerGroupId){
      $message = MessengerMessage::where('messenger_group_id', '=', $messengerGroupId)->orderBy('created_at', 'desc')->get();
      if(sizeof($message) > 0){
        $message[0]['account'] = $this->retrieveAccountDetails($message[0]['account_id']);
        $message[0]['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $message[0]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y');
        return $message[0];
      }
      return null;
    }

    public function getLastMessage($messengerGroupId){
      $message = MessengerMessage::where('messenger_group_id', '=', $messengerGroupId)->orderBy('created_at', 'desc')->get();
      $response = array(
        'title', 'description', 'date'
      );
      if(sizeof($message) > 0){
        $response['title'] = $this->retrieveAccountDetails($message[0]['account_id']);
        $response['description'] = $message[0]['message'];
        $response['created_at_human'] = Carbon::createFromFormat('Y-m-d H:i:s', $message[0]['created_at'])->copy()->tz('Asia/Manila')->format('F j, Y');
        return $response;
      }
      return null;
    }
}
