<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification email ulandi</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:24px;overflow:hidden;">
        <div style="padding:28px;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#ffffff;">
            <div style="display:inline-block;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,0.12);font-size:12px;letter-spacing:0.08em;text-transform:uppercase;">
                Notification email
            </div>
            <h1 style="margin:16px 0 8px;font-size:28px;line-height:1.2;">{{ $restaurantName }}</h1>
            <p style="margin:0;font-size:14px;line-height:1.6;color:#cbd5e1;">
                Ushbu email tizim xabarlari uchun muvaffaqiyatli ulandi.
            </p>
        </div>

        <div style="padding:28px;">
            <div style="padding:18px 20px;border-radius:18px;background:#f8fafc;border:1px solid #e2e8f0;">
                <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Ulangan manzil</p>
                <p style="margin:0;font-size:16px;line-height:1.7;color:#0f172a;"><strong>{{ $notificationEmail }}</strong></p>
                @if($contactPhone)
                    <p style="margin:8px 0 0;font-size:14px;line-height:1.7;color:#475569;">Aloqa raqami: {{ $contactPhone }}</p>
                @endif
            </div>

            <div style="margin-top:24px;padding:18px 20px;border-radius:18px;background:#fff7ed;border:1px solid #fed7aa;">
                <p style="margin:0;font-size:15px;line-height:1.8;color:#9a3412;">
                    Endi bu manzilga yaqin bronlar va kam qolgan mahsulotlar bo'yicha avtomatik eslatmalar yuboriladi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
