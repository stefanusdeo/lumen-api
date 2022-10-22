<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryVa extends Model{

    protected $table="inquiry_va";
    protected $fillable =[
        'client_id',
        'reference_number',
        'virtual_account'
    ];

}
