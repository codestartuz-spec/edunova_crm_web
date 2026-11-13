<?php

namespace App\Support;

/**
 * Kurs materiallari havolasini sahifaning O'ZIDA ijro etiladigan ko'rinishga aylantiradi (boshqa saytni ochmasdan).
 * Faqat ishonchli xizmatlar (YouTube, Vimeo, Google Drive) iframe orqali, to'g'ridan-to'g'ri fayllar esa
 * brauzerning o'z pleeri (<video>/<audio>) orqali ijro etiladi. Boshqa havolalar 'link' bo'lib qoladi.
 *
 * @return array{kind: 'iframe'|'video'|'audio'|'link'|'none', src: string, label: string}
 */
class MediaEmbed
{
    private const VIDEO_EXT = ['mp4', 'webm', 'ogv', 'mov', 'm4v'];

    private const AUDIO_EXT = ['mp3', 'wav', 'm4a', 'ogg', 'oga', 'aac', 'flac'];

    public static function resolve(?string $url): array
    {
        $url = trim((string) $url);
        $p = parse_url($url);
        $scheme = strtolower($p['scheme'] ?? '');
        $host = strtolower($p['host'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return ['kind' => 'none', 'src' => '', 'label' => "Noto'g'ri havola"];
        }

        $host = (string) preg_replace('/^(www|m)\./', '', $host);
        $path = $p['path'] ?? '';
        parse_str($p['query'] ?? '', $q);

        // YouTube
        if (in_array($host, ['youtube.com', 'youtube-nocookie.com', 'music.youtube.com', 'youtu.be'], true)) {
            $id = null;
            if ($host === 'youtu.be') {
                $id = explode('/', trim($path, '/'))[0] ?: null;
            } elseif (preg_match('#^/(?:embed|shorts|live|v)/([^/?]+)#', $path, $m)) {
                $id = $m[1];
            } elseif (isset($q['v']) && is_string($q['v'])) {
                $id = $q['v'];
            }

            if ($id && preg_match('/^[A-Za-z0-9_-]{6,20}$/', $id)) {
                $src = 'https://www.youtube-nocookie.com/embed/'.$id.'?rel=0&modestbranding=1&playsinline=1';
                $start = self::seconds($q['t'] ?? ($q['start'] ?? null));
                if ($start > 0) {
                    $src .= '&start='.$start;
                }

                return ['kind' => 'iframe', 'src' => $src, 'label' => 'YouTube'];
            }
        }

        // Vimeo
        if (in_array($host, ['vimeo.com', 'player.vimeo.com'], true) && preg_match('#/(?:video/)?(\d{5,})(?:/([a-f0-9]{6,}))?#i', $path, $m)) {
            $src = 'https://player.vimeo.com/video/'.$m[1].'?dnt=1';
            $hash = $m[2] ?? ($q['h'] ?? null);
            if (is_string($hash) && preg_match('/^[a-f0-9]{6,}$/i', $hash)) {
                $src .= '&h='.$hash;
            }

            return ['kind' => 'iframe', 'src' => $src, 'label' => 'Vimeo'];
        }

        // Google Drive (video yoki audio): preview pleeri
        if ($host === 'drive.google.com') {
            $id = null;
            if (preg_match('#/file/d/([A-Za-z0-9_-]+)#', $path, $m)) {
                $id = $m[1];
            } elseif (isset($q['id']) && is_string($q['id']) && preg_match('/^[A-Za-z0-9_-]+$/', $q['id'])) {
                $id = $q['id'];
            }

            if ($id) {
                return ['kind' => 'iframe', 'src' => 'https://drive.google.com/file/d/'.$id.'/preview', 'label' => 'Google Drive'];
            }
        }

        // Dropbox: ulashish havolasini to'g'ridan-to'g'ri faylga aylantiramiz
        if ($host === 'dropbox.com') {
            $query = ['raw' => '1'];
            if (isset($q['rlkey']) && is_string($q['rlkey'])) {
                $query['rlkey'] = $q['rlkey'];
            }
            $url = 'https://www.dropbox.com'.$path.'?'.http_build_query($query);
        }

        // To'g'ridan-to'g'ri media fayl
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($ext, self::VIDEO_EXT, true)) {
            return ['kind' => 'video', 'src' => $url, 'label' => 'Video fayl'];
        }
        if (in_array($ext, self::AUDIO_EXT, true)) {
            return ['kind' => 'audio', 'src' => $url, 'label' => 'Audio fayl'];
        }

        return ['kind' => 'link', 'src' => $url, 'label' => 'Boshqa sayt'];
    }

    /** Sahifa ichida ijro etib bo'ladimi. */
    public static function playable(array $media): bool
    {
        return in_array($media['kind'], ['iframe', 'video', 'audio'], true);
    }

    private static function seconds(mixed $t): int
    {
        if (! is_string($t) || $t === '') {
            return 0;
        }
        if (preg_match('/^(\d+)s?$/', $t, $m)) {
            return (int) $m[1];
        }
        if (preg_match('/^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/', $t, $m)) {
            return (int) ($m[1] ?? 0) * 3600 + (int) ($m[2] ?? 0) * 60 + (int) ($m[3] ?? 0);
        }

        return 0;
    }
}
