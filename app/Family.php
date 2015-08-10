<?php

namespace App;

use App\BaseModel;

class Family extends BaseModel
{
	/*
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'families';

    /**
     * The attributes that are mass assignable.
     *
     * @var array

    protected $fillable = ['name', 'surname', 'second_surname'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden =[];

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

}
