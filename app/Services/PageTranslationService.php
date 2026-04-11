<?php

namespace App\Services;

class PageTranslationService
{
    public const SUPPORTED_LOCALES = ['uz', 'uzc', 'ru', 'en'];

    public function translate(string $slug, string $title, string $content, string $targetLocale): array
    {
        $title = $this->normalizeSourceText($title);
        $content = $this->normalizeSourceText($content);

        return match ($targetLocale) {
            'uz' => ['title' => $title, 'content' => $content],
            'uzc' => [
                'title' => $this->latinUzbekToCyrillic($title),
                'content' => $this->latinUzbekToCyrillic($content),
            ],
            'ru' => $this->translateStructured($slug, $title, $content, 'ru'),
            'en' => $this->translateStructured($slug, $title, $content, 'en'),
            default => ['title' => $title, 'content' => $content],
        };
    }

    private function translateStructured(string $slug, string $title, string $content, string $locale): array
    {
        $documentTemplates = [
            'terms-of-use' => [
                'ru' => [
                    'title' => 'Условия использования',
                    'content' => implode("\n\n", [
                        'Используя эту систему, вы подтверждаете, что будете использовать платформу только в законных и добросовестных целях.',
                        'Защита данных учетной записи остается обязанностью пользователя. При обнаружении несанкционированного использования доступ может быть временно ограничен.',
                        'Функции сервиса могут обновляться со временем. Важные изменения публикуются как новая версия документа.',
                    ]),
                ],
                'en' => [
                    'title' => 'Terms of Use',
                    'content' => implode("\n\n", [
                        'By using this system, you agree to use the platform only for lawful and legitimate purposes.',
                        'Protecting account data remains the responsibility of the user. If unauthorized use is detected, access may be temporarily restricted.',
                        'Service features may change over time. Important changes are published as a new version of the document.',
                    ]),
                ],
            ],
            'privacy-policy' => [
                'ru' => [
                    'title' => 'Политика конфиденциальности',
                    'content' => implode("\n\n", [
                        'Информация, отправленная при регистрации, включая имя, номер телефона, имя пользователя и название объекта, используется для организации процесса подключения к системе.',
                        'Данные обрабатываются только для оказания сервиса, контроля безопасности и связи. Они не передаются неуполномоченным третьим лицам, кроме случаев, предусмотренных законом.',
                        'При обновлении политики публикуется новая версия, а дата последнего обновления отображается на странице.',
                    ]),
                ],
                'en' => [
                    'title' => 'Privacy Policy',
                    'content' => implode("\n\n", [
                        'Information submitted during registration, including name, phone number, username, and venue name, is used to organize the system connection process.',
                        'Data is processed only for service delivery, security monitoring, and communication. It is not shared with unauthorized third parties except where required by law.',
                        'When the policy is updated, a new version is published and the latest update date is shown on the page.',
                    ]),
                ],
            ],
        ];

        if (isset($documentTemplates[$slug][$locale])) {
            return $documentTemplates[$slug][$locale];
        }

        $paragraphs = preg_split("/\R{2,}/u", trim($content)) ?: [];
        $translated = array_map(
            fn (string $paragraph) => $this->translateFreeform($paragraph, $locale),
            $paragraphs
        );

        return [
            'title' => $this->translateFreeform($title, $locale),
            'content' => implode("\n\n", $translated),
        ];
    }

