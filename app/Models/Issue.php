<?php

/*
 * This file is part of Fixhub.
 *
 * Copyright (C) 2016 Fixhub.org
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fixhub\Models;

use Auth;
use Fixhub\Models\Traits\BroadcastChanges;
use Fixhub\Presenters\IssuePresenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * Issue model.
 */
class Issue extends Model implements HasPresenter
{
    use SoftDeletes, BroadcastChanges;

    const COMPLETED    = 0;
    const APPROVED     = 1;
    const PENDING      = 2;
    const APPROVING    = 3;
    const FAILED       = 4;
    const REJECTED     = 5;
    const NOT_APPROVED = 6;

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['deleted_at', 'project'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'content', 'author_id', 'assignee_id', 'project_id'];

    /**
     * Override the boot method to assign author_id by current user.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (Issue $model) {
            $model->author_id = Auth::user()->id;
        });
    }

    /**
     * Belongs to relationship.
     *
     * @return User
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }

    /**
     * Belongs to relationship.
     *
     * @return Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get all of the issue's comments.
     *
     * @return MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return IssuePresenter::class;
    }
}
