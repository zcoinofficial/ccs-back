<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * App\ProjectResource
 *
 * @property int $id
 * @property int $subaddr_index
 * @property string $target_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Deposit[] $deposits
 * @property-read mixed $raised_amount
 * @property-read string $address_uri
 * @property-read int $percentage_funded
 * @property-read int $contributions
 * @property-read string $qrcode
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereTargetAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $title
 * @property string|null $commit_sha
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereCommitSha($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereMergeRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Project whereTitle($value)
 */
class Project extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['created_at', 'updated_at'];
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'subaddr_index', 'subaddr_index');
    }

    public function getPercentageFundedAttribute() {
        return min(100, round($this->raised_amount / $this->target_amount * 100));
    }

    public function getContributionsAttribute() {
        return $this->deposits->count() ?? 0;
    }

    public function generateQrcode() {
        return QrCode::format('png')->size(500)->generate($this->address_uri);
    }

    public function getQrCodeSrcAttribute() {
        $encoded = base64_encode($this->generateQrcode());
        return "data:image/png;base64, {$encoded}";
    }
}
