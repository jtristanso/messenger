<?php

namespace Increment\Messenger\Models;
use Illuminate\Database\Eloquent\Model;
use App\APIModel;
class MessengerMessageFile extends APIModel
{
    protected $table = 'messenger_message_files';
    protected $fillable = ['message_id', 'url', 'extension'];
}
