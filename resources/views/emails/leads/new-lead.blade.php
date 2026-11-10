<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lead Baru</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1a1a1a; margin: 0; padding: 0; background: #f4f4f5;">
    <div style="max-width: 560px; margin: 0 auto; padding: 24px;">
        <div style="background: #4d7c0f; color: #ffffff; padding: 16px 24px; border-radius: 8px 8px 0 0;">
            <h1 style="margin: 0; font-size: 18px;">Ada Lead Baru Masuk</h1>
        </div>
        <div style="background: #ffffff; padding: 24px; border-radius: 0 0 8px 8px; border: 1px solid #e5e7eb; border-top: none;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; width: 140px; vertical-align: top;">Nama</td>
                    <td style="padding: 6px 0;">{{ $lead->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">WhatsApp</td>
                    <td style="padding: 6px 0;">{{ $lead->whatsapp }}</td>
                </tr>
                @if ($lead->contact_position)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Kontak</td>
                    <td style="padding: 6px 0;">{{ $lead->contact_position }}</td>
                </tr>
                @endif
                @if ($lead->email)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Email</td>
                    <td style="padding: 6px 0;">{{ $lead->email }}</td>
                </tr>
                @endif
                @if ($lead->address)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Alamat</td>
                    <td style="padding: 6px 0;">{{ $lead->address }}</td>
                </tr>
                @endif
                @if ($lead->service_interested)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Layanan Diminati</td>
                    <td style="padding: 6px 0;">{{ $lead->service_interested }}</td>
                </tr>
                @endif
                @if ($lead->budget_estimate)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Estimasi Budget</td>
                    <td style="padding: 6px 0;">{{ $lead->budget_estimate }}</td>
                </tr>
                @endif
                @if ($lead->message)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Pesan</td>
                    <td style="padding: 6px 0;">{{ $lead->message }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Sumber</td>
                    <td style="padding: 6px 0;">{{ $lead->source }}</td>
                </tr>
                @if ($lead->utm_source || $lead->referrer || $lead->landing_page)
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Asal Kunjungan</td>
                    <td style="padding: 6px 0;">{{ collect([$lead->utm_source, $lead->utm_medium, $lead->utm_campaign, $lead->referrer, $lead->landing_page])->filter()->join(" / ") }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 6px 0; color: #6b7280; vertical-align: top;">Waktu Submit</td>
                    <td style="padding: 6px 0;">{{ $lead->created_at->format("d M Y H:i") }}</td>
                </tr>
            </table>
        </div>
        <p style="font-size: 12px; color: #9ca3af; text-align: center; margin-top: 16px;">
            Notifikasi otomatis dari form kontak/konsultasi website.
        </p>
    </div>
</body>
</html>
