<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** v25: landing sahifasidagi demo so'rov. Filialga bog'liq EMAS; faqat sAdmin ko'radi va boshqaradi. */
class DemoRequest extends Model
{
    public const STATUSES = ['new' => 'Yangi', 'contacted' => "Bog'lanildi", 'done' => 'Yakunlandi', 'spam' => 'Spam'];

    public const SIZES = ['1-100' => '100 tagacha', '101-300' => '101–300', '301-1000' => '301–1000', '1000+' => '1000 dan ko\'p'];

    protected $fillable = ['name', 'phone', 'center', 'size', 'message', 'locale', 'status', 'telegram_sent', 'ip_hash', 'note'];

    protected function casts(): array
    {
        return ['telegram_sent' => 'boolean'];
    }
}
