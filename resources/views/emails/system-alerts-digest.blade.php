<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tizim eslatmalari</title>
</head>
<body style="margin:0;padding:24px;background:#f8fafc;font-family:Arial,sans-serif;color:#0f172a;">
    <div style="max-width:760px;margin:0 auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:24px;overflow:hidden;">
        <div style="padding:28px 28px 20px;background:linear-gradient(135deg,#0f172a 0%,#1e293b 100%);color:#ffffff;">
            <div style="display:inline-block;padding:6px 12px;border-radius:999px;background:rgba(255,255,255,0.12);font-size:12px;letter-spacing:0.08em;text-transform:uppercase;">
                Tizim eslatmalari
            </div>
            <h1 style="margin:16px 0 8px;font-size:28px;line-height:1.2;">{{ $restaurantName }}</h1>
            <p style="margin:0;font-size:14px;line-height:1.6;color:#cbd5e1;">
                Kalendar bo'yicha yaqin bronlar va ombordagi kam qolgan mahsulotlar haqidagi kundalik eslatma.
            </p>
        </div>

        <div style="padding:28px;">
            <div style="margin-bottom:24px;padding:18px 20px;border-radius:18px;background:#f8fafc;border:1px solid #e2e8f0;">
                <p style="margin:0 0 8px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Qisqacha</p>
                <p style="margin:0;font-size:15px;line-height:1.7;color:#334155;">
                    Yaqin {{ $days }} kun ichidagi bronlar: <strong>{{ $upcomingBookings->count() }}</strong><br>
                    Kam qolgan mahsulotlar: <strong>{{ $lowStockProducts->count() }}</strong>
                </p>
            </div>

            @if($upcomingBookings->isNotEmpty())
                <h2 style="margin:0 0 12px;font-size:20px;color:#0f172a;">Yaqin bronlar</h2>
                <div style="margin-bottom:28px;overflow:hidden;border:1px solid #e2e8f0;border-radius:18px;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#64748b;">Bron</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#64748b;">Sana</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#64748b;">Vaqt</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#64748b;">Zal / mijoz</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#64748b;">Holat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upcomingBookings as $booking)
                                <tr>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">
                                        <strong>{{ $booking->booking_number ?: 'BRN' }}</strong><br>
                                        <span style="color:#64748b;">{{ $booking->event_type }}</span>
                                    </td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">
                                        {{ optional($booking->event_date)->format('d.m.Y') }}<br>
                                        <span style="color:#64748b;">
                                            @if($booking->days_left <= 0)
                                                Bugun
                                            @elseif($booking->days_left === 1)
                                                Ertaga
                                            @else
                                                {{ $booking->days_left }} kundan keyin
                                            @endif
                                        </span>
                                    </td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">
                                        {{ $booking->start_time ?: '--:--' }} - {{ $booking->end_time ?: '--:--' }}
                                    </td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">
                                        <strong>{{ $booking->hall_name }}</strong><br>
                                        <span style="color:#64748b;">{{ $booking->client_name }}</span>
                                    </td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">
                                        {{ $booking->status }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($lowStockProducts->isNotEmpty())
                <h2 style="margin:0 0 12px;font-size:20px;color:#0f172a;">Kam qolgan mahsulotlar</h2>
                <div style="overflow:hidden;border:1px solid #e2e8f0;border-radius:18px;">
                    <table style="width:100%;border-collapse:collapse;">
                        <thead style="background:#fff7ed;">
                            <tr>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#9a3412;">Mahsulot</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#9a3412;">SKU</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#9a3412;">Joriy qoldiq</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#9a3412;">Minimal limit</th>
                                <th style="padding:14px;text-align:left;font-size:12px;color:#9a3412;">To'ldirish</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lowStockProducts as $product)
                                <tr>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">
                                        <strong>{{ $product->name }}</strong><br>
                                        <span style="color:#64748b;">{{ $product->unit }}</span>
                                    </td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">{{ $product->sku ?: '-' }}</td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">{{ $product->current_stock }} {{ $product->unit }}</td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">{{ $product->minimum_stock }} {{ $product->unit }}</td>
                                    <td style="padding:14px;border-top:1px solid #e2e8f0;font-size:14px;vertical-align:top;">{{ $product->restock_amount }} {{ $product->unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
