<?php

namespace App\Observers\ActionLog;

use Illuminate\Database\Eloquent\Model;

class ActionLogObserver
{
    protected function isAdmin()
    {
        return auth()->user() ? auth()->user()->is_admin == true  : false;
    }
    public function created(Model $model)
    {
        if ($this->isAdmin()) {
            $model->loggs()->create(['description' => 'admin created new ' . class_basename($model), 'user_id' => auth()->id()]);
        }
    }
    public function updated(Model $model)
    {
        if ($this->isAdmin()) {
            $model->loggs()->create(['description' => 'admin updated ' . class_basename($model), 'user_id' => auth()->id()]);
        }
    }
    public function deleted(Model $model)
    {
        if ($this->isAdmin()) {
            $model->loggs()->create(['description' => 'admin deleted ' . class_basename($model), 'user_id' => auth()->id()]);
        }
    }
}
