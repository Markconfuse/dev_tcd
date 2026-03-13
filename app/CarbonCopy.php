<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarbonCopy extends Model
{
    protected $table = 'carbon_copy';

    protected $primaryKey = 'cc_id';

    public $timestamps = false;

    public function scopeGetCC($query, $ticketID)
    {
        return $query->join('vw_crm_accounts as acc', 'acc.AccountID', '=', 'carbon_copy.account_id')
                    ->selectRaw('AccountID, AccountName, AccountGroup, AccountType, DomainAccount, Email, ValidTo, NickName, isActive, GAvatar, 
                        ticket_id, cc_id, account_id, date_added')
                    ->where('carbon_copy.ticket_id', $ticketID)
                    ->orderBy('cc_id', 'asc')
                    ->get();
    }
    
    public function scopeGetCCEmail($query, $_ticketID)
    {
    	return \Common::instance()->col2str(Self::getCC($_ticketID)->toJson(), 'Email');
    }

    public function scopeCCID($query, $_ticketID)
    {
        return Self::getCC($_ticketID)->pluck('account_id')->toArray();
    }
}
