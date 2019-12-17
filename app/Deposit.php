<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Deposit
 *
 * @property int $id
 * @property int $subaddr_index
 * @property string $amount
 * @property string $time_received
 * @property string $tx_id
 * @property int $block_received
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Project $project
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereBlockReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereTimeReceived($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereTxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Deposit whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Deposit extends Model
{
    protected $fillable = [
        'tx_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
