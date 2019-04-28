<?php

namespace Increment\Messenger\Models;
use Illuminate\Database\Eloquent\Model;
use App\APIModel;
class MessengerMember extends APIModel
{
    protected $table = 'messenger_members';
    protected $fillable = ['messenger_group_id', 'account_id', 'status'];
}
