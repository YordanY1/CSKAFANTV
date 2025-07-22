<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #fef2f2; padding: 30px; font-family: sans-serif;">
    <tr>
        <td style="max-width: 600px; margin: 0 auto; background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);">

            <h2 style="color: #b91c1c; margin-bottom: 10px;">📩 Ново съобщение от {{ $name }}</h2>

            <p style="margin-bottom: 10px;"><strong>Имейл за връзка:</strong> <a href="mailto:{{ $email }}" style="color: #ef4444;">{{ $email }}</a></p>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

            <p style="white-space: pre-wrap; font-size: 15px; color: #1e1e1e;">{{ $messageContent }}</p>

            <hr style="margin: 20px 0; border: none; border-top: 1px solid #eee;">

            <p style="font-size: 13px; color: #777;">Имейл изпратен от контактната форма на <strong>CSKA FAN TV</strong>.</p>
        </td>
    </tr>
</table>
