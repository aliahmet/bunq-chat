<?php
/**
 * Created by PhpStorm.
 * User: aliahmet
 * Date: 10/2/17
 * Time: 2:29 PM
 */

namespace Model;


use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    public $timestamps = false;
    protected $hidden = ['pivot'];


}