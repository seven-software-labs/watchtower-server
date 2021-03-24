<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'ticket_id' => $this->ticket_id,
            'message_type_id' => $this->message_type_id,
            'sender' => $this->sender ? collect([
                'id' => $this->sender->getKey(),
                'name' => $this->sender->name,
                'is_customer' => $this->sender->is_customer,
            ]):null,
            'recipient' => $this->recipient ? collect([
                'id' => $this->recipient->getKey(),
                'name' => $this->recipient->name,
                'is_customer' => $this->recipient->is_customer,
            ]):null,
            'source_id' => $this->source_id,
            'source_created_at' => $this->source_created_at,
            'is_sent' => $this->is_sent,
            'is_delivered' => $this->is_delivered,
        ];
    }
}
