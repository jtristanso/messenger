<?php

namespace Increment\Messenger\Http;

use Illuminate\Http\Request;
use App\Http\Controllers\APIController;
use Increment\Messenger\Models\MessengerGroup;
use Increment\Messenger\Models\MessengerMember;
use Increment\Messenger\Models\MessengerMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Events\Message;
class MessengerGroupController extends APIController
{
    function __construct(){
      $this->model = new MessengerGroup();
    }

    public function retrieve(Request $request){
      $data = $request->all();

      $this->retrieveDB($data);

      if(sizeof($this->response['data']) > 0){
        $i = 0;

        foreach ($variable as $key => $value) {
          $this->response['data'][$i]['account'] = $this->retrieveAccountDetails($this->response['data'][$i]['account_id']);
          $i++;
        }
      }

      return $this->response();
    }
    public function retrieveSummary(Request $request){
      $data = $request->all();
      $accountType = $data['account_type'];
      $accountId = $data['account_id'];
      $response = array();

      $result = DB::table('messenger_members as T1')
        ->join('messenger_groups as T2', 'T2.id', '=', 'T1.messenger_group_id')
        ->where('T1.account_id', '=', $accountId)
        ->where('T2.payload', '!=', 'support')
        ->select('T2.*')
        ->get();
      $result = json_decode($result, true);
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $response[] = app('Increment\Messenger\Http\MessengerMessageController')->getLastMessage($result[$i]['id']);
          $i++;
        }
      }else{
        $response = null;
      }

      return response()->json(array(
        'data'  => $response,
        'error' => null,
        'timestamps'  => Carbon::now()
      ));
    }

    public function createNewIssue(Request $request){
      $data = $request->all();

      $creator = intval($data['creator']);
      $message = $data['message'];
      $this->model = new MessengerGroup();
      $insertData = array(
        'account_id'  => $creator,
        'title' => 'NONE',
        'payload' => 'support'
      );
      $this->insertDB($insertData);
      $id = intval($this->response['data']);
      if($id > 0){
        $member = new MessengerMember();
        $member->messenger_group_id = $id;
        $member->account_id = $creator;
        $member->status = 'admin';
        $member->created_at = Carbon::now();
        $member->save();

        $messageModel = new MessengerMessage();
        $messageModel->messenger_group_id = $id;
        $messageModel->account_id = $creator;
        $messageModel->message = $message;
        $messageModel->created_at = Carbon::now();
        $messageModel->save();

        $messageArray = array(
          'messenger_group_id'  => $id,
          'account_id'          => $creator,
          'message'             => $message,
          'status'              => 'support',
          'account'             => $this->retrieveAccountDetails($creator),
          'created_at_human'    =>  Carbon::now()->copy()->tz('Asia/Manila')->format('F j, Y')
        );

        broadcast(new Message($messageArray))->toOthers();
        return response()->json(array(
          'data'  => $id,
          'error' => null,
          'timestamps'  => Carbon::now()
        ));
      }else{
        return response()->json(array(
          'data'  => null,
          'error' => null,
          'timestamps'  => Carbon::now()
        ));
      }
    }

    public function retrieveMyIssue(Request $request){
      $data = $request->all();
      $this->model = new MessengerGroup();
      $this->retrieveDB($data);

      $result = $this->response['data'];
      if(sizeof($result) > 0){
        $i = 0;
        foreach ($result as $key) {
          $this->response['data'][$i]['last_message'] = app('Increment\Messenger\Http\MessengerMessageController')->getLastMessageSupport($result[$i]['id']);
          $i++;
        }
      }
      return $this->response();
    }

}
