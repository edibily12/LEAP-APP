<?php

namespace App\Traits;

trait WithFilter
{
    public $search = '';
    public $perPage = 20;
    public $orderBy = 'created_at';
    public $orderAsc = false;
}
