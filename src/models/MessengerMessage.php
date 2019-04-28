<?php

namespace Increment\Messenger\Models;
use Illuminate\Database\Eloquent\Model;
use App\APIModel;
class MessengerMessage extends APIModel
{
    protected $table = 'messenger_messages';
    protected $fillable = ['messenger_group_id', 'account_id', 'message'];
}
