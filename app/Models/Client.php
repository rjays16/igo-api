<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $fillable=['first_name','last_name','gender','date_of_birth','email','phone','organization_id','address1','address2','city_id','state','zip','client_type_id','ca_date','tag','note'];

    /*
    public function organization(){
        return $this->belongsTo(Organization::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function client_type(){
        return $this->belongsTo(ClientType::class);
    }
    */

}
