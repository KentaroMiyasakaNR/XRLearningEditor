<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'player_record_id',
        'question_id',
        'option_id',
        'is_correct',
        'time_taken',
        'points_earned',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * プレイヤーレコードとのリレーションシップ
     */
    public function playerRecord(): BelongsTo
    {
        return $this->belongsTo(PlayerRecord::class);
    }

    /**
     * 質問とのリレーションシップ
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * 選択肢とのリレーションシップ
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(Option::class);
    }
}
