<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandTicket extends Model
{
    protected $table = 'brand_ticket';

    protected $primaryKey = 'brand_ticket_id';

    public $timestamps = false;

    public function scopeGetBrandTicket($query, $_ticketID)
    {

    	return $query->join('ticket as tix', 'brand_ticket.ticket_id', '=', 'tix.ticket_id')
    				->join('Procurement.dbo.lib_brand as lb', 'lb.brand_id', '=', 'brand_ticket.brand_id')
    				->select('*')
    	       		->where('brand_ticket.ticket_id', $_ticketID)
    	       		->orderBy('lb.brand_id', 'asc')
    	       		->get();
    }
}
