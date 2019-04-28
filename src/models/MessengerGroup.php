<?php

namespace Increment\Messenger\Models;
use Illuminate\Database\Eloquent\Model;
use App\APIModel;
class MessengerGroup extends APIModel
{
    protected $table = 'messenger_groups';
    protected $fillable = ['account_id', 'payload', 'title', 'payload'];
}