    private function translateFreeform(string $text, string $locale): string
    {
        $text = $this->normalizeSourceText($text);

        $maps = [
            'ru' => [
                "Foydalanish shartlari" => 'Условия использования',
                "Maxfiylik siyosati" => 'Политика конфиденциальности',
                "Ushbu tizimdan foydalanish orqali" => 'Используя эту систему',
                "siz" => 'вы',
                "platformadan" => 'платформой',
                "qonuniy" => 'законных',
                "halol" => 'добросовестных',
                "maqsadlarda" => 'целях',
                "foydalanishga rozilik bildirasiz" => 'соглашаетесь пользоваться',
                "Akkaunt ma'lumotlarini himoya qilish" => 'Защита данных учетной записи',
                "foydalanuvchi zimmasida" => 'остается обязанностью пользователя',
                "Ruxsatsiz foydalanish aniqlansa" => 'Если обнаружено несанкционированное использование',
                "kirish vaqtincha cheklanishi mumkin" => 'доступ может быть временно ограничен',
                "Xizmat funksiyalari" => 'Функции сервиса',
                "vaqt o'tishi bilan" => 'со временем',
                "yangilanadi" => 'обновляются',
                "Muhim o'zgarishlar" => 'Важные изменения',
                "yangi versiya sifatida e'lon qilinadi" => 'публикуются как новая версия',
                "Ro'yxatdan o'tishda yuborilgan" => 'Информация, отправленная при регистрации',
                "ism" => 'имя',
                "telefon" => 'телефон',
                "username" => 'имя пользователя',
                "obyekt nomi" => 'название объекта',
                "ma'lumotlar" => 'данные',
                "tizimga ulanish jarayonini tashkil qilish uchun ishlatiladi" => 'используются для организации процесса подключения к системе',
                "faqat" => 'только',
                "xizmat ko'rsatish" => 'оказания сервиса',
                "xavfsizlik nazorati" => 'контроля безопасности',
                "aloqa" => 'связи',
                "uchun qayta ishlanadi" => 'обрабатываются для',
                "Ular" => 'Они',
                "ruxsatsiz uchinchi tomonga berilmaydi" => 'не передаются неуполномоченным третьим лицам',
                "qonun talab qilgan holatlar bundan mustasno" => 'кроме случаев, предусмотренных законом',
                "Siyosat yangilanganda" => 'При обновлении политики',
                "oxirgi yangilangan sana sahifada ko'rsatiladi" => 'дата последнего обновления отображается на странице',
            ],
            'en' => [
                "Foydalanish shartlari" => 'Terms of Use',
                "Maxfiylik siyosati" => 'Privacy Policy',
                "Ushbu tizimdan foydalanish orqali" => 'By using this system',
                "siz" => 'you',
                "platformadan" => 'the platform',
                "qonuniy" => 'lawful',
                "halol" => 'legitimate',
                "maqsadlarda" => 'purposes',
                "foydalanishga rozilik bildirasiz" => 'agree to use',
                "Akkaunt ma'lumotlarini himoya qilish" => 'Protecting account data',
                "foydalanuvchi zimmasida" => 'remains the responsibility of the user',
                "Ruxsatsiz foydalanish aniqlansa" => 'If unauthorized use is detected',
                "kirish vaqtincha cheklanishi mumkin" => 'access may be temporarily restricted',
                "Xizmat funksiyalari" => 'Service features',
                "vaqt o'tishi bilan" => 'over time',
                "yangilanadi" => 'are updated',
                "Muhim o'zgarishlar" => 'Important changes',
                "yangi versiya sifatida e'lon qilinadi" => 'are published as a new version',
                "Ro'yxatdan o'tishda yuborilgan" => 'Information submitted during registration',
                "ism" => 'name',
                "telefon" => 'phone number',
                "username" => 'username',
                "obyekt nomi" => 'venue name',
                "ma'lumotlar" => 'data',
                "tizimga ulanish jarayonini tashkil qilish uchun ishlatiladi" => 'is used to organize the system connection process',
                "faqat" => 'only',
                "xizmat ko'rsatish" => 'service delivery',
                "xavfsizlik nazorati" => 'security monitoring',
                "aloqa" => 'communication',
                "uchun qayta ishlanadi" => 'is processed for',
                "Ular" => 'It',
                "ruxsatsiz uchinchi tomonga berilmaydi" => 'is not shared with unauthorized third parties',
                "qonun talab qilgan holatlar bundan mustasno" => 'except where required by law',
                "Siyosat yangilanganda" => 'When the policy is updated',
                "oxirgi yangilangan sana sahifada ko'rsatiladi" => 'the latest update date is shown on the page',
            ],
        ];

        foreach ($maps[$locale] ?? [] as $from => $to) {
            $text = preg_replace('/'.preg_quote($from, '/').'/u', $to, $text) ?? $text;
        }

        return $text;
    }

    private function normalizeSourceText(string $text): string
    {
        $text = str_replace(
            ["\r\n", "\r"],
            "\n",
            $text
        );

        $text = str_replace(
            ['вЂ', 'вЂ™', 'â€™', 'â€˜', '’', '‘', '`', '´'],
            "'",
            $text
        );

        return trim($text);
    }

    private function latinUzbekToCyrillic(string $text): string
    {
        $normalized = $this->normalizeSourceText($text);

        $normalized = str_replace(
            ["O'", "o'", "G'", "g'"],
            ['Ў', 'ў', 'Ғ', 'ғ'],
            $normalized
        );

        $pairs = [
            'Sh' => 'Ш', 'SH' => 'Ш', 'sh' => 'ш',
            'Ch' => 'Ч', 'CH' => 'Ч', 'ch' => 'ч',
            'Yo' => 'Ё', 'YO' => 'Ё', 'yo' => 'ё',
            'Yu' => 'Ю', 'YU' => 'Ю', 'yu' => 'ю',
            'Ya' => 'Я', 'YA' => 'Я', 'ya' => 'я',
            'Ts' => 'Ц', 'TS' => 'Ц', 'ts' => 'ц',
        ];

        $normalized = strtr($normalized, $pairs);

        $chars = [
            'A' => 'А', 'a' => 'а', 'B' => 'Б', 'b' => 'б', 'D' => 'Д', 'd' => 'д',
            'E' => 'Е', 'e' => 'е', 'F' => 'Ф', 'f' => 'ф', 'G' => 'Г', 'g' => 'г',
            'H' => 'Ҳ', 'h' => 'ҳ', 'I' => 'И', 'i' => 'и', 'J' => 'Ж', 'j' => 'ж',
            'K' => 'К', 'k' => 'к', 'L' => 'Л', 'l' => 'л', 'M' => 'М', 'm' => 'м',
            'N' => 'Н', 'n' => 'н', 'O' => 'О', 'o' => 'о', 'P' => 'П', 'p' => 'п',
            'Q' => 'Қ', 'q' => 'қ', 'R' => 'Р', 'r' => 'р', 'S' => 'С', 's' => 'с',
            'T' => 'Т', 't' => 'т', 'U' => 'У', 'u' => 'у', 'V' => 'В', 'v' => 'в',
            'X' => 'Х', 'x' => 'х', 'Y' => 'Й', 'y' => 'й', 'Z' => 'З', 'z' => 'з',
            "'" => 'ъ',
        ];

        return strtr($normalized, $chars);
    }
}
